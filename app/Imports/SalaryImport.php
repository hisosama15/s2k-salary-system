<?php

namespace App\Imports;

use App\Models\SalarySlip;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SalaryImport implements ToModel, WithStartRow
{
    protected $request;

    // รับค่า $request จาก Controller มาถือไว้
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function startRow(): int
    {
        return 3;
    }

    public function model(array $row)
    {
        if (!isset($row[1]) || empty(trim($row[1]))) {
            return null;
        }

        $clean = function ($val) {
            $val = str_replace(',', '', $val ?? '0');
            return is_numeric($val) ? floatval($val) : 0;
        };

        $text = function ($val) {
            return trim($val ?? '');
        };

        User::firstOrCreate(
            ['emp_id' => $text($row[1])],
            [
                'name' => $text($row[2]),
                'password' => Hash::make('1234'),
                'role' => 'employee'
            ]
        );

        return new SalarySlip([
            'emp_id'          => $text($row[1]),
            'emp_name'        => $text($row[2]),
            'bank_account_no' => $text($row[3]),
            'department'      => $text($row[4]),

            // ใช้ค่าจาก $this->request ที่ส่งมาจากหน้าเว็บ
            'month'      => $this->request->month,
            'year'       => $this->request->year_ad,
            'pay_date'   => $this->request->pay_date,
            'start_date' => $this->request->start_date,
            'end_date'   => $this->request->end_date,
            // ในไฟล์ SalaryImport.php ส่วนที่ return new SalarySlip
            'salary_type' => $this->request->payment_type, // ใช้แบบนี้ครับ
            
            'prepared_by'  => auth()->user()->name, 

            'wage_rate'      => $clean($row[5]),  
            'work_days'      => $clean($row[7]),  
            'salary'         => $clean($row[8]),  
            'ot_1_0_hrs'     => $clean($row[9]),  
            'ot_1_5_hrs'     => $clean($row[10]), 
            'ot_2_0_hrs'     => $clean($row[11]), 
            'ot_3_0_hrs'     => $clean($row[12]), 
            'ot_1_0'         => $clean($row[13]), 
            'ot_1_5'          => $clean($row[14]), 
            'ot_2_0'          => $clean($row[15]), 
            'ot_3_0'          => $clean($row[16]), 
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
            'sso'                    => $clean($row[28]), 
            'tax'                    => $clean($row[29]), 
            'student_loan_deduction' => $clean($row[30]), 
            'excess_leave_deduction' => $clean($row[31]), 
            'late_deduction'         => $clean($row[32]), 
            'other_deduct'           => $clean($row[33]), 
            'total_deduct'           => $clean($row[34]), 
            'net_salary'             => $clean($row[35]), 
            'sick_leave'         => $clean($row[36]), 
            'sick_leave_no_cert' => $clean($row[37]), 
            'personal_leave'     => $clean($row[38]), 
            'annual_leave'       => $clean($row[39]), 
            'absent'             => $clean($row[40]), 
            'other_leave'        => $clean($row[41]), 
            'late'               => $clean($row[42]), 
            'remark'             => $text($row[43]),  
        ]);
    }
}