<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalarySlip;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // 1. รับค่า Filter ต่างๆ
        $range = $request->input('range', 3);
        $selected_year = $request->input('year', date('Y')); // ปี ค.ศ. (เช่น 2026)

        // 2. ดึงสลิปทั้งหมดของพนักงานคนนี้ ในปีที่เลือก (เพื่อเอามาคำนวณยอดรวม)
        $yearSlips = \App\Models\SalarySlip::where('emp_id', $user->emp_id) // หรือ $user->username แล้วแต่ระบบเพื่อน
                            ->where('year', $selected_year)
                            ->get();

        // ✅✅ คำนวณยอดสะสมรายปี (เพิ่มใหม่ตรงนี้!)
        $total_year_income = $yearSlips->sum('total_income');
        $total_year_deduct = $yearSlips->sum('total_deduct'); // เงินหักสะสม
        $total_year_tax    = $yearSlips->sum('tax');          // ภาษีสะสม
        $total_year_sso    = $yearSlips->sum('sso');          // ประกันสังคมสะสม
        $total_year_net    = $yearSlips->sum('net_salary');   // รับสุทธิสะสม

        // 3. ดึงรายการสลิปที่จะโชว์ในตารางข้างล่าง (เรียงจากใหม่ไปเก่า)
        $slips = \App\Models\SalarySlip::where('emp_id', $user->emp_id)
                            ->where('year', $selected_year)
                            ->orderBy('year', 'desc')
                            ->orderBy('month', 'desc')
                            ->get();

        // 4. คำนวณยอดล่าสุด (กล่องซ้ายบน) ตาม Range ที่เลือก
        $recentSlips = \App\Models\SalarySlip::where('emp_id', $user->emp_id)
                            ->orderBy('pay_date', 'desc')
                            ->take($range)
                            ->get();
        $total_recent = $recentSlips->sum('net_salary');

        // 5. ลิสต์ปีที่มีข้อมูล (สำหรับ Dropdown)
        $years_list = \App\Models\SalarySlip::where('emp_id', $user->emp_id)
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year');

        // ส่งค่าไปหน้าเว็บ
        return view('dashboard', compact(
            'slips', 
            'total_recent', 
            'range', 
            'selected_year', 
            'years_list',
            'total_year_income',
            'total_year_deduct', // ✅ ส่งไป
            'total_year_tax',    // ✅ ส่งไป
            'total_year_sso',    // ✅ ส่งไป
            'total_year_net'
        ));
    }

    // หน้าดูรายละเอียดสลิป (ใบเสร็จ)
    public function show($id)
    {
        $user = Auth::user();

        // ดึงสลิปตาม ID แต่ต้องเช็คด้วยว่าเป็นของคนนี้จริงๆ (ห้ามแอบดูของคนอื่น)
        $slip = SalarySlip::where('id', $id)
                          ->where('emp_id', $user->emp_id)
                          ->firstOrFail(); // ถ้าไม่เจอ หรือไม่ใช่ของตัวเอง ให้ Error เลย

        return view('slip_detail', compact('slip'));
    }
}