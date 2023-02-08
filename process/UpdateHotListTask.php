<?php

namespace process;

use Webman\Event\Event;
use Workerman\Crontab\Crontab;

/**
 * 更新热搜列表任务
 */
class UpdateHotListTask
{
    public function onWorkerStart()
    {
        // 每10分钟执行一次
        new Crontab('0 */10 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新头条开始');
            Event::emit('toutiao', null);

            dump(date('Y-m-d H:i:s') . '更新天涯开始');
            Event::emit('tianya', null);

            dump(date('Y-m-d H:i:s') . '更新豆瓣开始');
            Event::emit('douban', null);

            dump(date('Y-m-d H:i:s') . '更新虎扑开始');
            Event::emit('hupu', null);

            dump(date('Y-m-d H:i:s') . '更新微博开始');
            Event::emit('weibo', null);
        });


        // 每小时的第一分钟执行
        new Crontab('0 1 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新知乎热榜开始');
            //发布事件
            Event::emit('zhihu', null);
        });
    }
}