<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Generator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Notes: 成功
     * User: 一颗地梨子
     * DateTime: 2022/2/14 10:20
     * @param array|object|NULL $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function ok ( $data = "", String $message = "success" )
    {
        return response()->json(returnInfo( $message, $data ));
    }

    /**
     * Notes: 错误返回值
     * User: 一颗地梨子
     * DateTime: 2022/2/14 14:25
     * @param string $message
     * @param int $code
     * @param int $http_code
     * @return \Illuminate\Http\JsonResponse|void
     */
    protected function fail ( String $message = "服务异常、请稍后重试", int $code = 100, int $http_code = 200 )
    {
        return fail( $code, $message, $http_code );
    }
}
