<?php

namespace App\Http\Requests;

use App\Enums\ConfigTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;

class ConfigRequest extends CommonRequest
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
                    'parent_id' => 'bail|required|integer',
                    'name'      => 'bail|required|between:2,32|unique:configs,name',
                    'key'       => 'bail|exclude_unless:parent_id,false|required|between:2,32|unique:configs,title',
                    'value'     => 'bail|exclude_unless:key,false|required|between:2,32',
                    'type'      => 'bail|exclude_unless:type,true|in:' . join( ",", ConfigTypeEnum::getAllKey() ),
                    'status'    => 'bail|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                    'sort'      => 'bail|between:1,255',
                ];

            case "PUT":

                // 路由中的参数
                $config_id = $this->route( 'id' );

                /// TODO 更新
                return [
                    'parent_id' => 'bail|required|integer',
                    'name'      => 'bail|required|between:2,32|unique:configs,name,' . $config_id,
                    'key'       => 'bail|exclude_unless:parent_id,false|required|between:2,32|unique:configs,title,' . $config_id,
                    'value'     => 'bail|exclude_unless:key,false|required|between:2,32',
                    'type'      => 'bail|exclude_unless:type,true|in:' . join( ",", ConfigTypeEnum::getAllKey() ),
                    'status'    => 'bail|integer|in:' . join( ",", StatusEnum::getAllKey() ),
                    'sort'      => 'bail|between:1,255',
                ];

        }
    }
}
