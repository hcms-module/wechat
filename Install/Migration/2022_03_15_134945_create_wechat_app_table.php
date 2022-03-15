<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatAppTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_app', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_key', 128)
                ->default('')
                ->nullable(false)
                ->default('应用关键字，唯一标识');
            $table->tinyInteger('app_type')
                ->default(0)
                ->nullable(false)
                ->comment('0公众号、1小程序、2第三方开放平台');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('appid');
            $table->string('app_secret', 256)
                ->default('')
                ->nullable(false)
                ->comment('app_secret');
            $table->string('token', 128)
                ->default('')
                ->nullable(false)
                ->comment('消息推送需要的token凭证');
            $table->string('aes_key', 128)
                ->default('')
                ->nullable(false)
                ->comment('消息加密密钥');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_app');
    }
}
