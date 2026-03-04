<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SalaryImport;
use App\Models\SalarySlip;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function adminDashboard()
    {
        return view('admin_dashboard');
    }

    public function index()
    {
        // รายการล่าสุด 10 คน
        $slips = SalarySlip::orderBy('id', 'desc')->take(10)->get();

        // [แก้ไขกลับ] ดึงประวัติแยกตามงวดวันที่ (Start - End Date) เพื่อมัดรวมคนในงวดเดียวกัน
        $history_dates = SalarySlip::selectRaw('start_date, end_date, count(*) as count')
                                    ->groupBy('start_date', 'end_date')
                                    ->orderBy('start_date', 'desc')
                                    ->get();

        return view('salary_upload', compact('slips', 'history_dates'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt',
            'month' => 'required',
            'year_th' => 'required',
            'pay_date' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'payment_type' => 'required', 
        ]);

        try {
            $year_ad = $request->year_th - 543;
            $request->merge(['year_ad' => $year_ad]);
            
            // ส่ง $request ไปให้ SalaryImport ด้วย
            Excel::import(new SalaryImport($request), $request->file('file'));

            ActivityLog::record('Import Salary', "อัปโหลดข้อมูลสำเร็จ งวดเดือน: {$request->month}/{$request->year_th} ({$request->payment_type})");
            
            return back()->with('success', '✅ นำเข้าข้อมูลสำเร็จเรียบร้อย!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // [แก้ไขกลับ] ฟังก์ชันลบตามงวดวันที่ (Period Delete)
    public function deleteMonth(Request $request)
    {
        $target = $request->delete_target; // รับค่า "start_date|end_date"

        if (!$target) {
            return back()->with('error', "❌ กรุณาเลือกรอบงวดวันที่ที่ต้องการลบ");
        }

        $dates = explode('|', $target);
        $start = $dates[0];
        $end = $dates[1];

        // ลบทุกคนที่อยู่ในงวดวันที่นี้ (ทั้งรายวันและรายเดือนหายพร้อมกัน)
        $deleted = SalarySlip::where('start_date', $start)
                             ->where('end_date', $end)
                             ->delete();

        if ($deleted > 0) {
            ActivityLog::record('Delete Period', "ลบข้อมูลลวดวันที่: $start ถึง $end (จำนวน $deleted รายการ)");
            
            $start_th = Carbon::parse($start)->addYears(543)->format('d/m/Y');
            $end_th = Carbon::parse($end)->addYears(543)->format('d/m/Y');
            
            return back()->with('success', "✅ ลบข้อมูลลวดวันที่ $start_th ถึง $end_th เรียบร้อยแล้ว ($deleted รายการ)");
        } else {
            return back()->with('error', "❌ ไม่พบข้อมูลในงวดที่เลือก");
        }
    }

    public function employees(Request $request)
    {
        $search = $request->search;
        $employees = User::where('role', 'employee')
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('emp_id', 'like', "%{$search}%");
            })
            ->get();

        return view('admin_employees', compact('employees', 'search'));
    }

    public function deleteEmployee($id)
    {
        try {
            $user = User::findOrFail($id);
            $name = $user->name;
            SalarySlip::where('emp_id', $user->emp_id)->delete();
            $user->delete();
            return back()->with('success', "✅ ลบพนักงาน $name เรียบร้อยแล้ว");
        } catch (\Exception $e) {
            return back()->with('error', "❌ ไม่สามารถลบได้: " . $e->getMessage());
        }
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate(['new_password' => 'required|string|min:4']);
        $user = User::findOrFail($id);
        $user->password = Hash::make($request->new_password);
        $user->save();
        return back()->with('success', "✅ เปลี่ยนรหัสผ่านเรียบร้อยแล้ว");
    }
}