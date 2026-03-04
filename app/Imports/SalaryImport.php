<?php

namespace App\Imports;

use App\Models\SalarySlip;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SalaryImport implements ToModel, WithStartRow
{
    /**
     * เริ่มอ่านที่บรรทัดที่ 3 (ข้ามหัวตาราง 2 บรรทัด)
     */
    public function startRow(): int
    {
        return 3;
    }

    public function model(array $row)
    {
        // เช็ครหัสพนักงาน (Column B / Index 1) ถ้าไม่มีให้ข้าม
        if (!isset($row[1]) || empty(trim($row[1]))) {
            return null;
        }

        // ฟังก์ชันช่วยแปลงตัวเลข (ลบลูกน้ำ, แปลงค่าว่างเป็น 0)
        $clean = function ($val) {
            $val = str_replace(',', '', $val ?? '0');
            return is_numeric($val) ? floatval($val) : 0;
        };

        // ฟังก์ชันช่วยแปลงข้อความ (ตัดช่องว่างหน้าหลัง)
        $text = function ($val) {
            return trim($val ?? '');
        };

        // --- Auto-Register User (สร้าง User อัตโนมัติถ้ายังไม่มี) ---
        User::firstOrCreate(
            ['emp_id' => $text($row[1])],
            [
                'name' => $text($row[2]),
                'password' => Hash::make('1234'), // รหัสผ่านเริ่มต้น
                'role' => 'employee'
            ]
        );

        // --- สร้างข้อมูลสลิป ---
        return new SalarySlip([
            // ข้อมูลทั่วไป
            'emp_id'          => $text($row[1]),  // 1: รหัสพนักงาน
            'emp_name'        => $text($row[2]),  // 2: ชื่อ-สกุล
            'bank_account_no' => $text($row[3]),  // 3: เลขที่บัญชี
            'department'      => $text($row[4]),  // 4: แผนก

            // ข้อมูลวันที่ (รับจาก Form หน้าเว็บ)
            'month'      => request('month'),
            'year'       => request('year_ad'),
            'pay_date'   => request('pay_date'),
            'start_date' => request('start_date'),
            'end_date'   => request('end_date'),
            
            // [แก้ไขให้แม่นยำ] ใช้ trim เพื่อให้ค่า "รายวัน/รายเดือน" ไม่มีช่องว่างแฝง
            'salary_type'  => trim(request('payment_type')), 
            
            'prepared_by'  => auth()->user()->name, 

            // หมวดรายได้ (Income)
            'wage_rate'      => $clean($row[5]),  
            'work_days'      => $clean($row[7]),  
            'salary'         => $clean($row[8]),  

            // OT (ชั่วโมง)
            'ot_1_0_hrs'     => $clean($row[9]),  
            'ot_1_5_hrs'     => $clean($row[10]), 
            'ot_2_0_hrs'     => $clean($row[11]), 
            'ot_3_0_hrs'     => $clean($row[12]), 

            // OT (จำนวนเงิน)
            'ot_1_0'         => $clean($row[13]), 
            'ot_1_5'         => $clean($row[14]), 
            'ot_2_0'         => $clean($row[15]), 
            'ot_3_0'         => $clean($row[16]), 

            // ค่าตอบแทนอื่นๆ
            'diligence'         => $clean($row[18]), 
            'shift_allowance'   => $clean($row[19]), 
            'living_allowance'  => $clean($row[20]), 
            'medical_allowance' => $clean($row[21]), 
            'trip_allowance'    => $clean($row[22]), 
            'per_diem'          => $clean($row[23]), 
            'commission'        => $clean($row[24]), 
            'bonus'             => $clean($row[25]), 
            'other_income'      => $clean($row[26]), 
            
            'total_income'      => $clean($row[27]), 

            // หมวดรายการหัก (Deduct)
            'sso'                    => $clean($row[28]), 
            'tax'                    => $clean($row[29]), 
            'student_loan_deduction' => $clean($row[30]), 
            'excess_leave_deduction' => $clean($row[31]), 
            'late_deduction'         => $clean($row[32]), 
            'other_deduct'           => $clean($row[33]), 
            
            'total_deduct'           => $clean($row[34]), 

            // สุทธิ (Net)
            'net_salary'             => $clean($row[35]), 

            // สถิติวันลา/มาสาย (Stats)
            'sick_leave'         => $clean($row[36]), 
            'sick_leave_no_cert' => $clean($row[37]), 
            'personal_leave'     => $clean($row[38]), 
            'annual_leave'       => $clean($row[39]), 
            'absent'             => $clean($row[40]), 
            'other_leave'        => $clean($row[41]), 
            'late'               => $clean($row[42]), 

            // หมายเหตุ
            'remark'             => $text($row[43]),  
        ]);
    }
}