<?php

namespace App\Enums;

/**
 * Notes: 用户身份
 * User: 一颗地梨子
 * DateTime: 2022-02-26 15:23
 */
final class IdentityEnum extends CommonEnum
{
    const PLAIN    = [ "title" => "普通", "key" => 1 ];
    const AGENT    = [ "title" => "代理", "key" => 2 ];
}
