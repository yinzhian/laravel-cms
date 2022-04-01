<?php

namespace App\Enums;

/**
 *  标签类型
 * Class LabelTypeEnum
 * @package App\Enums
 */
final class LabelTypeEnum extends CommonEnum
{
    const MEMBER  = [ "title" => "用户", "key" => 1 ];
    const ARTICLE = [ "title" => "文章", "key" => 2 ];
}
