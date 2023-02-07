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
        // 每小时的第一分钟执行
        new Crontab('0 1 * * * *', function () {
            dump(date('Y-m-d H:i:s') . '更新知乎热榜开始');
            //发布事件
            Event::emit('zhihu', null);
        });
    }
}