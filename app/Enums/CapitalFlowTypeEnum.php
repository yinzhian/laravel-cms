<?php

namespace App\Enums;

/**
 * Notes: 资金变动类型
 * User: 一颗地梨子
 * DateTime: 2022-02-26 16:16
 */
final class CapitalFlowTypeEnum extends CommonEnum
{
    const NEW_MEMBER  = [ "title" => "新用户注册", "key" => 2 ];
    const PULL_MEMBER = [ "title" => "拉新", "key" => 4 ];
    const COMMISSION = [ "title" => "佣金", "key" => 6 ];
}
