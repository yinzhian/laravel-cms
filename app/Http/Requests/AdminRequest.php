<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use Illuminate\Support\Str;

class AdminRequest extends CommonRequest
{
    /**
     * Notes: 校验
     * User: 一颗地梨子
     * DateTime: 2022/2/14 18:19
     * @return array
     */
    public function rules()
    {
        // 路由
        $path = $this->route()->getName();

        // 请求方式
        $method = $this->method();

        switch ( strtoupper( $method ) ) {

            case "POST":

                if ( Str::contains( $path, 'admin.login' ) ) {

                    /// TODO 登录
                    return [
                        'username'  => 'required|between:2,32|exists:admins,username',
                        'password'  => 'required|between:6,32',
                    ];
                } else if ( Str::contains( $path, 'admin.admin.create' ) ) {

                    /// TODO 添加
                    return [
                        'username' => 'required|between:2,32|unique:admins,username',
                        'phone'    => 'required|regex:/^1[3456789]{1}\d{9}$/|unique:admins,phone',
                        'password' => 'required|between:6,32',
                        'status'   => 'required|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                    ];
                }

            case "PUT":
                if ( Str::contains( $path, 'admin.admin.update' ) ) {

                    // 路由中的参数
                    $admin_id = $this->route( 'id' );

                    /// TODO 更新
                    return [
                        'username' => "required|between:2,32|unique:admins,username,{$admin_id}",
                        'phone'    => "required|regex:/^1[3456789]{1}\d{9}$/|unique:admins,phone,{$admin_id}",
                        'password' => 'between:6,32',
                        'status'   => 'required|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                    ];
                } else if ( Str::contains( $path, 'admin.admin.addRole' ) ) {

                    /// TODO 修改角色
                    return [
                        'role_ids' => "bail|required|array"
                    ];
                } else if ( Str::contains( $path, 'admin.admin.empower' ) ) {

                    /// TODO 授权
                    return [
                        'permission_ids' => "bail|required|array"
                    ];
                }
        }
    }
}
