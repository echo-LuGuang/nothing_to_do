<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use QL\QueryList;
use support\Redis;

class DouBan
{
    //豆瓣话题地址
    const url = 'https://www.douban.com/gallery/';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'douban';


    /**
     * 更新豆瓣话题
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
                    //cookie
                    'cookie' => 'bid=YUl46w92vGM; _pk_ref.100001.8cb4=%5B%22%22%2C%22%22%2C1675760501%2C%22https%3A%2F%2Fmomoyu.cc%2F%22%5D; _pk_ses.100001.8cb4=*; ap_v=0,6.0; ll="108090"; __utma=30149280.688369536.1675760505.1675760505.1675760505.1; __utmc=30149280; __utmz=30149280.1675760505.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utmt=1; _pk_id.100001.8cb4=c20a119c98541b78.1675760501.1.1675760544.1675760501.; __utmb=30149280.4.10.1675760505'
                ]
            ])->getBody()->getContents();

            $ql = new QueryList();

            //解析html
            $ul = $ql->html($html)->find('.trend');

            $insertData = $ul->find('li')->map(function ($row) {
                $title = $row->find('a')->text();
                $subtitle = $row->find('span')->text();
                $url = $row->find('a')->attr('href');

                //去除“次浏览”
                $subtitle = str_replace('次浏览', '', $subtitle);

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
            dump('更新豆瓣话题异常：' . $exception->getMessage());
            dump($exception);
        }
    }
}