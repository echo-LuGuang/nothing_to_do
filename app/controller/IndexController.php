<?php

namespace app\controller;

use app\event\TouTiao;
use Webman\Event\Event;

class IndexController extends BaseController
{
    public function index()
    {
        //发布事件
        Event::emit(TouTiao::type, null);
        return $this->success();
    }
}