<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 表名
        $table = "admins";

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->String("username", 32)->unique()->comment("登录账号");
            $table->String("password", 255)->comment("登录密码");
            $table->String("real_name", 16)->nullable()->comment("姓名");
            $table->char("phone", 11)->unique()->comment("手机号");
            $table->String("email", 32)->nullable()->comment("邮箱");
            $table->String("avatar", 255)->nullable()->default("https://scdn.qiqiangkeji.com/210904210553.png")->comment("头像");
            $table->unsignedTinyInteger("status", false)->nullable()->default(\App\Enums\StatusEnum::ENABLE["key"])->comment("状态");
            $table->timestamps();
            $table->softDeletes();

        });

        // 表前缀
        $table = env("DB_PREFIX").$table;

        // 表名注释
        DB::statement("ALTER TABLE {$table} comment '管理员表'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
