<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatOfficeMessageTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_office_message', function (Blueprint $table) {
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
            $table->string('msg_type', 32)
                ->default('')
                ->nullable(false)
                ->comment('事件类型');
            $table->string('msg_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('消息ID');
            $table->string('content', 1024)
                ->default('')
                ->nullable(false)
                ->comment('文本消息内容');
            $table->string('pic_url', 256)
                ->default('')
                ->nullable(false)
                ->comment('图片链接（由系统生成）');
            $table->string('media_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('消息媒体id，可以调用获取临时素材接口拉取数据。');
            $table->string('format', 32)
                ->default('')
                ->nullable(false)
                ->comment('语音格式，如amr，speex等');
            $table->string('Recognition', 1024)
                ->default('')
                ->nullable(false)
                ->comment('语音识别结果，UTF8编码');
            $table->string('thumb_media_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('视频消息缩略图的媒体id，可以调用获取临时素材接口拉取数据');
            $table->string('location_x', 32)
                ->default('')
                ->nullable(false)
                ->comment('地理位置纬度');
            $table->string('location_y', 32)
                ->default('')
                ->comment('地理位置经度');
            $table->string('scale', 32)
                ->default('')
                ->comment('地图缩放大小');
            $table->string('label', 32)
                ->default('')
                ->comment('地理位置信息');
            $table->string('title', 32)
                ->default('')
                ->comment('消息标题');
            $table->string('description', 1024)
                ->default('')
                ->comment('消息描述');
            $table->string('url', 1024)
                ->default('')
                ->comment('消息链接');
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
        Schema::dropIfExists('wechat_office_message');
    }
}
