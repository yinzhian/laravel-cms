<?php

namespace App\Enums;

/**
 *  是否删除
 * Class AdTypeEnum
 * @package App\Enums
 */
final class DeletedEnum extends CommonEnum
{
    const NO  = [ "title" => "否", "key" => 0 ];
    const YES = [ "title" => "已删除", "key" => 1 ];
}
