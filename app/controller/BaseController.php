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
    /**
     * 响应成功的封装
     * @param void $data 提示语/响应数据
     * @param string $msg 提示语
     * @param int $code 响应code
     * @param array $header 响应头
     * @return Response
     */
    protected function success($data = [], string $msg = '操作成功', int $code = 200, array $header = []): Response
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


    /**
     * 响应失败的封装
     * @param string $msg 提示语
     * @param array $data 返回数据
     * @param int $code code
     * @param array $header 响应头
     * @return Response
     */
    protected function error(string $msg = '操作失败', array $data = [], int $code = 400, array $header = []): Response
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];

        return json($result)->withHeaders($header);
    }
}
