<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_work_oa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('corp_id', 128)
                ->nullable(false)
                ->default('')
                ->comment('企业ID');
            $table->string('sp_no', 128)
                ->default("")
                ->nullable(false)
                ->comment('审批编号');
            $table->string('sp_name', 128)
                ->default('')
                ->nullable(false)
                ->comment('审批申请类型名称（审批模板名称）');
            $table->integer('sp_status')
                ->default(0)
                ->comment('申请单状态：1-审批中；2-已通过；3-已驳回；4-已撤销；6-通过后撤销；7-已删除；10-已支付');
            $table->string('template_id', 128)
                ->default("")
                ->nullable(false)
                ->comment('审批模板id。可在“获取审批申请详情”、“审批状态变化回调通知”中获得，也可在审批模板的模板编辑页面链接中获得。');
            $table->integer('apply_time')
                ->default(0)
                ->nullable(false)
                ->comment('审批申请提交时间,Unix时间戳');
            $table->string('creator_userid', 128)
                ->default('')
                ->nullable(false)
                ->comment('申请人userid，此审批申请将以此员工身份提交，申请人需在应用可见范围内');
            $table->integer('department_id')
                ->default(0)
                ->nullable(false)
                ->comment('发起人部门id');
            $table->text('submit_param')
                ->comment('提交审批申请时传入的参数，格式为json');
            $table->longText('sp_result')
                ->comment('审批结果，审批通过时返回审批结果，审批不通过时返回驳回原因');
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_work_oa');
    }
};
