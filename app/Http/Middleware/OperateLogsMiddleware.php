<?php

namespace App\Http\Middleware;

use App\Enums\ClientEnum;
use App\Models\OperateLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OperateLogsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle( Request $request, Closure $next )
    {
        try {

            // 路由
            $route = $request->route()->getName();

            // 路径
            $path = $request->path();

            if ( Str::contains( $request->route()->getName(), 'admin.operate_log' ) ) {
                return $next( $request );
            }

            // 类型[1.小程序；2.后台]
            if ( $request->is( 'api/*' ) ) $client_type = ClientEnum::MEMBER["key"];
            if ( $request->is( 'admin/*' ) ) $client_type = ClientEnum::ADMIN["key"];

            // 请求方式
            $method = $request->method();

            // IP
            $ip = $request->getClientIp();

            // 参数
            $param = $request->all();

            $param = $param ?? [];

            $param = json_encode( $param, JSON_UNESCAPED_UNICODE );

            // token
            $token = $request->header( 'Authorization', '' );

            if ( $token ) {
                if ( ClientEnum::MEMBER["key"] == $client_type ) {
                    $m_id = auth( "member" )->id();
                } else if ( ClientEnum::ADMIN["key"] == $client_type ) {
                    $m_id = auth( "admin" )->id();
                }
            }

            $data = array(
                "route"       => $route,
                "path"        => $path,
                "client_type" => $client_type,
                "method"      => $method,
                "ip"          => $ip,
                "param"       => $param,
                "m_id"        => $m_id ?? 0,
            );

            OperateLog::insert( $data );

        } catch ( \Exception $exception ) {
            Log::error( "操作日志报错：", [
                "错误码"  => $exception->getCode(),
                "错误信息" => $exception->getMessage(),
                "错误文件" => $exception->getFile(),
                "错误行号" => $exception->getLine(),
            ] );
        }
        return $next( $request );
    }
}
