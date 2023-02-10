<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use QL\QueryList;
use support\Redis;

class V2ex
{
    const url = 'https://www.v2ex.com/';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'v2ex';


    /**
     * @return void
     */
    public function update()
    {
        try {
            $host = 'https://www.v2ex.com';

            $client = new Client([
                //允许重定向
                'allow_redirects' => true,
                //代理
                'proxy' => 'http://127.0.0.1:8118'
            ]);

            $html = $client->get(self::url, [
                'headers' => [
                    //模拟浏览器请求
                    'user-agent' => self::userAgent,
                    //cookie
                    'cookie' => 'PB3_SESSION="2|1:0|10:1675997094|11:PB3_SESSION|40:djJleDoxMTYuMjA2LjEwMS4xNjY6NzMyNzM0MDc=|6dc2d010303a0d839facb49443f07facd43b7b8cde19ab40b15f170912fb98bf"; V2EX_LANG=zhcn; __gads=ID=e3ef7380859ba2f5-22c3497a99d90069:T=1675997096:RT=1675997096:S=ALNI_MYfGJOptJM0gd0MTq-8mIOdMV2vlA; __gpi=UID=00000bbed1e70c8e:T=1675997096:RT=1675997096:S=ALNI_MYE1rMDSdQV61q7K9glftfD_tNj0w; _ga=GA1.2.432626368.1675997096; _gid=GA1.2.2100444781.1675997098; V2EX_TAB="2|1:0|10:1675997200|8:V2EX_TAB|4:aG90|a08f7d84c636110e2235418847563791e279fb2698c6b0f12c54c1f86049c57f"; V2EX_REFERRER="2|1:0|10:1675997209|13:V2EX_REFERRER|12:U2d0UGVwcGVy|f8827354cd392d0c190fe79e6744e0f707e393f7f035f443a97a9c39e5d0a820"; _gat=1'
                ]
            ])->getBody()->getContents();

            $ql = new QueryList();

            //解析html
            $table = $ql->html($html)->find('#TopicsHot');

            $insertData = $table->find('.cell table')->map(function ($row) use ($host) {
                $title = $row->find('.item_hot_topic_title a')->text();
                $subtitle = '';
                $url = $host . $row->find('.item_hot_topic_title a')->attr('href');

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
            dump('更' . self::type . '榜异常：' . $exception->getMessage());
            dump($exception);
        }
    }
}