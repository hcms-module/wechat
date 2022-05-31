<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatOpenworkEventTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_openwork_event', function (Blueprint $table) {
            $table->bigIncrements('event_id');
            $table->string('suite_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('SuiteId');
            $table->string('info_type', 128)
                ->default('')
                ->nullable(false)
                ->comment('事件类型');
            $table->text('event_content')
                ->nullable(false)
                ->comment('事件内容，json存储');
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
        Schema::dropIfExists('wechat_openwork_event');
    }
}
