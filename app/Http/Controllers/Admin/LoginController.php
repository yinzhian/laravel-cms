<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Notes: 登录
     * User: 一颗地梨子
     * DateTime: 2022/2/15 15:03
     * @param AdminRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function login( AdminRequest $request )
    {
        if ( ! $token = auth( 'admin' )->attempt( $request->all() ) ) {
            return $this->fail("账号密码不匹配");
        }

        $data = array(
            "token"      => "bearer " . $token,
            'expires_in' => auth( 'admin' )->factory()->getTTL() * 60
        );

        return $this->ok( $data );
    }

    /**
     * Notes: 退出登录
     * User: 一颗地梨子
     * DateTime: 2022/2/15 15:04
     * @return \Illuminate\Http\JsonResponse
     */
    public function quit ()
    {
        auth( 'admin' )->logout();

        return $this->ok();
    }
}
