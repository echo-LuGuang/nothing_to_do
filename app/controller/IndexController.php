<?php

namespace app\controller;

use app\event\TianYa;
use Webman\Event\Event;

class IndexController extends BaseController
{
    public function index()
    {
        //发布事件
        Event::emit(TianYa::type, null);
        return $this->success();
    }
}