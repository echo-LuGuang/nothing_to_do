<?php


return [
    //知乎热榜
    'zhihu' => [
        [app\event\ZhiHu::class, 'update'],
    ],

    //微博热榜
    'weibo' => [
        [app\event\WeiBo::class, 'update'],
    ],
];