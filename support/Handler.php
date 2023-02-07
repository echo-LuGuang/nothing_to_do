<?php

namespace support;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends exception\Handler
{
    /**
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception): Response
    {
        $msg = '服务错误';
        if ($this->debug) {
            $msg = $exception->getMessage();
        }

        return json(['msg' => $msg, 'code' => 500]);
    }
}