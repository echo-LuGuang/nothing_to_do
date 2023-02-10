<?php


use app\event\BaiDu;
use app\event\Bilibili;
use app\event\BilibiliHot;
use app\event\DouBan;
use app\event\HuPu;
use app\event\Itzhijia;
use app\event\Maimai;
use app\event\TianYa;
use app\event\Tieba;
use app\event\TouTiao;
use app\event\V2ex;
use app\event\WeiBo;
use app\event\ZhiHu;

return [
    //知乎热榜
    ZhiHu::type => [
        [ZhiHu::class, 'update'],
    ],

    //微博热榜
    WeiBo::type => [
        [WeiBo::class, 'update'],
    ],

    //豆瓣话题
    DouBan::type => [
        [DouBan::class, 'update'],
    ],

    //天涯热帖
    TianYa::type => [
        [TianYa::class, 'update'],
    ],

    //虎扑步行街
    HuPu::type => [
        [HuPu::class, 'update'],
    ],

    //今日头条
    TouTiao::type => [
        [TouTiao::class, 'update'],
    ],

    //百度
    BaiDu::type => [
        [BaiDu::class, 'update'],
    ],

    //b站
    Bilibili::type => [
        [Bilibili::class, 'update']
    ],

    //it之家
    Itzhijia::type => [
        [Itzhijia::class, 'update']
    ],

    //贴吧
    Tieba::type => [
        [Tieba::class, 'update']
    ],

    //b站综合热门
    BilibiliHot::type => [
        [BilibiliHot::class, 'update']
    ],

    //脉脉
    Maimai::type => [
        [Maimai::class, 'update']
    ],

    //v2ex
    V2ex::type => [
        [V2ex::class, 'update']
    ],
];