<?php

use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CommonController;
use App\Http\Controllers\Admin\LabelController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\OperateLogController;
use App\Http\Controllers\Admin\ConfigController;
use Illuminate\Support\Facades\Route;

/**
 * |--------------------------------------------------------------------------
 * | ADMIN Routes
 * |--------------------------------------------------------------------------
 * |
 * | 后台路由文件
 * |
 */

/// TODO 超级管理员角色   ->middleware(['role:'.$app_supper])
$app_supper = env( "APP_SUPER", "SuperAdmin" );

Route::prefix( "/" )->middleware( "operate.log" )->group( function () use ( $app_supper ) {

    // 登录
    Route::post( "login", [ LoginController::class, "login" ] )->name( "login" )->middleware(['throttle:login']);

    /// TODO 必须登录
    Route::middleware( [ 'admin.refresh' ] )->group( function () use ( $app_supper ) {

        // 退出
        Route::put( "quit", [ LoginController::class, "quit" ] )->name( "quit" );

        // 获取个人信息
        Route::get( "/me", [ AdminController::class, "me" ] )->name( "me" );

        // 获取七牛上传TOKEN
        Route::get( "/getQiNiuToken", [ CommonController::class, "getQiNiuToken" ] )->name( "getQiNiuToken" );

        // 管理员管理 - 仅超级管理员方可操作
        Route::prefix( "/admin" )->controller( AdminController::class )->name( "admin." )->middleware( [ "role:{$app_supper}" ] )->group( function () {

            Route::get( "/", "index" );
            Route::get( "/{id}", "detail" )->name( "detail" );
            Route::post( "/", "create" )->name( "create" );
            Route::put( "/{id}", "update" )->name( "update" );
            Route::delete( "/", "delete" )->name( "delete" );
            Route::put( "/restore", "restore" )->name( "restore" );
            Route::put( "/{id}/addRole", "addRole" )->name( "addRole" );
            Route::put( "/{id}/empower", "empower" )->name( "empower" );

        } );

        // 角色管理 - 仅超级管理员方可操作
        Route::prefix( "/role" )->controller( RoleController::class )->name( "role." )->middleware( [ "role:{$app_supper}" ] )->group( function () {

            Route::get( "/", "index" );
            Route::get( "/all", "getAll" )->name( "getAll" );
            Route::get( "/{id}", "detail" )->name( "detail" );
            Route::post( "/", "create" )->name( "create" );
            Route::put( "/{id}", "update" )->name( "update" );
            Route::delete( "/", "delete" )->name( "delete" );
            Route::put( "/restore", "restore" )->name( "restore" );
            Route::put( "/{id}/empower", "empower" )->name( "empower" );

        } );

        // 权限管理 - 仅超级管理员方可操作
        Route::prefix( "/permission" )->controller( PermissionController::class )->name( "permission." )->middleware( [ "role:{$app_supper}" ] )->group( function () {

            Route::get( "/", "index" );
            Route::get( "/all", "getAll" )->name( "getAll" );
            Route::get( "/{id}", "detail" )->name( "detail" );
            Route::post( "/", "create" )->name( "create" );
            Route::put( "/{id}", "update" )->name( "update" );
            Route::delete( "/", "delete" )->name( "delete" );
            Route::put( "/restore", "restore" )->name( "restore" );

        } );

        // 操作日志管理
        Route::prefix( "/operateLog" )->controller( OperateLogController::class )->name( "operate_log." )->group( function () {

            Route::get( "/", "index" );
            Route::get( "/{id}", "detail" )->name( "detail" );

        } );

        // 配置管理
        Route::prefix( "/config" )->controller( ConfigController::class )->name( "config." )->group( function () {

            Route::get( "/", "index" );
            Route::get( "/{id}", "detail" )->name( "detail" );
            Route::post( "/", "create" )->name( "create" );
            Route::put( "/{id}", "update" )->name( "update" );

        } );

        // 标签管理
        Route::prefix( "/label" )->controller( LabelController::class )->name( "label." )->group( function () {

            Route::get( "/", "index" );
            Route::get( "/all", "getAll" );
            Route::get( "/{id}", "detail" )->name( "detail" );
            Route::post( "/", "create" )->name( "create" );
            Route::put( "/{id}", "update" )->name( "update" );
            Route::delete( "/", "delete" )->name( "delete" );
            Route::put( "/restore", "restore" )->name( "restore" );

        } );

        // 文章类目管理
        Route::prefix( "/articleCategory" )->controller( ArticleCategoryController::class )->name( "articleCategory." )->group( function () {

            Route::get( "/", "index" );
            Route::get( "/{id}", "detail" )->name( "detail" );
            Route::post( "/", "create" )->name( "create" );
            Route::put( "/{id}", "update" )->name( "update" );
            Route::delete( "/", "delete" )->name( "delete" );
            Route::put( "/restore", "restore" )->name( "restore" );

        } );

        // 文章管理
        Route::prefix( "/article" )->controller( ArticleController::class )->name( "article." )->group( function () {

            Route::get( "/", "index" );
            Route::get( "/{id}", "detail" )->name( "detail" );
            Route::post( "/", "create" )->name( "create" );
            Route::put( "/{id}", "update" )->name( "update" );
            Route::delete( "/", "delete" )->name( "delete" );
            Route::put( "/restore", "restore" )->name( "restore" );

        } );

    } );
} );
