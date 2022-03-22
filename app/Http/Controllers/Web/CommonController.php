<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    /**
     * Notes: 获取枚举
     * User: 一颗地梨子
     * DateTime: 2022/2/19 17:30
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function enum ( Request $request )
    {
        // 枚举名称
        $name = $request->get("name", "");

        // 过滤
        if ( !$name ) return $this->fail();

        // 实例化类
        $enum = '\\App\\Enums\\'.$name;

        return $this->ok( $enum::getValues() );
    }
}
