<?php

namespace App\Enums;

/**
 * Notes: 性别
 * User: 一颗地梨子
 * DateTime: 2022-02-25 21:59
 */
final class SexEnum extends CommonEnum
{
    const SECRET = [ "title" => "保密", "key" => 0 ];
    const MAN    = [ "title" => "男", "key" => 1 ];
    const WOMAN  = [ "title" => "女", "key" => 2 ];
}
