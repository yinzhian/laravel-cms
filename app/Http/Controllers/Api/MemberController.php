<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Notes: 用户信息
     * User: 一颗地梨子
     * DateTime: 2022/3/3 15:50
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->ok( Member::getMember() );
    }

    /**
     * Notes: 更新用户信息
     * User: 一颗地梨子
     * DateTime: 2022/3/3 10:28 下午
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request )
    {
        // 获取参数
        $params = $request->all();

        if ( ! Member::where( "id", Member::getMemberId() )->update( array_filter($params) ) ) {
            return $this->fail();
        }

        return $this->ok();
    }
}
