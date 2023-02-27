<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use support\Redis;

class JueJin
{
    //掘金
    const url = 'https://api.juejin.cn/recommend_api/v1/article/recommend_all_feed';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'juejin';

    /**
     * 更新掘金热榜
     * @return void
     */
    public function update()
    {
        try {
            $client = new Client();

            $res = json_decode($client->post(self::url, [
                'json' => json_decode('{"id_type":2,"client_type":2608,"sort_type":3,"cursor":"0","limit":20}', true),
                'headers' => [
                    //模拟浏览器请求
                    'user-agent' => self::userAgent,
                ]
            ])->getBody()->getContents(), true);


            $host = 'https://juejin.cn/post/';

            $insertData = [];

            foreach ($res['data'] as $item) {
                if ($item['item_type'] == 14) continue;

                $title = $item['item_info']['article_info']['title'];

                $url = $host . $item['item_info']['article_id'];
                $subtitle = $item['item_info']['article_info']['view_count'];

                $insertData[] = [
                    'title' => $title,
                    'url' => $url,
                    'subtitle' => $subtitle,
                    'type' => self::type
                ];
            }


            dump($insertData);
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