<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 表名
        $table = "configs";

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("parent_id", false)->default(0)->comment("上级ID");
            $table->String("name", 20)->comment("名称");
            $table->String("key")->nullable()->unique()->comment("搜索KEY");
            $table->String("value")->nullable()->comment("配置值");
            $table->String("type", 32)->nullable()->default(\App\Enums\ConfigTypeEnum::INPUT["key"])->comment("类型");
            $table->longText("option")->nullable()->comment("选项");
            $table->unsignedTinyInteger("sort", false)->nullable()->default(255)->comment("排序");
            $table->unsignedTinyInteger("status", false)->nullable()->default(\App\Enums\StatusEnum::ENABLE["key"])->comment("状态");
            $table->timestamps();
            $table->softDeletes();
        });

        // 表前缀
        $table = env("DB_PREFIX").$table;

        // 表名注释
        DB::statement("ALTER TABLE {$table} comment '配置表'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configs');
    }
}
