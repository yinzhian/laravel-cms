<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 表名
        $table = "labels";

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string("title", 255)->comment("标题");
            $table->string("color", 191)->nullable()->comment("颜色");
            $table->string("icon", 255)->nullable()->comment("图标");
            $table->unsignedTinyInteger("type", false)->default(\App\Enums\LabelTypeEnum::ARTICLE["key"])->comment("类型");
            $table->unsignedTinyInteger("sort", false)->default(255)->comment("排序");
            $table->timestamps();
            $table->softDeletes();
        });

        // 表前缀
        $table = env( "DB_PREFIX" ) . $table;

        // 表名注释
        DB::statement( "ALTER TABLE {$table} comment '文章类目'" );//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labels');
    }
}
