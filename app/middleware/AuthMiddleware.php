<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $token = $request->header('token');

        if (empty($token)) {
            return json(['code' => 401, 'msg' => 'token不存在']);
        }

        return $handler($request);
    }
}