<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\IndexController;
use \App\Http\Controllers\Api\LoginController;
use \App\Http\Controllers\Api\MemberController;

/*
|--------------------------------------------------------------------------
| Member Routes - 用户路由
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 登录
Route::get( "code", [ LoginController::class, "code" ] )->name( "code" );
Route::post( "login", [ LoginController::class, "login" ] )->name( "login" );
Route::post( "demoLogin", [ LoginController::class, "demoLogin" ] )->name( "demoLogin" );

// 首页
Route::prefix( "/" )->group( function () {

} );

/// TODO 必须登录
Route::middleware( [ 'member.refresh' ] )->group( function () {

    // 用户相关
    Route::prefix( "/member" )->name( "member." )->group( function () {

        // 退出
        Route::put( "quit", [ MemberController::class, "quit" ] )->name( "quit" );

        // 个人信息
        Route::get( "/", [ MemberController::class, "index" ] )->name( "index" );
        Route::put( "/update", [ MemberController::class, "update" ] )->name( "update" );

    } );
} );

/// TODO WeChat 路由
Route::prefix( "/wechat" )->group( function () {

    Route::get( "index", [ IndexController::class, "index" ] )->name( "index" );

} );
