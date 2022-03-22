<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
            //
        ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash
        = [
            'current_password',
            'password',
            'password_confirmation',
        ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable( function ( Throwable $e ) {
            //
        } );
    }

    /**
     * Notes: 异常处理
     * User: 一颗地梨子
     * DateTime: 2022/2/14 11:08
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render( $request, Throwable $exception )
    {
        if ( $request->is( 'api/*' ) || $request->is( 'admin/*' ) ) {

            // 获取错误信息
            $error = $this->convertExceptionToResponse( $exception );

            Log::error( "***** 统一捕捉错误 START ************************" );
            Log::error(
                "统一捕捉错误 === ",
                [
                    "http_code"       => $error->getStatusCode(),
                    "diy_status_code" => $exception->getCode(),
                    "message"         => $exception->getMessage(),
                    "file"            => $exception->getFile(),
                    "line"            => $exception->getLine()
                ]
            );
            Log::error( "***** 统一捕捉错误 END **************************" );

            return fail( (int) $exception->getCode(), (string) $exception->getMessage(), $error->getStatusCode() );

        } else {
            return parent::render( $request, $exception );
        }
    }
}
