<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            
            // ฟังก์ชันตัวช่วย: เช็คก่อนว่ามีคอลัมน์หรือยัง? ถ้ายังไม่มีค่อยสร้าง
            $addCol = function($col, $type, $after) use ($table) {
                if (!Schema::hasColumn('salary_slips', $col)) {
                    if ($type === 'string') {
                        $table->string($col)->nullable()->after($after);
                    } elseif ($type === 'text') {
                        $table->text($col)->nullable()->after($after);
                    } else { // decimal
                        $table->decimal($col, 10, 2)->default(0)->after($after);
                    }
                }
            };

            // --- 1. ข้อมูลทั่วไป ---
            $addCol('bank_account_no', 'string', 'department');
            $addCol('remark', 'text', 'net_salary');

            // --- 2. รายได้ ---
            $addCol('ot_1_0_hrs', 'decimal', 'salary');
            $addCol('ot_1_5_hrs', 'decimal', 'ot_1_0_hrs');
            $addCol('ot_2_0_hrs', 'decimal', 'ot_1_5_hrs');
            $addCol('ot_3_0_hrs', 'decimal', 'ot_2_0_hrs');

            $addCol('ot_1_0', 'decimal', 'ot_3_0_hrs');
            $addCol('ot_2_0', 'decimal', 'ot_1_5');
            
            $addCol('shift_allowance', 'decimal', 'diligence');
            $addCol('medical_allowance', 'decimal', 'living_allowance');
            $addCol('trip_allowance', 'decimal', 'medical_allowance');
            $addCol('per_diem', 'decimal', 'trip_allowance');
            $addCol('commission', 'decimal', 'per_diem');
            $addCol('bonus', 'decimal', 'commission');

            // --- 3. รายการหัก ---
            $addCol('student_loan_deduction', 'decimal', 'tax');
            $addCol('excess_leave_deduction', 'decimal', 'student_loan_deduction');
            $addCol('late_deduction', 'decimal', 'excess_leave_deduction');

            // --- 4. สถิติวันลา ---
            // เช็คทีละตัว ถ้าไม่มีค่อยสร้าง
            if (!Schema::hasColumn('salary_slips', 'sick_leave')) $table->decimal('sick_leave', 10, 2)->default(0);
            if (!Schema::hasColumn('salary_slips', 'sick_leave_no_cert')) $table->decimal('sick_leave_no_cert', 10, 2)->default(0);
            if (!Schema::hasColumn('salary_slips', 'personal_leave')) $table->decimal('personal_leave', 10, 2)->default(0);
            if (!Schema::hasColumn('salary_slips', 'annual_leave')) $table->decimal('annual_leave', 10, 2)->default(0);
            if (!Schema::hasColumn('salary_slips', 'absent')) $table->decimal('absent', 10, 2)->default(0);
            if (!Schema::hasColumn('salary_slips', 'other_leave')) $table->decimal('other_leave', 10, 2)->default(0);
            if (!Schema::hasColumn('salary_slips', 'late')) $table->decimal('late', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        // ตอนลบ (Rollback) ก็เช็คก่อนเหมือนกัน กัน Error
        Schema::table('salary_slips', function (Blueprint $table) {
            $cols = [
                'bank_account_no', 'remark',
                'ot_1_0_hrs', 'ot_1_5_hrs', 'ot_2_0_hrs', 'ot_3_0_hrs',
                'ot_1_0', 'ot_2_0',
                'shift_allowance', 'medical_allowance', 'trip_allowance', 'per_diem', 'commission', 'bonus',
                'student_loan_deduction', 'excess_leave_deduction', 'late_deduction',
                'sick_leave', 'sick_leave_no_cert', 'personal_leave', 'annual_leave', 'absent', 'other_leave', 'late'
            ];
            
            foreach ($cols as $col) {
                if (Schema::hasColumn('salary_slips', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};