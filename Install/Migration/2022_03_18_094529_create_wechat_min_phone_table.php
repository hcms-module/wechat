<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatMinPhoneTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_min_phone', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('app_id');
            $table->string('phone', 64)
                ->default('')
                ->nullable(false)
                ->comment('手机号码');
            $table->string('country_code', 32)
                ->default('')
                ->nullable(false)
                ->comment('国家区号，中国86');
            $table->string('target', 128)
                ->default('')
                ->nullable(false)
                ->comment('关联数据唯一标示，可以是用户的user_id或是openid');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_min_phone');
    }
}
