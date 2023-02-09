<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use QL\QueryList;
use support\Redis;

class Itzhijia
{
    //it之家地址
    const url = 'https://m.ithome.com/rankm/';

    const userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1';

    const type = 'itzhijia';


    /**
     * 更新it之家
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
                    'cookie' => 'Hm_lvt_cfebe79b2c367c4b89b285f412bf9867=1675663993,1675763496,1675926272; Hm_lvt_f2d5cbe611513efcf95b7f62b934c619=1675663993,1675926272; Hm_lpvt_f2d5cbe611513efcf95b7f62b934c619=1675926272; Hm_lpvt_cfebe79b2c367c4b89b285f412bf9867=1675926281'
                ]
            ])->getBody()->getContents();

            $ql = new QueryList();

            //解析html
            $div = $ql->html($html)->find('.rank-box:eq(0)');

            $insertData = $div->find('>div')->map(function ($row) {
                $title = $row->find('a .plc-title')->text();
                $subtitle = $row->find('a .review-num')->text();
                $url = $row->find('a')->attr('href');

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
            dump(date('Y-m-d H:i:s') . '更新it之家成功');
        } catch (GuzzleException|\Exception $exception) {
            //回滚事务
            Db::rollBack();
            dump('更新it之家异常：' . $exception->getMessage());
            dump($exception);
        }
    }
}