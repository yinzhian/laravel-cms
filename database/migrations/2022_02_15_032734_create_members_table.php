<?php

use App\Enums\IdentityEnum;
use App\Enums\SourceEnum;
use App\Enums\SexEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 表名
        $table = "members";

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("parent_id", false)->default(0)->comment("上级ID");
            $table->unsignedBigInteger("agent_id", false)->default(0)->comment("代理ID");
            $table->char("phone", 11)->unique()->comment("手机号");
            $table->string("nick", 32)->comment("昵称");
            $table->string("avatar", 255)->nullable()->default("https://scdn.qiqiangkeji.com/210904210553.png")->comment("头像");
            $table->char("invite_code", 8)->unique()->comment("邀请码");
            $table->longText("path")->nullable()->comment("路径[id,pid,ppid]");
            $table->date("birthday")->nullable()->comment("生日");
            $table->unsignedTinyInteger("identity", false)->nullable()->default(IdentityEnum::PLAIN["key"])->comment("身份");
            $table->unsignedTinyInteger("source", false)->nullable()->default(SourceEnum::WECHAT_MINI_APP["key"])->comment("来源");
            $table->unsignedTinyInteger("sex", false)->nullable()->default(SexEnum::SECRET["key"])->comment("性别");
            $table->unsignedTinyInteger("status", false)->nullable()->default(StatusEnum::ENABLE["key"])->comment("状态");
            $table->timestamps();
            $table->softDeletes();
        });

        // 表前缀
        $table = env("DB_PREFIX").$table;

        // 表名注释
        DB::statement("ALTER TABLE {$table} comment '用户表'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
