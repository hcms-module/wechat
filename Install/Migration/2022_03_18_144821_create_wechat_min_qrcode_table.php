<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatMinQrcodeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_min_qrcode', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('app_id');
            $table->string('scene', 64)
                ->default('')
                ->nullable(false)
                ->comment('场景值');
            $table->string('page', 128)
                ->default('')
                ->nullable(false)
                ->comment('页面路径，为空则是首页');
            $table->string('env_version', 64)
                ->default('')
                ->nullable(false)
                ->comment('版本类型');
            $table->integer('width')
                ->default(0)
                ->nullable(false)
                ->comment('尺寸');
            $table->string('file_path', 256)
                ->default('')
                ->nullable(false)
                ->comment('文件路径');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_min_qrcode');
    }
}
