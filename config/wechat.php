<?php

$public = [
    // 下面为可选项
    // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
    'response_type' => 'array',

    'log' => [
        'level' => 'debug',
        'file' => __DIR__.'/wechat.log',
    ],
];

/***
 *  DOTO 微信配置文件
 */
return [

    // 公众号
    "officialAccount" => [
        'app_id' => env("WECHAT_OFFICIALACCOUNT_APP_ID"), // AppID
        'secret' => env("WECHAT_OFFICIALACCOUNT_APP_ID"), // AppSecret

        $public
    ],

    // 小程序
    "miniApp"         => [
        'app_id' => env("WECHAT_MINIAPP_APP_ID"), // AppID
        'secret' => env("WECHAT_MINIAPP_SECRET"), // AppSecret

        $public
    ],

];
