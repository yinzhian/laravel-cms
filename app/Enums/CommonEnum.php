<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 *  公共 枚举文件
 * Class CommonEnum
 * @package App\Enums
 */
class CommonEnum extends Enum
{
    /**
     * Notes: 根据 Title 获取 Key
     * User: 一颗地梨子
     * DateTime: 2022/2/14 15:09
     * @param String $title
     * @return int
     */
    static function getKeyByTitle( String $title ) : int
    {
        // 返回值
        $key = 0;
        // 转数组
        $enumArray = self::getValues();
        // 解析
        if ( $enumArray && is_array($enumArray) ) {
            foreach ( $enumArray AS $enum ) {
                if ( $title == $enum["title"] )
                {
                    $key = $enum["key"];
                    break;
                }
            }
        }
        return $key;
    }

    /**
     * Notes: 获取 Title
     * User: 一颗地梨子
     * DateTime: 2022/2/14 15:10
     * @param $key
     * @param $field
     * @return string
     */
    static function getTitle( $key, $field = "title" ) : string
    {
        // 返回值
        $title = 0;
        // 转数组
        $enumArray = self::getValues();
        // 解析
        if ( $enumArray && is_array($enumArray) ) {
            foreach ( $enumArray AS $enum ) {
                if ( $key == $enum["key"] )
                {
                    $title = $enum[$field];
                    break;
                }
            }
        }
        return $title;
    }

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
