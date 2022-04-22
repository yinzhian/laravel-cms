<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperateLog;
use Illuminate\Http\Request;

class OperateLogController extends Controller
{
    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:05
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request )
    {
        return $this->ok( OperateLog::list( $request ) );
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:05
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail( $id )
    {
        return $this->ok( OperateLog::detail( $id ) );
    }
}
