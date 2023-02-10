<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use QL\QueryList;
use support\Redis;

class Tieba
{
    //贴吧地址
    const url = 'https://tieba.baidu.com/hottopic/browse/topicList?res_type=1';

    const userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1';

    const type = 'baidutieba';


    /**
     * 更新贴吧
     * @return void
     */
    public function update()
    {
        try {
            $client = new Client([
                //允许重定向
                'allow_redirects' => true,
            ]);

            $html = $client->get(self::url, [
                'headers' => [
                    //模拟浏览器请求
                    'user-agent' => self::userAgent,
                    'cookie' => 'XFI=6e1d1ce0-a85d-11ed-99eb-11b538647ab4; XFCS=D6620BE57597589FF8D940D2F3F516567360D391F71F8F478004C368E8F76DE8; XFT=iyu7lzyKnNFDWJxaWeR2wGxa6a8RMk/dJWOtML8Naq8=; BAIDUID=5C2E750DE79339FB8080AC93D265832F:FG=1; BAIDUID_BFESS=5C2E750DE79339FB8080AC93D265832F:FG=1; Hm_lvt_98b9d8c2fd6608d564bf2ac2ae642948=1675935291; Hm_lpvt_98b9d8c2fd6608d564bf2ac2ae642948=1675935475; USER_JUMP=-1; BAIDU_WISE_UID=wapp_1675935475718_958'
                ]
            ])->getBody()->getContents();

            $ql = new QueryList();

            //解析html
            $div = $ql->html($html)->find('.main>.topic-top-list');

            $insertData = $div->find('.topic-top-item')->map(function ($row) {
                $title = $row->find('.topic-name a')->text();
                $subtitle = $row->find('.topic-name .topic-num')->text();
                $url = $row->find('.topic-name a')->attr('href');

                //去除“实时讨论”
                $subtitle = str_replace('实时讨论', '', $subtitle);
                return [
                    'title' => $title,
                    'url' => $url,
                    'subtitle' => $subtitle,
                    'type' => self::type
                ];

            })->filter()->values()->toArray();

            if (empty($insertData)) return;

            //开启事务
            Db::beginTransaction();

            //删除原来的旧数据
            Article::query()->where('type', self::type)->delete();
            //添加新的数据
            Article::query()->insert($insertData);

            //redis缓存
            Redis::set(self::type, json_encode($insertData, JSON_UNESCAPED_UNICODE));

            //redis缓存 记录更新时间
            $time = date('Y-m-d H:i:s');
            Redis::set(self::type . 'time', $time);

            //提交事务
            Db::commit();
            dump(date('Y-m-d H:i:s') . '更新' . self::type . '成功');
        } catch (GuzzleException|\Exception $exception) {
            //回滚事务
            Db::rollBack();
            dump('更新' . self::type . '异常：' . $exception->getMessage());
            dump($exception);
        }
    }
}