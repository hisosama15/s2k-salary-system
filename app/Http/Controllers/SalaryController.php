<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SalaryImport;
use App\Models\SalarySlip;

class SalaryController extends Controller
{
    // หน้า Dashboard หลักของ Admin
    public function adminDashboard()
    {
        return view('admin_dashboard');
    }

    // 1. แก้ฟังก์ชัน index (เพิ่มการดึงประวัติงวด)
    public function index()
    {
        // ดึงรายการล่าสุดมาโชว์ในตาราง (เหมือนเดิม)
        $slips = SalarySlip::orderBy('id', 'desc')->take(10)->get();

        // [เพิ่มใหม่] ดึงงวดการจ่ายทั้งหมดที่มีในระบบ (เพื่อเอาไปทำ Dropdown ลบ)
        // Group ตามวันที่จ่าย, นับจำนวนคน, เรียงจากวันที่ล่าสุดก่อน
        $history_dates = SalarySlip::selectRaw('pay_date, count(*) as count')
                                   ->groupBy('pay_date')
                                   ->orderBy('pay_date', 'desc')
                                   ->get();

        return view('salary_upload', compact('slips', 'history_dates'));
    }

    // ตอนกดปุ่มอัปโหลด
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
            'month' => 'required',
            'year_th' => 'required',
            'pay_date' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'payment_type' => 'required', // รับค่าเพิ่ม
        ]);

        try {
            $year_ad = $request->year_th - 543;
            
            // ส่งค่าไปให้ตัว Import
            $request->merge(['year_ad' => $year_ad]);
            
            Excel::import(new SalaryImport, $request->file('file'));

            \App\Models\ActivityLog::record('Import Salary', "งวดเดือน: {$request->month}/{$request->year_th} (ประเภท: {$request->payment_type})");
            
            return back()->with('success', '✅ นำเข้าข้อมูลสำเร็จเรียบร้อย!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // 2. แก้ฟังก์ชันลบ (เปลี่ยนจากลบเดือน -> ลบตามวันที่จ่าย)
    public function deleteMonth(Request $request)
    {
        $pay_date = $request->pay_date;

        if (!$pay_date) {
            return back()->with('error', "❌ กรุณาเลือกงวดที่ต้องการลบ");
        }

        // ลบข้อมูลทั้งหมดที่ตรงกับ "วันที่จ่าย" ที่เลือก
        $deleted = SalarySlip::where('pay_date', $pay_date)->delete();

        if ($deleted > 0) {

            \App\Models\ActivityLog::record('Delete Data', "ลบข้อมูลงวดวันที่: $pay_date ($deleted รายการ)");
            // แปลงวันที่เป็นไทยสวยๆ เพื่อแจ้งเตือน
            $year_th = date('Y', strtotime($pay_date)) + 543;
            $date_th = date('d/m', strtotime($pay_date)) . '/' . $year_th;
            
            return back()->with('success', "✅ ลบข้อมูลงวดวันที่ $date_th เรียบร้อยแล้ว ($deleted รายการ)");
        } else {
            return back()->with('error', "❌ ไม่พบข้อมูลให้ลบ");
        }
    }

    // หน้าดูรายชื่อพนักงาน
   //public function employees()
    //{
        // ดึง User ทั้งหมดที่เป็นพนักงาน (ไม่เอา Admin)
    //    $employees = \App\Models\User::where('role', 'employee')->get();
    //    return view('admin_employees', compact('employees'));
    //}

    // 1. ฟังก์ชันค้นหา (แก้ของเดิมนิดหน่อย)
    public function employees(Request $request)
    {
        $search = $request->search;
        
        $employees = \App\Models\User::where('role', 'employee')
            ->when($search, function($query, $search) {
                // ค้นหาจาก ชื่อ หรือ รหัสพนักงาน
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('emp_id', 'like', "%{$search}%");
            })
            ->get();

        return view('admin_employees', compact('employees', 'search'));
    }

    // เปลี่ยนรหัสผ่านพนักงาน (Admin กำหนดเอง)
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:4', // บังคับว่าต้องพิมพ์และยาว 4 ตัวขึ้นไป
        ]);

        $user = \App\Models\User::findOrFail($id);
        $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $user->save();

        \App\Models\ActivityLog::record('Change Password', "เปลี่ยนรหัสผ่านให้พนักงาน: {$user->name} ({$user->emp_id})");

        return back()->with('success', "✅ เปลี่ยนรหัสผ่านของ $user->name เป็น '{$request->new_password}' เรียบร้อยแล้ว");
    }
}