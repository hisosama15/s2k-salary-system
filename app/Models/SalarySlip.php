<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    use HasFactory;

    // บรรทัดนี้สำคัญมาก! อนุญาตให้บันทึกข้อมูลได้ทุกช่อง (Mass Assignment)
    protected $fillable = [
        'emp_id',
        'emp_name',
        'department',
        'bank_account_no', // [ใหม่]
        'month',
        'year',
        'pay_date',
        'start_date',
        'end_date',
        'payment_type',
        
        // รายได้
        'wage_rate',
        'work_days',
        'salary',
        'ot_1_0_hrs', 'ot_1_5_hrs', 'ot_2_0_hrs', 'ot_3_0_hrs', // [ใหม่] OT Hrs
        'ot_1_0', 'ot_1_5', 'ot_2_0', 'ot_3_0', // [ใหม่] OT Amount
        'diligence',
        'shift_allowance', 'living_allowance', 'medical_allowance', // [ใหม่]
        'trip_allowance', 'per_diem', 'commission', 'bonus', // [ใหม่]
        'other_income',
        'total_income',

        // รายการหัก
        'sso',
        'tax',
        'student_loan_deduction', // [ใหม่]
        'excess_leave_deduction', // [ใหม่]
        'late_deduction', // [ใหม่]
        'other_deduct',
        'absent_deduct',
        'total_deduct',
        'net_salary',

        // สถิติวันลา
        'sick_leave', 'sick_leave_no_cert', 'personal_leave', // [ใหม่]
        'annual_leave', 'absent', 'late', 'other_leave', // [ใหม่]
        
        'remark', // [ใหม่]
        'prepared_by', // ✅✅ ต้องมีบรรทัดนี้ครับ! ถ้าไม่มี ข้อมูลจะไม่เข้า
    ];
}