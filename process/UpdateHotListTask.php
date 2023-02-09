<?php

namespace process;

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
            dump(date('Y-m-d H:i:s') . '更新头条开始');
            Event::emit('toutiao', null);

            dump(date('Y-m-d H:i:s') . '更新微博开始');
            Event::emit('weibo', null);
        });

        // 每10分钟执行一次
        new Crontab('0 */10 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新天涯开始');
            Event::emit('tianya', null);

            dump(date('Y-m-d H:i:s') . '更新豆瓣开始');
            Event::emit('douban', null);
        });

        // 每11分钟执行一次
        new Crontab('0 */11 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新虎扑开始');
            Event::emit('hupu', null);

            dump(date('Y-m-d H:i:s') . '更新知乎热榜开始');
            Event::emit('zhihu', null);
        });

        // 每12分钟执行一次
        new Crontab('0 */12 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新百度开始');
            Event::emit('baidu', null);
        });
    }
}