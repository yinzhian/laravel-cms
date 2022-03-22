<?php

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

if ( ! function_exists( 'returnInfo' ) ) {
    /**
     * Notes: 请求成功返回格式
     * User: 一颗地梨子
     * DateTime: 2021/9/6 17:24
     * @param string $message
     * @param array|object|NULL $data
     * @param int $code
     * @return array
     */
    function returnInfo( string $message = 'ok', $data = "", int $code = 200 ) : array
    {
        return array(
            'code'    => (int) str_pad($code, 6, 0, STR_PAD_RIGHT),
            'result'  => $data ? $data : NULL,
            'message' => $message,
        );
    }
}

if ( ! function_exists("fail") ) {

    /**
     * Notes: 异常返回值
     * User: 一颗地梨子
     * DateTime: 2022/2/14 11:49
     * @param int $diy_code     自定义状态码
     * @param string $message   返回提示信息
     * @param int $http_code    http 状态码
     * @return \Illuminate\Http\JsonResponse
     */
    function fail( int $diy_code = 400, string $message = '服务异常', int $http_code = 200 )
    {
        //$message = __("message.{$message}");  // 同下
        $message = trans("message.{$message}") ?? "服务异常";

        return response()->json( returnInfo( $message, "", (int) ($http_code.$diy_code) ), $http_code);
    }
}

if ( ! function_exists( "isPhone" ) ) {
    /**
     * Notes: 验证手机号
     * User: 一颗地梨子
     * Email: yza8023@gmail.com
     * DateTime: 2021/1/4 10:42 上午
     * @param String $phone
     * @return bool
     */
    function isPhone( string $phone ) : bool
    {
        if ( preg_match( "/^1[3456789]{1}\d{9}$/", $phone ) ) {
            return true;
        } else {
            return false;
        }
    }
}

if ( ! function_exists( 'isJson' ) ) {
    /**
     * Notes: 验证是否为正确的JSON
     * User: 一颗地梨子
     * Email: yza8023@gmail.com
     * DateTime: 2021/1/5 4:58 下午
     * @param String $string
     * @return array
     * @throws Exception
     */
    function isJson( string $string ) : array
    {
        try {
            $result = json_decode( $string, true );
        } catch ( \Exception $exception ) {
            info( " isJson 解析错误： ", [$string] );
            throw new \Exception("数据格式错误");
        }

        return $result;
    }
}

if ( ! function_exists( 'phoneSecrecy' ) ) {
    /**
     * Notes: 手机号加密
     * User: 一颗地梨子
     * Email: yza8023@gmail.com
     * DateTime: 2021/2/7 4:49 下午
     * @param String $phone
     * @return String
     */
    function phoneSecrecy( string $phone ) : string
    {
        return substr_replace( $phone, '****', 3, 4 );
    }
}

if ( ! function_exists( "arrayRemoveEmpty" ) ) {
    /**
     * Notes: 自定义数字排空、保留：0
     * User: 一颗地梨子
     * DateTime: 2022/1/19 17:36
     * @param array $param
     * @return array
     */
    function arrayRemoveEmpty ( array $param ): array {
        foreach ( $param AS $key => $value ) {
            if (
                blank( $value ) ||
                in_array( $key, [ "created_at", "updated_at" ] )
                //||
                //( Str::of($key)->is('*_name') && !in_array( $key, [ "nick_name", "display_name", "guard_name"] ) )
            )
                unset( $param[$key] );
        }
        return $param;
    }
}


if ( ! function_exists( "diyLog" ) ) {

    /**
     * Notes: 记录日志
     * User: 一颗地梨子
     * DateTime: 2022/1/19 16:03
     * @param $title
     * @param $result
     * @param $dir
     */
    function diyLog ($title, $result, $dir) {
        ( new Logger( env( "APP_ENV", "local" ) ) )
            ->pushHandler( new RotatingFileHandler( storage_path( "logs/{$dir}/.log" ) ) )
            ->info( $title, $result && is_array( $result ) ? $result : [] );
    }

}
