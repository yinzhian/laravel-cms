<?php

namespace App\Enums;

/**
 *  操作终端
 * Class ClientEnum
 * @package App\Enums
 */
final class ClientEnum extends CommonEnum
{
    const ADMIN  = [ "title" => "后台",  "key" => 1 ];
    const MEMBER = [ "title" => "客户端", "key" => 2 ];
}
