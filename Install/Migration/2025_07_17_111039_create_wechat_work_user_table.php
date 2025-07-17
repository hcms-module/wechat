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
        Schema::create('wechat_work_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('corp_id', 128)
                ->nullable(false)
                ->default("")
                ->comment('企业的 corp_id');
            $table->string('userid', 64)
                ->unique()
                ->nullable(false)
                ->default('')
                ->comment('用户唯一标识');
            $table->string('name', 64)
                ->nullable(false)
                ->default('')
                ->comment('成员名称');
            $table->string('department', 64)
                ->nullable(false)
                ->default('')
                ->comment('部门id列表');
            $table->integer('main_department')
                ->nullable(false)
                ->default(0)
                ->comment('主部门id');
            $table->string('position', 64)
                ->nullable(false)
                ->default('')
                ->comment('职位信息');
            $table->integer('status')
                ->nullable(false)
                ->default(1)
                ->comment('用户激活状态: 1=已激活，2=已禁用，4=未激活，5=退出企业');
            $table->integer('isleader')
                ->nullable(false)
                ->default(0)
                ->comment('是否为部门主管');
            $table->string('telephone', 64)
                ->nullable(false)
                ->default('')
                ->comment('座机');
            $table->integer('enable')
                ->nullable(false)
                ->default(1)
                ->comment('是否开启企业微信');
            $table->string('alias', 128)
                ->nullable(false)
                ->default('')
                ->comment('别名');
            $table->string('direct_leader', 512)
                ->nullable(false)
                ->default('')
                ->comment('直接主管');
            //授权才获得的资料

            $table->integer('gender')
                ->nullable(false)
                ->default(0)
                ->comment('性别');
            $table->string("email", 128)
                ->nullable(false)
                ->default('')
                ->comment('邮箱');
            $table->string("avatar", 512)
                ->nullable(false)
                ->default('')
                ->comment('头像');
            $table->string("qr_code", 512)
                ->nullable(false)
                ->default('')
                ->comment('二维码');
            $table->string("address", 128)
                ->nullable(false)
                ->default('')
                ->comment('地址');

            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wechat_work_user');
    }
};
