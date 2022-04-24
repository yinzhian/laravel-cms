<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateArticleCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 表名
        $table = "article_categories";

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string("title", 64)->unique()->comment("标题");
            $table->unsignedTinyInteger("sort", false)->nullable()->default(255)->comment("排序");
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
        Schema::dropIfExists('article_categories');
    }
}
