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
    Schema::create('salary_slips', function (Blueprint $table) {
        $table->id();
        $table->string('emp_id'); // รหัสพนักงาน
        $table->string('emp_name')->nullable(); // ชื่อ-สกุล (เผื่อไว้)
        
        // ข้อมูลเดือน/ปี
        $table->integer('month');
        $table->integer('year');
        $table->date('pay_date')->nullable();

        // --- รายได้ ---
        $table->decimal('salary', 10, 2)->default(0);      // ค่าแรง/เงินเดือน
        $table->decimal('ot_1_5', 10, 2)->default(0);      // OT 1.5
        $table->decimal('ot_1_0', 10, 2)->default(0);      // OT 1.0 (จากไฟล์พี่ A)
        $table->decimal('ot_3_0', 10, 2)->default(0);      // OT 3.0 (ในไฟล์เขียน OT 2 แต่เราเก็บช่องนี้แทนได้)
        $table->decimal('shift_fee', 10, 2)->default(0);   // ค่ากะ
        $table->decimal('living_allowance', 10, 2)->default(0); // ค่าครองชีพ
        $table->decimal('diligence', 10, 2)->default(0);   // เบี้ยขยัน
        $table->decimal('other_income', 10, 2)->default(0); // รายได้อื่นๆ
        
        // --- รายการหัก ---
        $table->decimal('sso', 10, 2)->default(0);         // ประกันสังคม
        $table->decimal('absent_deduct', 10, 2)->default(0); // หักขาดงาน/ลา
        $table->decimal('other_deduct', 10, 2)->default(0); // หักอื่นๆ
        $table->decimal('tax', 10, 2)->default(0);          // ภาษี (เผื่อไว้)

        // --- สรุปยอด ---
        $table->decimal('total_income', 10, 2)->default(0);
        $table->decimal('total_deduct', 10, 2)->default(0);
        $table->decimal('net_salary', 10, 2)->default(0); // เงินได้สุทธิ

        // --- เติม 3 บรรทัดนี้ลงไปครับ ---
        $table->decimal('ytd_income', 12, 2)->default(0);
        $table->decimal('ytd_tax', 12, 2)->default(0);
        $table->decimal('ytd_sso', 12, 2)->default(0);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};
