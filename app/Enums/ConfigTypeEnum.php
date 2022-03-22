<?php

namespace App\Enums;

/**
 *  配置类型
 * Class ConfigTypeEnum
 * @package App\Enums
 */
final class ConfigTypeEnum extends CommonEnum
{
    const INPUT    = [ "title" => "表单", "key" => "input" ];
    const TEXTAREA = [ "title" => "文本", "key" => "textarea" ];
    const RADIO    = [ "title" => "单选", "key" => "radio" ];
    const CHECKBOX = [ "title" => "多选", "key" => "checkbox" ];
    const FILE     = [ "title" => "文件", "key" => "file" ];
    const ARRAY    = [ "title" => "数组", "key" => "array" ];
}
