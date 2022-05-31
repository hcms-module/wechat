<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatOpenworkCorpTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_openwork_corp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('corpid', 128)
                ->default('')
                ->nullable(false)
                ->comment('企业id');
            $table->string('corp_name', 256)
                ->default('')
                ->nullable(false)
                ->comment('企业名称');
            $table->string('permanent_code', 512)
                ->default('')
                ->nullable(false)
                ->comment('永久授权code');
            $table->string('access_token', 512)
                ->default('')
                ->nullable(false)
                ->comment('请求 access_token');
            $table->integer('expires_in')
                ->default(0)
                ->nullable(false)
                ->comment('access_token 过期时间');
            $table->text('auth_corp_info')
                ->nullable(false)
                ->comment('企业信息');
            $table->text('auth_info')
                ->nullable(false)
                ->comment('授权信息');
            $table->string('auth_user_info', 1024)
                ->default('')
                ->nullable(false)
                ->comment('操作授权的用户信息');
            $table->string('register_code_info', 1024)
                ->default('')
                ->nullable(false)
                ->comment('注册码相关信息');
            $table->string('state', 128)
                ->default('')
                ->nullable(false)
                ->comment('授权状态相关信息');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_openwork_corp');
    }
}
