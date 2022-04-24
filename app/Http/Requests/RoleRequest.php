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
                    'name'   => 'required|between:2,32|unique:App\Models\Role,name',
                    'title'  => 'required|between:2,32|unique:App\Models\Role,title',
                    'status' => 'required|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                    'sort'   => 'between:1,255',
                ];

            case "PUT":

                if ( Str::contains( $path, 'update' ) ) {

                    // 路由中的参数
                    $role_id = $this->route( 'id' );

                    /// TODO 更新
                    return [
                        'name'   => "required|between:2,32|unique:App\Models\Role,name,{$role_id}",
                        'title'  => "required|between:2,32|unique:App\Models\Role,title,{$role_id}",
                        'status' => 'required|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                        'sort'   => 'between:1,255',
                    ];
                } else if ( Str::contains( $path, 'empower' ) ) {

                    /// TODO 授权
                    return [
                        'permission_ids' => "bail|required|array"
                    ];
                }
        }
    }
}
