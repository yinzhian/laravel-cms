<?php

namespace App\Http\Requests;

use App\Enums\AdPositionEnum;
use App\Enums\AdTypeEnum;
use App\Enums\JumpTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;

class ArticleCategoryRequest extends CommonRequest
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
                    'title' => 'bail|required|between:2,32|unique:article_categories,title',
                    'sort'  => 'bail|integer|max:255',
                ];

            case "PUT":

                // 路由中的参数
                $id = $this->route( 'id' );

                /// TODO 更新
                return [
                    'title' => "bail|required|between:2,32|unique:article_categories,title,{$id}",
                    'sort'  => 'bail|integer|max:255',
                ];
        }
    }
}
