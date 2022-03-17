<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatOfficeTemplateSendRecordTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_office_template_send_record', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('app_id');
            $table->string('open_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('接收用户open_id');
            $table->string('template_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('模板id');

            $table->string('url', 128)
                ->default('')
                ->nullable(false)
                ->comment('跳转的url，如果传小程序的appid，那就是小程序是page path');

            $table->string('mini_program_appid', 128)
                ->default('')
                ->nullable(false)
                ->comment('小程序的appid');
            $table->string('post_data', 1024)
                ->default('')
                ->nullable(false)
                ->comment('发送内容，以json的格式存储');
            $table->tinyInteger('status')
                ->default(1)
                ->nullable(false)
                ->comment('发送状态1成功、0失败');
            $table->string('result', 512)
                ->default('')
                ->nullable(false)
                ->comment('发送结果，success，failed等信息');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_office_template_send_record');
    }
}
