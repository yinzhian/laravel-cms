<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use Illuminate\Support\Str;

class RoleRequest extends CommonRequest
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

                /// TODO 添加
                return [
                    'name'   => 'required|between:2,32|unique:roles,name',
                    'title'  => 'required|between:2,32|unique:roles,title',
                    'status' => 'required|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                    'sort'   => 'between:1,255',
                ];

            case "PUT":

                if ( Str::contains( $path, 'admin.role.update' ) ) {

                    // 路由中的参数
                    $role_id = $this->route( 'id' );

                    /// TODO 更新
                    return [
                        'name'   => "required|between:2,32|unique:roles,name,{$role_id}",
                        'title'  => "required|between:2,32|unique:roles,title,{$role_id}",
                        'status' => 'required|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                        'sort'   => 'between:1,255',
                    ];
                } else if ( Str::contains( $path, 'admin.role.empower' ) ) {

                    /// TODO 授权
                    return [
                        'permission_ids' => "bail|required|array"
                    ];
                } else if ( Str::contains( $path, 'admin.role.restore' ) ) {

                    /// TODO 还原
                    return [
                        'ids' => "bail|required|array"
                    ];
                }

            case "DELETE":
                /// TODO 删除
                return [
                    'ids' => "bail|required|array"
                ];
        }
    }
}
