<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateArticleLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 表名
        $table = "article_labels";

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("article_id", false)->comment("文章ID");
            $table->unsignedBigInteger("label_id", false)->comment("标签ID");
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
        Schema::dropIfExists('article_labels');
    }
}
