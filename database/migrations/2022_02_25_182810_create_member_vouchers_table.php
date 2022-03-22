<?php

use App\Enums\LoginTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMemberVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 表名
        $table = "member_vouchers";

        Schema::create( $table, function ( Blueprint $table ) {
            $table->id();
            $table->unsignedBigInteger( "member_id", false )->comment( "用户ID" );
            $table->string( "account", 191 )->comment( "账号" );
            $table->string( "voucher", 255 )->nullable()->comment( "凭证" );
            $table->unsignedTinyInteger( "login_type", false )->default( LoginTypeEnum::WECHAT_MINI_APP["key"] )->comment( "登录类型" );
            $table->unsignedTinyInteger( "status", false )->default( StatusEnum::ENABLE["key"] )->comment( "状态" );
            $table->timestamps();
            $table->softDeletes();
        } );

        // 表前缀
        $table = env( "DB_PREFIX" ) . $table;

        // 表名注释
        DB::statement( "ALTER TABLE {$table} comment '用户凭证'" );//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'member_vouchers' );
    }
}
