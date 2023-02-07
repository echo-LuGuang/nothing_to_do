<?php


namespace app\controller;


use support\Response;

/**
 * 基本控制器
 * Class BaseController
 * @package App\controller
 */
class BaseController
{
    protected function success($data = [], $msg = '操作成功', int $code = 200, array $header = []): Response
    {
        //让前端可以获取请求头的token
        $header['Access-Control-Expose-Headers'] = 'Authorization';

        $result = [
            'code' => $code,
            'data' => $data,
            'msg' => $msg
        ];

        if (is_string($data)) {
            $result['msg'] = $data;
            $result['data'] = [];
        }

        return json($result)->withHeaders($header);
    }


    protected function error($msg = '操作失败', $data = [], int $code = 400, array $header = []): Response
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];

        return json($result)->withHeaders($header);
    }
}
