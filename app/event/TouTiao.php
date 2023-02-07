<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;

class TouTiao
{
    //今日头条的热榜接口可以直接请求
    const url = 'https://www.toutiao.com/hot-event/hot-board/?origin=toutiao_pc';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'toutiao';

    /**
     * 更新今日头条热榜
     * @return void
     */
    public function update()
    {
        try {
            $host = 'https://www.toutiao.com/trending/';
            $client = new Client();

            $res = json_decode($client->get(self::url, [
                'headers' => [
                    //模拟浏览器请求
                    'user-agent' => self::userAgent,
                    'cookie' => 'local_city_cache=%E5%8C%97%E4%BA%AC; csrftoken=d629c4cf65498641303da0bcec3d8656; _tea_utm_cache_24=undefined; s_v_web_id=verify_ldu2d0wn_t5XTbyBa_EMlu_4asm_BVuY_0TicyI0dsJfX; MONITOR_WEB_ID=985975fe-3138-423c-92d0-9ddd78b1d25b; tt_webid=7197349673618261563; _ga=GA1.1.1787281075.1675763575; ttwid=1%7C077FkCSz3ab_1z5TLjQvvTZiyNyhyUGCc8CpyfJDmNs%7C1675763695%7C5fbe97d87d9d0976e1442080b58ac4783dccf8780e1a6f3544b44d26353664c0; tt_scid=sp9Zq1T3-s68fKpxxc.HNm8qcIfv0tiZuaU9CdsV0MiWZVXCjWHWpn8E47xUoKmA3163; _ga_QEHZPBE5HH=GS1.1.1675763575.1.1.1675763698.0.0.0'
                ]
            ])->getBody()->getContents(), true);

            $insertData = [];

            foreach ($res['data'] as $item) {
                $title = $item['Title'];
                $url = $host . $item['ClusterIdStr'];
                $subtitle = $item['HotValue'];
                $insertData[] = [
                    'title' => $title,
                    'url' => $url,
                    'subtitle' => $subtitle,
                    'type' => self::type
                ];
            }

            //开启事务
            Db::beginTransaction();

            //删除原来的旧数据
            Article::query()->where('type', self::type)->delete();
            //添加新的数据
            Article::query()->insert($insertData);

            //提交事务
            Db::commit();
            dump(date('Y-m-d H:i:s') . '更新今日头条热榜成功');
        } catch (GuzzleException|\Exception $exception) {
            //回滚事务
            Db::rollBack();
            dump('更新今日头条热榜异常：' . $exception->getMessage());
            dump($exception);
        }
    }
}