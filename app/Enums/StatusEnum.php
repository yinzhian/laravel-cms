<?php

namespace App\Enums;

/**
 * Class StatusEnum
 * @package App\Enums
 */
final class StatusEnum extends CommonEnum
{
    const DISABLE = [ "title" => "禁用", "key" => 0 ];
    const ENABLE  = [ "title" => "启用", "key" => 1 ];

}
