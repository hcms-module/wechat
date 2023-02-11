<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatOfficeUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_office_user', function (Blueprint $table) {
            $table->bigIncrements('office_user_id');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('所属appid');
            $table->string('openid', 128)
                ->default('')
                ->nullable(false)
                ->comment('授权用户的openid');
            $table->string('nickname', 128)
                ->default('')
                ->nullable(false)
                ->comment('用户昵称');
            $table->string('headimgurl', 512)
                ->default('')
                ->nullable(false)
                ->comment('头像');
            $table->string('unionid', 128)
                ->default('')
                ->nullable(false)
                ->comment('只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_office_user');
    }
}
