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
            $table->tinyInteger('sex')
                ->default(0)
                ->nullable(false)
                ->comment('用户的性别，值为1时是男性，值为2时是女性，值为0时是未知');
            $table->string('province', 128)
                ->default('')
                ->nullable(false)
                ->comment('用户个人资料填写的省份');
            $table->string('city', 128)
                ->default('')
                ->nullable(false)
                ->comment('普通用户个人资料填写的城市');
            $table->string('country', 32)
                ->default('')
                ->nullable(false)
                ->comment('国家，如中国为CN');
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
