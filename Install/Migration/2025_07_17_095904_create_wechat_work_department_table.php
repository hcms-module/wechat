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
        Schema::create('wechat_work_department', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('corp_id', 128)
                ->nullable(false)
                ->default("")
                ->comment('企业的 corp_id');
            $table->string('name', 128)
                ->nullable(false)
                ->default("")
                ->comment('部门名称');
            $table->integer('parentid')
                ->nullable(false)
                ->default(0)
                ->comment('父部门id，根部门为1');
            $table->integer('order')
                ->default(0)
                ->nullable(false)
                ->comment('在父部门中的次序值。order值大的排序靠前。有效的值范围是[0, 2^32)');
            $table->string('department_leader', 1024)
                ->nullable(false)
                ->default("")
                ->comment('部门负责人的UserID；第三方仅通讯录应用可获取');
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_work_department');
    }
};
