<?php

namespace App\Enums;

/**
 *  是否是菜单
 * Class StatusEnum
 * @package App\Enums
 */
final class IsMenuEnum extends CommonEnum
{
    const MENU   = [ "title" => "菜单", "key" => 1 ];
    const BUTTON = [ "title" => "按钮", "key" => 2 ];

}
