<?php

namespace App\Http\Requests;

use App\Enums\LoginTypeEnum;
use App\Enums\SexEnum;
use App\Enums\SourceEnum;

class LoginRequest extends CommonRequest
{
    /**
     * Notes: 校验
     * User: 一颗地梨子
     * DateTime: 2022/2/25 18:00
     * @return string[]
     */
    public function rules()
    {
        // 路由
        //$path = $this->route()->getName();

        // 请求方式
        $method = $this->method();

        switch ( strtoupper( $method ) ) {

            case "GET":
                /// TODO 小程序 code 登录
                return [
                    'code' => 'bail|string',
                ];

            case "POST":

                /// TODO 小程序 登录
                return [
                    'login_type'  => 'bail|required|integer|in:' . join( ",", LoginTypeEnum::getAllKey() ),
                    'openid'      => 'bail|exclude_unless:login_type,' . LoginTypeEnum::WECHAT_MINI_APP["key"] . '|required|string',
                    'iv'          => 'bail|exclude_unless:login_type,' . LoginTypeEnum::WECHAT_MINI_APP["key"] . '|required|string',
                    'data'        => 'bail|exclude_unless:login_type,' . LoginTypeEnum::WECHAT_MINI_APP["key"] . '|required|string',
                    'invite_code' => 'bail|exclude_unless:invite_code,true|between:4,10',
                    'phone'       => 'bail|regex:/^1[3456789]{1}\d{9}$/',
                    'password'    => 'bail|nullable|between:6,20',
                    'nick'        => 'bail|nullable|between:2,32',
                    'avatar'      => 'bail|nullable|max:255',
                    'sex'         => 'bail|nullable|integer|in:' . join( ",", SexEnum::getAllKey() ),
                    'source'      => 'bail|nullable|integer|in:' . join( ",", SourceEnum::getAllKey() ),
                ];
        }
    }
}
