<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use QL\QueryList;
use support\Redis;

class BilibiliHot
{
    //b站地址
    const url = 'https://api.bilibili.com/x/web-interface/popular?ps=50&pn=1';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'bilibiliHot';


    /**
     * 更新b站
     * @return void
     */
    public function update()
    {
        try {
            $client = new Client([
                //允许重定向
                'allow_redirects' => true,
            ]);

            $data = json_decode($client->get(self::url, [
                'headers' => [
                    //模拟浏览器请求
                    'user-agent' => self::userAgent,
                    'cookie' => 'buvid3=A8FDD5D4-9F94-AD91-C888-920C1C627D4F18454infoc; b_nut=1675924818; _uuid=D5236B58-27D3-DC4D-4F49-23C6FD5A7AC318778infoc; buvid_fp=6f89fc5eb24e4cf8feced2765e74cb80; buvid4=4D7B9B0C-02FE-0939-D83A-6010D35EBBB420301-023020914-KxUcNPBJu7UmRVVupymsBA%3D%3D'
                ]
            ])->getBody()->getContents(), true);


            if ($data['code'] !== 0) throw new \Exception('更新b站热门失败');


            $insertData = [];
            foreach ($data['data']['list'] as $value) {
                $insertData[] = [
                    'title' => $value['title'],
                    'url' => $value['short_link'],
                    'subtitle' => $value['stat']['view'],
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