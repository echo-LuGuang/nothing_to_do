<?php

use app\middleware\AuthMiddleware;
use Webman\Route;

Route::group('/api/v1/', function () {


    Route::get('index', [\app\controller\IndexController::class, 'index']);

    //需要登录的接口
    Route::group(function () {

    })->middleware(AuthMiddleware::class);
});

//自定义404
Route::fallback(fn() => json(['code' => 404, 'msg' => 'not found']));

//关闭默认路由
Route::disableDefaultRoute();