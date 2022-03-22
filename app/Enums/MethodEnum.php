<?php

namespace App\Enums;

/**
 *  请求方式
 * Class MethodEnum
 * @package App\Enums
 */
final class MethodEnum extends CommonEnum
{
    const GET    = [ "title" => "GET", "key" => "GET" ];
    const POST   = [ "title" => "POST", "key" => "POST" ];
    const PUT    = [ "title" => "PUT", "key" => "PUT" ];
    const DELETE = [ "title" => "DELETE", "key" => "DELETE" ];
}
