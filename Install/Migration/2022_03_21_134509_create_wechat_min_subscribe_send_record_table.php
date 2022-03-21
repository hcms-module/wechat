<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatMinSubscribeSendRecordTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_min_subscribe_send_record', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('');
            $table->string('openid', 128)
                ->nullable(false)
                ->default('')
                ->comment('用户openid');
            $table->string('template_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('模板id');
            $table->string('page', 256)
                ->default('')
                ->nullable(false)
                ->comment('跳转页面地址');
            $table->string('post_data', 1024)
                ->default('')
                ->nullable(false)
                ->comment('发送内容，以json的格式存储');
            $table->tinyInteger('status')
                ->nullable(false)
                ->default(0)
                ->comment('发送状态1成功0失败');
            $table->string('result', 512)
                ->default('')
                ->nullable(false)
                ->comment('返回结果');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_min_subscribe_send_record');
    }
}
