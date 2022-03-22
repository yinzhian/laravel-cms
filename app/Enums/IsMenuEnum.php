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

    /**
     * Notes: 获取所有的KEY
     * User: 一颗地梨子
     * DateTime: 2022/2/17 11:49
     * @return array
     */
    static function getAllKey () : array {

        $keys = [];

        $enumArray = self::getValues();

        foreach ( $enumArray AS $enum ) {

            $keys[] = $enum["key"];
        }

        return $keys;
    }
}
