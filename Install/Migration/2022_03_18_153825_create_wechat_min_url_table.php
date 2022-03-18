<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatMinUrlTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_min_url', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('appid');
            $table->string('path', 256)
                ->default('')
                ->nullable(false)
                ->comment('页面路径，空为首页');
            $table->string('query', 512)
                ->default('')
                ->nullable(false)
                ->comment('页面参数，建议使用 scene=xxx，这样能跟小程序码生成一样处理');
            $table->boolean('is_expire')
                ->default(true)
                ->nullable(false)
                ->comment('是否过期');
            $table->integer('expire_time')
                ->default(0)
                ->nullable(false)
                ->comment('过期时间0为永久');
            $table->string('env_version', 64)
                ->default('')
                ->nullable(false)
                ->comment('版本');
            $table->string('url_link', 128)
                ->nullable(false)
                ->default('')
                ->comment('打开小程序短连接');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_min_url');
    }
}
