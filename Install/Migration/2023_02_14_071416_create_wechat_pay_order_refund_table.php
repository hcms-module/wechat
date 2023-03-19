<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatPayOrderRefundTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_pay_order_refund', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('refund_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('微信支付退款单号');
            $table->string('out_refund_no', 128)
                ->default('')
                ->nullable(false)
                ->comment('支付退款唯一单号');
            $table->string('transaction_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('微信支付流水单号');
            $table->string('out_trade_no', 128)
                ->nullable(false)
                ->default('')
                ->comment('支付订单唯一单号');
            $table->string('channel', 32)
                ->default('')
                ->nullable(false)
                ->comment('退款渠道');
            $table->string('status', 32)
                ->nullable(false)
                ->default('')
                ->comment('退款状态');
            $table->integer('refund_fee')
                ->nullable(false)
                ->default(0)
                ->comment('退款金额');
            $table->bigInteger('total')
                ->default(0)
                ->nullable(false)
                ->comment('订单总金额');
            $table->text('meta_info')
                ->comment('原始数据');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_pay_order_refund');
    }
}
