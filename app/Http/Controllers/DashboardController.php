<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalarySlip;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. ดึงรายการปีที่มีในระบบ (เพื่อเอาไปทำ Dropdown เลือกปี)
        $years_list = SalarySlip::where('emp_id', $user->emp_id)
                                ->select('year')
                                ->distinct()
                                ->orderBy('year', 'desc')
                                ->pluck('year');

        // 2. กำหนดปีที่เลือก (ถ้าไม่เลือก ให้เอาปีล่าสุดที่มี หรือปีปัจจุบัน)
        $selected_year = $request->year ?? ($years_list->first() ?? date('Y'));

        // 3. ดึงสลิปเงินเดือน "เฉพาะปีที่เลือก" (เอาไปโชว์ในตารางข้างล่าง)
        $slips = SalarySlip::where('emp_id', $user->emp_id)
                           ->where('year', $selected_year)
                           ->orderBy('pay_date', 'desc')
                           ->get();

        // 4. คำนวณยอด 3 หรือ 6 เดือนล่าสุด (สำหรับกล่องสีส้มข้างบน)
        // อันนี้จะไม่เกี่ยวกับปีที่เลือกข้างบนครับ ต้องเอาล่าสุดจริงๆ เสมอ
        $range = $request->range ?? 3; // ค่าเริ่มต้นคือ 3 เดือน
        $recent_slips = SalarySlip::where('emp_id', $user->emp_id)
                                  ->orderBy('pay_date', 'desc')
                                  ->take($range)
                                  ->get();
        
        $total_recent = $recent_slips->sum('net_salary'); // ยอดรวมล่าสุด

        // 5. คำนวณยอดรวมรายปี (สำหรับปีที่เลือก)
        // ใช้ตัวแปร $slips ที่ดึงมาแล้วได้เลย ไม่ต้อง Query ใหม่
        $total_year_income = $slips->sum('total_income');
        $total_year_net = $slips->sum('net_salary');

        // 6. ส่งข้อมูลไปหน้าเว็บ
        // สังเกตว่าผมเอา 'total_last_3' ออกแล้ว และใส่ตัวแปรที่ครบถ้วนแทน
        return view('dashboard', compact(
            'slips', 
            'years_list', 
            'selected_year', 
            'range', 
            'total_recent', 
            'total_year_income', 
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