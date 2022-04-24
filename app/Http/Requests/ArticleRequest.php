<?php

namespace App\Http\Requests;

use App\Enums\AdPositionEnum;
use App\Enums\AdTypeEnum;
use App\Enums\JumpTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;

class ArticleRequest extends CommonRequest
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
                    'article_category_id' => 'bail|required|integer',
                    'title'               => 'bail|required|between:2,191|unique:App\Models\Article,title',
                    'cover'               => 'bail|nullable|between:2,255',
                    'describe'            => 'bail|nullable|between:2,255',
                    'content'             => 'bail|required',
                    'sort'                => 'bail|integer|max:255',
                    'label'               => 'bail|required|array',
                ];

            case "PUT":

                // 路由中的参数
                $id = $this->route( 'id' );

                /// TODO 更新
                return [
                    'article_category_id' => 'bail|required|integer',
                    'title'               => "bail|required|between:2,191|unique:App\Models\Article,title,{$id}",
                    'cover'               => 'bail|nullable|between:2,255',
                    'describe'            => 'bail|nullable|between:2,255',
                    'content'             => 'bail|required',
                    'sort'                => 'bail|integer|max:255',
                    'label'               => 'bail|required|array',
                ];
        }
    }
}
