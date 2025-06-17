<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatPayMchTransferTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_pay_mch_transfer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pay_mch_id', 128)
                ->default('')
                ->nullable(false)
                ->comment('商户号');
            $table->string('appid', 128)
                ->default('')
                ->nullable();
            $table->string('out_bill_no', 255)
                ->default('')
                ->nullable(false)
                ->comment('系统内部单号');
            $table->string('transfer_scene_id', 32)
                ->default('1000')
                ->nullable(false)
                ->comment('转账场景id');
            $table->string('openid', 128)
                ->default('')
                ->nullable(false)
                ->comment('用户openid');
            $table->bigInteger('transfer_amount')
                ->default(0)
                ->nullable(false)
                ->comment('转账金额');
            $table->string('transfer_remark', 255)
                ->default('')
                ->nullable(false)
                ->comment('转账备注');
            $table->string('transfer_out_trade_no', 1024)
                ->default('')
                ->nullable(false)
                ->comment('商户转账单号');
            $table->string('package_info', 1024)
                ->default('')
                ->nullable(false);
            $table->string('state', 32)
                ->default('')
                ->nullable(false)
                ->comment('转账状态');
            $table->string('fail_reason', 255)
                ->default('')
                ->nullable(false)
                ->comment('失败原因');
            $table->string('transfer_bill_no', 255)
                ->default('')
                ->nullable(false)
                ->comment('微信单号');
            $table->string('target', 128)
                ->default('')
                ->nullable(false)
                ->comment('来源标识');
            $table->string('target_type', 128)
                ->default('')
                ->nullable(false)
                ->comment('来源标识');
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_pay_mch_transfer');
    }
}
