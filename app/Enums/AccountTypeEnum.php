<?php

namespace App\Enums;

/**
 * Notes: 账户类型
 * User: 一颗地梨子
 * DateTime: 2022-02-25 22:45
 */
final class AccountTypeEnum extends CommonEnum
{
    const WECHAT = [ "title" => "微信", "key" => 1 ];
    const ALIPAY = [ "title" => "支付宝", "key" => 2 ];
    const BANK   = [ "title" => "银行卡", "key" => 3 ];
}
