<?php

namespace process;

use app\event\BaiDu;
use app\event\Bilibili;
use app\event\BilibiliHot;
use app\event\DouBan;
use app\event\HuPu;
use app\event\Itzhijia;
use app\event\TianYa;
use app\event\Tieba;
use app\event\TouTiao;
use app\event\WeiBo;
use app\event\ZhiHu;
use Webman\Event\Event;
use Workerman\Crontab\Crontab;

/**
 * 更新热搜列表任务
 */
class UpdateHotListTask
{
    //错开时间执行，否则固定时间段接口会响应很慢
    public function onWorkerStart()
    {
        // 每9分钟执行一次
        new Crontab('0 */9 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新' . TouTiao::type . '开始');
            Event::emit(TouTiao::type, null);

            dump(date('Y-m-d H:i:s') . '更新' . WeiBo::type . '开始');
            Event::emit(WeiBo::type, null);
        });

        // 每10分钟执行一次
        new Crontab('0 */10 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新' . TianYa::type . '开始');
            Event::emit(TianYa::type, null);

            dump(date('Y-m-d H:i:s') . '更新' . DouBan::type . '开始');
            Event::emit(DouBan::type, null);
        });

        // 每11分钟执行一次
        new Crontab('0 */11 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新' . HuPu::type . '开始');
            Event::emit(HuPu::type, null);

            dump(date('Y-m-d H:i:s') . '更新' . ZhiHu::type . '开始');
            Event::emit(ZhiHu::type, null);
        });

        // 每12分钟执行一次
        new Crontab('0 */12 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新' . BaiDu::type . '开始');
            Event::emit(BaiDu::type, null);

            dump(date('Y-m-d H:i:s') . '更新' . Bilibili::type . '开始');
            Event::emit(Bilibili::type, null);
        });

        // 每13分钟执行一次
        new Crontab('0 */13 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新' . Itzhijia::type . '开始');
            Event::emit(Itzhijia::type, null);

            dump(date('Y-m-d H:i:s') . '更新' . Tieba::type . '开始');
            Event::emit(Tieba::type, null);
        });

        // 每14分钟执行一次
        new Crontab('0 */13 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新' . BilibiliHot::type . '开始');
            Event::emit(BilibiliHot::type, null);
        });
    }
}