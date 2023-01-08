<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatMinEventMessageTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_min_event_message', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id', 128)
                ->nullable(false)
                ->default('')
                ->comment('app_id');
            $table->string('to_user_name', 128)
                ->default('')
                ->nullable(false)
                ->comment('接收者');
            $table->string('from_user_name', 128)
                ->default('')
                ->nullable(false)
                ->comment('发送者');
            $table->integer('create_time')
                ->default(0)
                ->nullable(false)
                ->comment('发送时间');
            $table->string('msg_type', 128)
                ->default('')
                ->nullable(false)
                ->comment('消息类型，默认event');
            $table->string('event', 128)
                ->default('')
                ->nullable(false)
                ->comment('事件');
            $table->string('trace_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('任务id');
            $table->integer('version')
                ->default(0)
                ->nullable(false)
                ->comment('版本号');
            $table->string('result', 1024)
                ->default('')
                ->nullable(false)
                ->comment('结果集合');
            $table->string('detail', 1024)
                ->default('')
                ->nullable(false)
                ->comment('详情集合');
            $table->tinyInteger('isrisky')
                ->default(0)
                ->nullable(false)
                ->comment('风险提示');
            $table->string('extra_info_json', 1024)
                ->default('')
                ->nullable(false)
                ->comment('结果集合');
            $table->tinyInteger('status_code')
                ->default(0)
                ->nullable(false)
                ->comment('状态码，默认是0');
            $table->string('info_md5', 128)
                ->default('')
                ->nullable(false)
                ->comment('信息md5');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_min_event_message');
    }
}
