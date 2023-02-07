<?php

namespace app\event;

use app\model\Article;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Db;

class TianYa
{
    //天涯的热榜接口可以直接请求
    const url = 'https://bbs.tianya.cn/api?method=bbs.ice.getHotArticleList&params.pageSize=40&params.pageNum=1';

    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';

    const type = 'tianya';

    /**
     * 更新天涯热榜
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
                    'cookie' => 'Hm_lvt_bc5755e0609123f78d0e816bf7dee255=1675761632; __u_a=v2.3.0; __asc=adf3a4a91862b2d455f62803d62; __auc=adf3a4a91862b2d455f62803d62; ASL=19395,0000r,7ce1d6ce; ADVC=3b88f698cd75ae; ADVS=3b88f698cd75ae; __cid=CN; __guid=1968353217; __guid2=1968353217; time=ct=1675761634.796; Hm_lpvt_bc5755e0609123f78d0e816bf7dee255=1675761635; __ptime=1675761635556'
                ]
            ])->getBody()->getContents(), true);

            if ($res['success'] != 1 || $res['code'] != 1) {
                dump('更新天涯热榜接口响应异常：' . json_encode($res, JSON_UNESCAPED_UNICODE));
                return;
            }

            $insertData = [];

            foreach ($res['data']['rows'] as $item) {
                $title = $item['title'];
                $url = $item['url'];
                $subtitle = $item['item_name'];
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
            dump(date('Y-m-d H:i:s') . '更新天涯热榜成功');
        } catch (GuzzleException|\Exception $exception) {
            //回滚事务
            Db::rollBack();
            dump('更新天涯热榜异常：' . $exception->getMessage());
            dump($exception);
        }
    }
}