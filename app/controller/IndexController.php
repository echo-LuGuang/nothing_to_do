<?php

namespace app\controller;

use app\event\BaiDu;
use app\event\Bilibili;
use app\event\DouBan;
use app\event\HuPu;
use app\event\Itzhijia;
use app\event\TianYa;
use app\event\TouTiao;
use app\event\WeiBo;
use app\event\ZhiHu;
use app\model\Article;
use support\Redis;
use support\Response;
use Webman\Event\Event;

class IndexController extends BaseController
{
    private array $cate = [
        [
            'type' => ZhiHu::type,
            'name' => '知乎热榜',
            'color' => '#0177D7'
        ],
        [
            'type' => WeiBo::type,
            'name' => '微博热搜',
            'color' => '#E30E24'
        ],
        [
            'type' => BaiDu::type,
            'name' => '百度热搜',
            'color' => '#2d34d6'
        ],
        [
            'type' => Bilibili::type,
            'name' => 'B站',
            'color' => '#4ba5db'
        ],
        [
            'type' => TouTiao::type,
            'name' => '今日头条',
            'color' => '#FF2F49'
        ],
        [
            'type' => DouBan::type,
            'name' => '豆瓣热话',
            'color' => '#2e963d'
        ],
        [
            'type' => TianYa::type,
            'name' => '天涯热帖',
            'color' => '#4A86D0'
        ],
        [
            'type' => HuPu::type,
            'name' => '虎扑步行街',
            'color' => '#C50100'
        ],
    ];

    public function test(): Response
    {
        //发布事件
        Event::emit(Itzhijia::type, null);
        return $this->success();
    }


    //全部列表
    public function index(): Response
    {
        $data = [];
        foreach ($this->cate as $item) {
            $item['time'] = parseTimeLine(Redis::get($item['type'] . 'time'));
            $item['list'] = $this->{$item['type']}();
            $data[] = $item;
        }

        return $this->success($data);
    }

    //详情
    public function detail($name): Response
    {
        //判断参数合法性
        $types = array_column($this->cate, 'type');

        if (!in_array($name, $types)) {
            return $this->error('分类不存在');
        }

        $data = Redis::get($name);

        if (!empty($data)) {
            $data = json_decode($data, true);
        } else {
            $data = Article::query()->where('type', $name)->orderBy('id')->get();
        }

        return $this->success($data);
    }
}