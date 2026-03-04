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
        // แก้ไขเป็นชื่อตาราง 'salary_slips' ให้ตรงกับระบบจริง
        Schema::table('salary_slips', function (Blueprint $table) {
            // เพิ่มช่องเก็บประเภทเงินเดือน ไว้หลังช่องจำนวนเงิน (salary) 
            // หมายเหตุ: ถ้าในตารางไม่มีคอลัมน์ amount ให้เปลี่ยนเป็นหลังชื่อคอลัมน์ที่มี เช่น 'salary' หรือ 'emp_name'
            $table->string('salary_type')->nullable()->after('salary'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            // เผื่อต้องย้อนกลับ ให้ลบคอลัมน์นี้ทิ้ง
            $table->dropColumn('salary_type');
        });
    }
};