<?php

namespace App\Enums;

/**
 *  用户类型
 * Class UserTypeEnum
 * @package App\Enums
 */
final class UserTypeEnum extends CommonEnum
{
    const ADMIN  = [ "title" => "管理员", "key" => 1 ];
    const MEMBER = [ "title" => "用户",   "key" => 2 ];
}
