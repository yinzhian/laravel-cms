<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 表名
        $table = "articles";

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("admin_id", false)->comment("管理员ID");
            $table->unsignedBigInteger("article_category_id", false)->comment("分类ID");
            $table->string("title", 191)->unique()->comment("标题");
            $table->string("cover", 255)->nullable()->comment("封面图");
            $table->string("describe", 255)->nullable()->comment("描述");
            $table->longText("content")->comment("内容");
            $table->unsignedBigInteger("read_num", false)->default(0)->comment("阅读量");
            $table->unsignedTinyInteger("sort", false)->default(255)->comment("排序");
            $table->timestamps();
            $table->softDeletes();
        });

        // 表前缀
        $table = env( "DB_PREFIX" ) . $table;

        // 表名注释
        DB::statement( "ALTER TABLE {$table} comment '文章'" );//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
