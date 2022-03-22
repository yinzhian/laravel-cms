<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\CommonController;
use App\Http\Controllers\Web\IndexController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any( "/demo", [ IndexController::class, 'demo' ] )->name( "demo" );

// 获取枚举
Route::get( "/enum", [ CommonController::class, 'enum' ] )->name( "enum" );
