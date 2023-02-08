<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use QL\QueryList;
use support\Redis;

class WeiBo
{
    //微博热榜地址
    const url = 'https://s.weibo.com/top/summary?cate=realtimehot';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'weibo';


    /**
     * 更新微博热榜
     * @return void
     */
    public function update()
    {
        try {
            $host = 'https://s.weibo.com';

            $client = new Client([
                //允许重定向
                'allow_redirects' => true,
            ]);

            $html = $client->get(self::url, [
                'headers' => [
                    //模拟浏览器请求
                    'user-agent' => self::userAgent,
                    //cookie
                    'cookie' => 'SUB=_2AkMUvoH0f8NxqwFRmP4SxWrnb451yQzEieKi4nAvJRMxHRl-yT9jqh1YtRB6Pz6vG53IuuNcVmtx7SWagTBey1bXoRF8; SUBP=0033WrSXqPxfM72-Ws9jqgMF55529P9D9WWir9y7UWdySP8OAUxCzbNI'
                ]
            ])->getBody()->getContents();

            $ql = new QueryList();

            //解析html
            $table = $ql->html($html)->find('table:eq(0)');

            $insertData = $table->find('tr:gt(1)')->map(function ($row) use ($host) {
                $title = $row->find('td:eq(1)>a')->text();
                $subtitle = $row->find('td:eq(1)>span')->text();
                $url = $host . $row->find('td:eq(1)>a')->attr('href');

                return [
                    'title' => $title,
                    'url' => $url,
                    'subtitle' => $subtitle,
                    'type' => self::type
                ];
            })->filter()->values()->toArray();

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
            dump(date('Y-m-d H:i:s') . '更新微博热榜成功');
        } catch (GuzzleException|\Exception $exception) {
            //回滚事务
            Db::rollBack();
            dump('更新微博热榜异常：' . $exception->getMessage());
            dump($exception);
        }
    }
}