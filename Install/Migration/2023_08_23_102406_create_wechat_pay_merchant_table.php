<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWechatPayMerchantTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_pay_merchant', function (Blueprint $table) {
            $table->bigIncrements('merchant_id');
            $table->tinyInteger('merchant_type')
                ->default(0)
                ->comment("0 普通商户，1特约商户");
            $table->string("pay_mch_id", 32)
                ->default("")
                ->comment("商户号");
            $table->string("pay_secret_key", 128)
                ->default("")
                ->comment("支付secret");
            $table->string("pay_v2_secret_key", 128)
                ->default("")
                ->comment("支付secret-v2");
            $table->string("serial", 128)
                ->default("")
                ->comment("证书序列号");
            $table->string("pay_cert_key", 2048)
                ->default("")
                ->comment("支付秘钥");
            $table->string("platform_cert_pem", 2048)
                ->default("")
                ->comment("平台支付证书");
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_pay_merchant');
    }
}
