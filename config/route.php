<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use app\middleware\AuthMiddleware;
use Webman\Route;

Route::group('/api/v1/', function () {


    //需要登录的接口
    Route::group(function () {

    })->middleware(AuthMiddleware::class);
});

//自定义404
Route::fallback(fn() => json(['code' => 404, 'msg' => 'not found']));

//关闭默认路由
Route::disableDefaultRoute();