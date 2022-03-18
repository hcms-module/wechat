<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatMinUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_min_user', function (Blueprint $table) {
            $table->bigIncrements('mini_user_id');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('app_id');
            $table->string('openid', 128)
                ->default('')
                ->nullable(false)
                ->comment('用户openid');
            $table->string('session_key', 128)
                ->default('')
                ->nullable(false)
                ->comment('session_key 会话密钥');
            $table->string('unionid', 128)
                ->default('')
                ->nullable(false)
                ->comment('unionid');
            $table->string('token', 128)
                ->default('')
                ->nullable(false)
                ->comment('登录凭证，可作为登录凭证给到前端');
            $table->string('target', 64)
                ->default('')
                ->nullable(false)
                ->comment('关联来源，例如是关联用户的user_id');
            $table->integer('expire_time')
                ->default(0)
                ->nullable(false)
                ->comment('过期时间，0为不会过期');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_min_user');
    }
}
