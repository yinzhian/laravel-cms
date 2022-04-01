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
    Route::post( "login", [ LoginController::class, "login" ] )->name( "login" );

    /// TODO 必须登录
    Route::middleware( [ 'admin.refresh' ] )->group( function () use ( $app_supper ) {

        // 退出
        Route::put( "quit", [ LoginController::class, "quit" ] )->name( "quit" );

        // 获取个人信息
        Route::get( "/me", [ AdminController::class, "me" ] )->name( "me" );

        // 获取七牛上传TOKEN
        Route::get( "/getQiNiuToken", [ CommonController::class, "getQiNiuToken" ] )->name( "getQiNiuToken" );

        // 管理员管理 - 仅超级管理员方可操作
        Route::prefix( "/admin" )->name( "admin." )->middleware( [ "role:{$app_supper}" ] )->group( function () {

            Route::get( "/", [ AdminController::class, "index" ] );
            Route::get( "/{id}", [ AdminController::class, "detail" ] )->name( "detail" );
            Route::post( "/", [ AdminController::class, "create" ] )->name( "create" );
            Route::put( "/{id}", [ AdminController::class, "update" ] )->name( "update" );
            Route::delete( "/", [ AdminController::class, "delete" ] )->name( "delete" );
            Route::put( "/restore", [ AdminController::class, "restore" ] )->name( "restore" );
            Route::put( "/{id}/addRole", [ AdminController::class, "addRole" ] )->name( "addRole" );
            Route::put( "/{id}/empower", [ AdminController::class, "empower" ] )->name( "empower" );

        } );

        // 角色管理 - 仅超级管理员方可操作
        Route::prefix( "/role" )->name( "role." )->middleware( [ "role:{$app_supper}" ] )->group( function () {

            Route::get( "/", [ RoleController::class, "index" ] );
            Route::get( "/all", [ RoleController::class, "getAll" ] )->name( "getAll" );
            Route::get( "/{id}", [ RoleController::class, "detail" ] )->name( "detail" );
            Route::post( "/", [ RoleController::class, "create" ] )->name( "create" );
            Route::put( "/{id}", [ RoleController::class, "update" ] )->name( "update" );
            Route::delete( "/", [ RoleController::class, "delete" ] )->name( "delete" );
            Route::put( "/restore", [ RoleController::class, "restore" ] )->name( "restore" );
            Route::put( "/{id}/empower", [ RoleController::class, "empower" ] )->name( "empower" );

        } );

        // 权限管理 - 仅超级管理员方可操作
        Route::prefix( "/permission" )->name( "permission." )->middleware( [ "role:{$app_supper}" ] )->group( function () {

            Route::get( "/", [ PermissionController::class, "index" ] );
            Route::get( "/all", [ PermissionController::class, "getAll" ] )->name( "getAll" );
            Route::get( "/{id}", [ PermissionController::class, "detail" ] )->name( "detail" );
            Route::post( "/", [ PermissionController::class, "create" ] )->name( "create" );
            Route::put( "/{id}", [ PermissionController::class, "update" ] )->name( "update" );
            Route::delete( "/", [ PermissionController::class, "delete" ] )->name( "delete" );
            Route::put( "/restore", [ PermissionController::class, "restore" ] )->name( "restore" );

        } );

        // 操作日志管理
        Route::prefix( "/operateLog" )->name( "operate_log." )->group( function () {

            Route::get( "/", [ OperateLogController::class, "index" ] );
            Route::get( "/{id}", [ OperateLogController::class, "detail" ] )->name( "detail" );

        } );

        // 配置管理
        Route::prefix( "/config" )->name( "config." )->group( function () {

            Route::get( "/", [ ConfigController::class, "index" ] );
            Route::get( "/{id}", [ ConfigController::class, "detail" ] )->name( "detail" );
            Route::post( "/", [ ConfigController::class, "create" ] )->name( "create" );
            Route::put( "/{id}", [ ConfigController::class, "update" ] )->name( "update" );

        } );

        // 标签管理
        Route::prefix( "/label" )->name( "label." )->group( function () {

            Route::get( "/", [ LabelController::class, "index" ] );
            Route::get( "/all", [ LabelController::class, "getAll" ] );
            Route::get( "/{id}", [ LabelController::class, "detail" ] )->name( "detail" );
            Route::post( "/", [ LabelController::class, "create" ] )->name( "create" );
            Route::put( "/{id}", [ LabelController::class, "update" ] )->name( "update" );
            Route::delete( "/", [ LabelController::class, "delete" ] )->name( "delete" );
            Route::put( "/restore", [ LabelController::class, "restore" ] )->name( "restore" );

        } );

        // 文章类目管理
        Route::prefix( "/articleCategory" )->name( "articleCategory." )->group( function () {

            Route::get( "/", [ ArticleCategoryController::class, "index" ] );
            Route::get( "/{id}", [ ArticleCategoryController::class, "detail" ] )->name( "detail" );
            Route::post( "/", [ ArticleCategoryController::class, "create" ] )->name( "create" );
            Route::put( "/{id}", [ ArticleCategoryController::class, "update" ] )->name( "update" );
            Route::delete( "/", [ ArticleCategoryController::class, "delete" ] )->name( "delete" );
            Route::put( "/restore", [ ArticleCategoryController::class, "restore" ] )->name( "restore" );

        } );

        // 文章管理
        Route::prefix( "/article" )->name( "article." )->group( function () {

            Route::get( "/", [ ArticleController::class, "index" ] );
            Route::get( "/{id}", [ ArticleController::class, "detail" ] )->name( "detail" );
            Route::post( "/", [ ArticleController::class, "create" ] )->name( "create" );
            Route::put( "/{id}", [ ArticleController::class, "update" ] )->name( "update" );
            Route::delete( "/", [ ArticleController::class, "delete" ] )->name( "delete" );
            Route::put( "/restore", [ ArticleController::class, "restore" ] )->name( "restore" );

        } );

    } );
} );
