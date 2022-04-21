<?php

namespace App\Http\Requests;

use App\Enums\IsMenuEnum;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;

class PermissionRequest extends CommonRequest
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
                    'parent_id' => 'bail|integer',
                    'name'      => 'bail|required|between:2,191|unique:permissions,name',
                    'title'     => 'bail|exclude_unless:parent_id,true|between:2,32|unique:permissions,title',
                    'route'     => 'bail|exclude_unless:parent_id,true|between:2,32|unique:permissions,route',
                    'icon'      => 'bail|exclude_unless:parent_id,false|max:255',
                    'is_menu'   => 'bail|required|integer|in:' . join( ",", IsMenuEnum::getAllKey() ),
                    'status'    => 'bail|required|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                    'sort'      => 'bail|between:1,255',
                ];

            case "PUT":

                // 路由中的参数
                $permission_id = $this->route( 'id' );

                /// TODO 更新
                return [
                    'parent_id' => 'bail|integer',
                    'name'      => 'bail|required|between:2,191|unique:permissions,name,' . $permission_id,
                    'title'     => 'bail|exclude_unless:parent_id,true|between:2,32|unique:permissions,title,' . $permission_id,
                    'route'     => 'bail|exclude_unless:parent_id,true|between:2,32|unique:permissions,route,' . $permission_id,
                    'icon'      => 'bail|exclude_unless:parent_id,false|max:255',
                    'is_menu'   => 'bail|required|integer|in:' . join( ",", IsMenuEnum::getAllKey() ),
                    'status'    => 'bail|required|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                    'sort'      => 'bail|between:1,255',
                ];
        }
    }
}
