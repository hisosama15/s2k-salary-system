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
        if (!isset($row[1]) || empty($row[1])) {
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

        // --- สร้างข้อมูลสลิป (Mapping ตามที่นายบอก) ---
        return new SalarySlip([
            // ข้อมูลทั่วไป
            'emp_id'          => $text($row[1]),  // 1: รหัสพนักงาน
            'emp_name'        => $text($row[2]),  // 2: ชื่อ-สกุล
            'bank_account_no' => $text($row[3]),  // 3: เลขที่บัญชี (เพิ่มใหม่)
            'department'      => $text($row[4]),  // 4: แผนก

            // ข้อมูลวันที่ (รับจาก Form หน้าเว็บ)
            'month'      => request('month'),
            'year'       => request('year_ad'),
            'pay_date'   => request('pay_date'),
            'start_date' => request('start_date'),
            'end_date'   => request('end_date'),
            'payment_type' => request('payment_type'), // ✅ เพิ่มบรรทัดนี้แล้วครับ!

            // ---------------------------------
            // หมวดรายได้ (Income)
            // ---------------------------------
            'wage_rate'      => $clean($row[5]),  // 5: อัตราเงินเดือน
            'work_days'      => $clean($row[7]),  // 7: วันทำงานปกติ
            'salary'         => $clean($row[8]),  // 8: เงินเดือน

            // OT (ชั่วโมง)
            'ot_1_0_hrs'     => $clean($row[9]),  // 9: Ot*1hr
            'ot_1_5_hrs'     => $clean($row[10]), // 10: Ot*1.5hr
            'ot_2_0_hrs'     => $clean($row[11]), // 11: Ot*2hr
            'ot_3_0_hrs'     => $clean($row[12]), // 12: Ot*3hr

            // OT (จำนวนเงิน)
            'ot_1_0'         => $clean($row[13]), // 13: Ot*1 (เงิน)
            'ot_1_5'         => $clean($row[14]), // 14: Ot*1.5 (เงิน)
            'ot_2_0'         => $clean($row[15]), // 15: Ot*2 (เงิน)
            'ot_3_0'         => $clean($row[16]), // 16: Ot*3 (เงิน)

            // ค่าตอบแทนอื่นๆ
            'diligence'         => $clean($row[18]), // 18: เบี้ยขยัน
            'shift_allowance'   => $clean($row[19]), // 19: ค่ากะ
            'living_allowance'  => $clean($row[20]), // 20: ค่าครองชีพ
            'medical_allowance' => $clean($row[21]), // 21: ค่ารักษา
            'trip_allowance'    => $clean($row[22]), // 22: ค่าเที่ยว
            'per_diem'          => $clean($row[23]), // 23: เบี้ยเลี้ยง
            'commission'        => $clean($row[24]), // 24: ค่าคอม
            'bonus'             => $clean($row[25]), // 25: โบนัส
            'other_income'      => $clean($row[26]), // 26: รายได้อื่นๆ
            
            'total_income'      => $clean($row[27]), // 27: รวมรายได้

            // ---------------------------------
            // หมวดรายการหัก (Deduct)
            // ---------------------------------
            'sso'                    => $clean($row[28]), // 28: ประกันสังคม
            'tax'                    => $clean($row[29]), // 29: ภาษี
            'student_loan_deduction' => $clean($row[30]), // 30: หัก กยศ.
            'excess_leave_deduction' => $clean($row[31]), // 31: หักลาเกินสิทธิ์
            'late_deduction'         => $clean($row[32]), // 32: หักสาย (เงิน)
            'other_deduct'           => $clean($row[33]), // 33: หักอื่นๆ
            
            'total_deduct'           => $clean($row[34]), // 34: รวมรายการหัก

            // ---------------------------------
            // สุทธิ (Net)
            // ---------------------------------
            'net_salary'             => $clean($row[35]), // 35: เงินได้สุทธิ

            // ---------------------------------
            // สถิติวันลา/มาสาย (Stats)
            // ---------------------------------
            'sick_leave'         => $clean($row[36]), // 36: ป่วย (วัน)
            'sick_leave_no_cert' => $clean($row[37]), // 37: ป่วยไม่มีใบ (วัน)
            'personal_leave'     => $clean($row[38]), // 38: ลากิจ (ชม.)
            'annual_leave'       => $clean($row[39]), // 39: พักร้อน (ชม.)
            'absent'             => $clean($row[40]), // 40: ขาดงาน (ชม.)
            'other_leave'        => $clean($row[41]), // 41: ลาอื่นๆ (ชม.)
            'late'               => $clean($row[42]), // 42: สาย (นาที)

            // หมายเหตุ
            'remark'             => $text($row[43]),  // 43: หมายเหตุ
        ]);
    }
}