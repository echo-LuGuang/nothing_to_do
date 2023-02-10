<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use QL\QueryList;
use support\Redis;

class BaiDu
{
    //百度地址
    const url = 'https://top.baidu.com/board?tab=realtime';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'baidu';


    /**
     * 更新百度
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
                ]
            ])->getBody()->getContents();

            $ql = new QueryList();

            //解析html
            $div = $ql->html($html)->find('.container:eq(3)');


            $insertData = $div->find('div>div>.horizontal_1eKyQ')->map(function ($row) {
                $title = $row->find('.c-single-text-ellipsis')->text();
                $subtitle = $row->find('.hot-index_1Bl1a')->text();
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
            dump(date('Y-m-d H:i:s') . '更新百度成功');
        } catch (GuzzleException|\Exception $exception) {
            //回滚事务
            Db::rollBack();
            dump('更新' . self::type . '异常：' . $exception->getMessage());
            dump($exception);
        }
    }
}