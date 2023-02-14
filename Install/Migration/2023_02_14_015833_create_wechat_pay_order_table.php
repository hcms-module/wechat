<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatPayOrderTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_pay_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('appid', 128)
                ->default('')
                ->nullable(false)
                ->comment('公众号appid');
            $table->string('mchid', 128)
                ->nullable(false)
                ->default('')
                ->comment('支付商户号id');
            $table->string('openid', 128)
                ->default('')
                ->nullable(false)
                ->comment('授权用户的openid');
            $table->string('out_trade_no', 128)
                ->default('')
                ->nullable(false)
                ->comment('支付订单的唯一单号');
            $table->bigInteger('total_fee')
                ->nullable(false)
                ->default(0)
                ->comment('支付金额，单位（分）');
            $table->string('description', 128)
                ->default('')
                ->nullable(false)
                ->comment('支付商品介绍');
            $table->string('notify_url', 512)
                ->default('')
                ->nullable(false)
                ->comment('支付回调地址');
            $table->string('prepay_id', 256)
                ->nullable(false)
                ->default('')
                ->comment('支付的 prepay_id');
            $table->string('trade_type', 32)
                ->nullable(false)
                ->default('')
                ->comment('支付类型');
            $table->string('transaction_id', 256)
                ->default('')
                ->nullable(false)
                ->comment('微信支付系统生成的订单号');
            $table->string('trade_state', 32)
                ->nullable(false)
                ->default('')
                ->comment('支付状态');
            $table->string('trade_state_desc', 128)
                ->nullable(false)
                ->default('')
                ->comment('状态描述');
            $table->text('meta_info')
                ->nullable(true)
                ->comment('回调原始数据');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_pay_order');
    }
}
