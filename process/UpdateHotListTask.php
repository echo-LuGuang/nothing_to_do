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

        // 每秒钟执行一次
        new Crontab('0 */1 * * * *', function () {
            //发布事件
            Event::emit('weibo', null);


            //发布事件
            Event::emit('zhihu', null);
        });


        // 每小时的第一分钟执行
        new Crontab('0 1 * * * *', function () {
            dump('每小时的第一分钟执行');
            echo date('Y-m-d H:i:s') . "\n";
        });


        // 每天的7点50执行，注意这里省略了秒位
        new Crontab('50 7 * * *', function () {
            echo date('Y-m-d H:i:s') . "\n";
        });

    }
}