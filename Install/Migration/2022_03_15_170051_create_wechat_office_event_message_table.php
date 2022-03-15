<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatOfficeEventMessageTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_office_event_message', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('app_id');
            $table->string('to_user_name', 128)
                ->default('')
                ->nullable(false)
                ->comment('接收者openid');
            $table->string('from_user_name', 128)
                ->default('')
                ->nullable(false)
                ->comment('发送者');
            $table->integer('create_time')
                ->default(0)
                ->nullable(false)
                ->comment('创建时间');
            $table->string('event', 128)
                ->default('')
                ->nullable(false)
                ->comment('事件类型');
            $table->string('event_key', 128)
                ->default('')
                ->nullable(false)
                ->comment('事件关键字，部分事件会有');
            $table->string('ticket', 128)
                ->default('')
                ->nullable(false)
                ->comment('事件凭证，部分事件会有');
            $table->string('latitude', 32)
                ->default('')
                ->nullable(false)
                ->comment('纬度，位置相关事件会触发');
            $table->string('longitude', 32)
                ->default('')
                ->nullable(false)
                ->comment('经度，位置相关事件会触发');
            $table->string('precision', 32)
                ->default('')
                ->nullable(false)
                ->comment('地理位置精度，位置相关事件会触发');
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
        Schema::dropIfExists('wechat_office_event_message');
    }
}
