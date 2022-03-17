<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatOfficeQrcodeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_office_qrcode', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('app_id');
            $table->string('scene', 128)
                ->default('')
                ->nullable(false)
                ->comment('场景值、参数值');
            $table->integer('expire_time')
                ->default(0)
                ->nullable(false)
                ->comment('过期时间、0位永久');
            $table->string('file_path', 256)
                ->default('')
                ->nullable(false)
                ->comment('文件本地存储路径');
            $table->string('qrcode_url', 256)
                ->default('')
                ->nullable(false)
                ->comment('二维码url');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_office_qrcode');
    }
}
