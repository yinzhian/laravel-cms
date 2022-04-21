<?php

namespace App\Http\Requests;

use App\Enums\AdPositionEnum;
use App\Enums\AdTypeEnum;
use App\Enums\JumpTypeEnum;
use App\Enums\LabelTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;

class LabelRequest extends CommonRequest
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
                    'title' => 'bail|required|between:2,32|unique:labels,title',
                    'color' => 'bail|exclude_unless:color,true|between:2,32',
                    'icon'  => 'bail|exclude_unless:color,false|required|between:2,255',
                    'type'  => 'bail|required|integer|in:' . join( ",", LabelTypeEnum::getAllKey() ),
                    'sort'  => 'bail|integer|max:255',
                ];

            case "PUT":

                // 路由中的参数
                $id = $this->route( 'id' );

                /// TODO 更新
                return [
                    'title' => "bail|required|between:2,32|unique:labels,title,{$id}",
                    'color' => 'bail|exclude_unless:color,true|between:2,32',
                    'icon'  => 'bail|exclude_unless:color,false|required|between:2,255',
                    'type'  => 'bail|required|integer|in:' . join( ",", LabelTypeEnum::getAllKey() ),
                    'sort'  => 'bail|integer|max:255',
                ];
        }
    }
}
