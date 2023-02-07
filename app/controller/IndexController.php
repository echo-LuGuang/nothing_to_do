<?php

namespace app\controller;

use app\event\WeiBo;
use Webman\Event\Event;

class IndexController extends BaseController
{
    public function index()
    {
        //发布事件
        Event::emit(WeiBo::type, null);
        return $this->success();
    }
}