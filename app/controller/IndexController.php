<?php

namespace app\controller;

use Webman\Event\Event;

class IndexController extends BaseController
{
    public function index()
    {
        //发布事件
        Event::emit('zhihu', null);
        return $this->success();
    }
}