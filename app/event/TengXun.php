<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use support\Redis;

class TengXun
{
    //腾讯新闻
    const url = 'https://r.inews.qq.com/gw/event/hot_ranking_list?ids_hash=&offset=0&page_size=20';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'tengxun';

    /**
     * 更新掘金热榜
     * @return void
     */
    public function update()
    {
        try {
            $client = new Client();

            $res = json_decode($client->get(self::url, [
                'headers' => [
                    //模拟浏览器请求
                    'user-agent' => self::userAgent,
                ]
            ])->getBody()->getContents(), true);


            $host = 'https://new.qq.com/rain/a/';

            $insertData = [];

            foreach ($res['idlist'][0]['newslist'] as $key => $item) {
                if ($key === 0) continue;

                $title = $item['title'];
                $url = $host . $item['hotEvent']['id'];
                if ($item['articletype'] === '0') {
                    $subtitle = $item['readCount'];
                } elseif ($item['articletype'] == '4') {
                    $subtitle = $item['video_channel']['video']['playcount'];
                }

                $insertData[] = [
                    'title' => $title,
                    'url' => $url,
                    'subtitle' => $subtitle,
                    'type' => self::type
                ];
            }

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