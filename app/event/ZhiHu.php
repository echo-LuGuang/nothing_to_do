<?php

namespace app\event;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ZhiHu
{
    //知乎的热榜接口可以直接请求
    const url = 'https://www.zhihu.com/api/v4/creators/rank/hot?domain=0&period=hour&limit=50';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    /**
     * 更新知乎热榜
     * @return void
     */
    public function update($data)
    {
        try {
            $client = new Client();

            $res = json_decode($client->get(self::url, [
                'headers' => [
                    //模拟浏览器请求
                    'user-agent' => self::userAgent
                ]
            ])->getBody()->getContents(), true);

            dump($res);
        } catch (GuzzleException|\Exception $exception) {
            dump('更新知乎热榜异常：' . $exception->getMessage());
            dump($exception);
        }

    }
}