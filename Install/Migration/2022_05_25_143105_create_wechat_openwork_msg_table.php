<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatOpenworkMsgTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_openwork_msg', function (Blueprint $table) {
            $table->bigIncrements('msg_id');
            $table->string('to_user_name', 128)
                ->default('')
                ->nullable(false)
                ->comment('ToUserName');
            $table->string('from_user_name', 128)
                ->default('')
                ->nullable(false)
                ->comment('FromUserName');
            $table->string('msg_type', 128)
                ->default('')
                ->nullable(false)
                ->comment('消息类型');
            $table->text('msg_content')
                ->comment('消息类型');
            $table->tinyInteger('handle_res')
                ->default(0)
                ->nullable(false)
                ->comment('处理结果0未处理，1已经处理');
            $table->string('handle_msg', 128)
                ->default('')
                ->nullable(false)
                ->comment('处理结果信息');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_openwork_msg');
    }
}
