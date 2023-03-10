<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use support\Redis;

//脉脉
class Maimai
{
    const url = 'https://maimai.cn/sdk/web/content/get_list?api=feed/v5/nd1feed';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'maimai';


    /**
     * @return void
     */
    public function update()
    {
        try {

            //脉脉字数限制
            $text_num = 100;

            $client = new Client([
                //允许重定向
                'allow_redirects' => true,
            ]);

            $data = json_decode($client->get(self::url, [
                'headers' => [
                    //模拟浏览器请求
                    'user-agent' => self::userAgent
                ]
            ])->getBody()->getContents(), true);

            if (!$data['result']) throw new \Exception('更新' . self::type . '失败');

            $insertData = [];
            foreach ($data['list'] as $value) {

                if (isset($value['style1'])) {
                    $title = $value['style1']['text'];
                    if (mb_strlen($title) > $text_num) {
                        $title = mb_substr($title, 0, $text_num) . '...';
                    }
                    $url = 'https://maimai.cn/web/feed_detail?fid=' . $value['id'] . '&efid=' . $value['efid'];
                } else if (isset($value['style44'])) {
                    $title = $value['style44']['text'];
                    if (mb_strlen($title) > $text_num) {
                        $title = mb_substr($title, 0, $text_num) . '...';
                    }
                    $url = $value['style44']['share_url'];
                } else {
                    //其他的类型是广告
                    continue;
                }

                $insertData[] = [
                    'url' => $url,
                    'title' => $title,
                    'subtitle' => '',
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