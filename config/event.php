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

    //豆瓣话题
    'douban' => [
        [app\event\DouBan::class, 'update'],
    ],

    //天涯热帖
    'tianya' => [
        [app\event\TianYa::class, 'update'],
    ],
];