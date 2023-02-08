<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;
use QL\QueryList;
use support\Redis;

class HuPu
{
    //虎扑步行街地址
    const url = 'https://www.hupu.com/';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'hupu';


    /**
     * 更新虎扑步行街
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
                    'cookie' => 'sajssdk_2015_cross_new_user=1; smidV2=202302071716287ac32b465f34922e9543556d282d83fa00d063b8fe894e170; sensorsdata2015jssdkcross=%7B%22distinct_id%22%3A%221862ae8de9bfcd-061559d408b57b8-26021051-1204049-1862ae8de9ca38%22%2C%22first_id%22%3A%22%22%2C%22props%22%3A%7B%22%24latest_traffic_source_type%22%3A%22%E7%9B%B4%E6%8E%A5%E6%B5%81%E9%87%8F%22%2C%22%24latest_search_keyword%22%3A%22%E6%9C%AA%E5%8F%96%E5%88%B0%E5%80%BC_%E7%9B%B4%E6%8E%A5%E6%89%93%E5%BC%80%22%2C%22%24latest_referrer%22%3A%22%22%7D%2C%22%24device_id%22%3A%221862ae8de9bfcd-061559d408b57b8-26021051-1204049-1862ae8de9ca38%22%7D'
                ]
            ])->getBody()->getContents();

            $ql = new QueryList();

            //解析html
            $div = $ql->html($html)->find('.itemListA:eq(2)');

            $insertData = $div->find('a')->map(function ($row) {
                $title = $row->find('.hot-title')->text();
                $subtitle = "";
                $url = $row->attr('href');

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
            dump(date('Y-m-d H:i:s') . '更新虎扑步行街成功');
        } catch (GuzzleException|\Exception $exception) {
            //回滚事务
            Db::rollBack();
            dump('更新虎扑步行街异常：' . $exception->getMessage());
            dump($exception);
        }
    }
}