<?php

namespace App\Enums;

/**
 *  登录类型
 * Class LoginTypeEnum
 * @package App\Enums
 */
final class LoginTypeEnum extends CommonEnum
{
    const WECHAT_MINI_APP         = SourceEnum::WECHAT_MINI_APP;
    const WECHAT_OFFICIAL_ACCOUNT = SourceEnum::WECHAT_OFFICIAL_ACCOUNT;
    const PASSWORD                = [ "title" => "密码", "key" => 10 ];
    const SMS_CODE                = [ "title" => "短信", "key" => 12 ];
}
