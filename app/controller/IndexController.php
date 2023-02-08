<?php

namespace app\controller;

use app\event\DouBan;
use app\event\HuPu;
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
        ],
        [
            'type' => WeiBo::type,
            'name' => '微博热搜',
        ],
        [
            'type' => TouTiao::type,
            'name' => '今日头条',
        ],
        [
            'type' => DouBan::type,
            'name' => '豆瓣热话',
        ],
        [
            'type' => TianYa::type,
            'name' => '天涯热帖',
        ],
        [
            'type' => HuPu::type,
            'name' => '虎扑步行街',
        ],
    ];

    public function test(): Response
    {
        //发布事件
        Event::emit(ZhiHu::type, null);
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

        $data = $this->$name();

        return $this->success($data);
    }

    //获取知乎列表
    public function zhihu(): array
    {
        $data = Redis::get(ZhiHu::type);

        if (!empty($data)) {
            $data = json_decode($data, true);
        } else {
            $data = Article::query()->where('type', ZhiHu::type)->orderBy('id')->get();
        }

        return $data;
    }

    //获取头条列表
    public function toutiao(): array
    {
        $data = Redis::get(TouTiao::type);

        if (!empty($data)) {
            $data = json_decode($data, true);
        } else {
            $data = Article::query()->where('type', TouTiao::type)->orderBy('id')->get();
        }

        return $data;
    }

    //获取虎扑列表
    public function hupu(): array
    {
        $data = Redis::get(HuPu::type);

        if (!empty($data)) {
            $data = json_decode($data, true);
        } else {
            $data = Article::query()->where('type', HuPu::type)->orderBy('id')->get();
        }

        return $data;
    }

    //获取微博列表
    public function weibo(): array
    {
        $data = Redis::get(WeiBo::type);

        if (!empty($data)) {
            $data = json_decode($data, true);
        } else {
            $data = Article::query()->where('type', WeiBo::type)->orderBy('id')->get();
        }

        return $data;
    }

    //获取豆瓣列表
    public function douban(): array
    {
        $data = Redis::get(DouBan::type);

        if (!empty($data)) {
            $data = json_decode($data, true);
        } else {
            $data = Article::query()->where('type', DouBan::type)->orderBy('id')->get();
        }

        return $data;
    }

    //获取天涯列表
    public function tianya(): array
    {
        $data = Redis::get(TianYa::type);

        if (!empty($data)) {
            $data = json_decode($data, true);
        } else {
            $data = Article::query()->where('type', TianYa::type)->orderBy('id')->get();
        }

        return $data;
    }
}