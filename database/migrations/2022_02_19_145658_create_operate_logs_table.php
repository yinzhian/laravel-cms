<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOperateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 表名
        $table = "operate_logs";

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->String("route", 191)->nullable()->comment("路由");
            $table->String("path", 191)->nullable()->comment("PATH");
            $table->unsignedBigInteger("m_id", false)->default(\App\Enums\UserTypeEnum::MEMBER['key'])->comment("用户ID");
            $table->longText("param")->nullable()->comment("参数");
            $table->String("method", 8)->nullable()->comment("请求方式");
            $table->String("ip", 161)->nullable()->comment("IP");
            $table->unsignedTinyInteger("client_type", false)->default(\App\Enums\ClientEnum::ADMIN['key'])->comment("客户端类型");
            $table->timestamps();
            $table->softDeletes();
        });

        // 表前缀
        $table = env("DB_PREFIX").$table;

        // 表名注释
        DB::statement("ALTER TABLE {$table} comment '操作日志'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operate_logs');
    }
}
