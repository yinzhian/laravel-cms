<?php

namespace App\Enums;

/**
 *  来源
 * Class SourceEnum
 * @package App\Enums
 */
final class SourceEnum extends CommonEnum
{
    const WECHAT_MINI_APP         = [ "title" => "微信小程序", "key" => 1 ];
    const WECHAT_OFFICIAL_ACCOUNT = [ "title" => "微信公众号", "key" => 2 ];
}
