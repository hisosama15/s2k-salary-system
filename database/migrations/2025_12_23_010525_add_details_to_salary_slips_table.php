<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            // ข้อมูลทั่วไป
             $table->string('payment_type')->nullable()->after('emp_name'); // ประเภท: รายวัน/รายเดือน
             $table->string('department')->nullable()->after('payment_type'); // แผนก
             $table->string('account_number')->nullable()->after('department'); // เลขบัญชี

            // รายละเอียดการทำงาน (เอาไว้โชว์ในสลิปฝั่งซ้าย)
             $table->decimal('work_days', 10, 2)->default(0)->after('salary'); // วันทำงานปกติ
             $table->decimal('wage_rate', 10, 2)->default(0)->after('work_days'); // อัตราค่าจ้าง (ต่อวัน/เดือน)
            
            // จำนวนชั่วโมง OT (เอาไว้โชว์ในวงเล็บ)
             $table->decimal('ot_1_5_hrs', 10, 2)->default(0)->after('ot_3_0');
             $table->decimal('ot_1_0_hrs', 10, 2)->default(0)->after('ot_1_5_hrs');
             $table->decimal('ot_3_0_hrs', 10, 2)->default(0)->after('ot_1_0_hrs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            //
        });
    }
};
