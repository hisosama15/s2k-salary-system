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

        // [แก้ไขใหม่] ดึงประวัติแยกตาม "วินาทีที่กดอัปโหลด" (Timeline)
        // จัดกลุ่มตาม created_at เพื่อให้เห็นว่ารอบไหนอัปโหลดมาเท่าไหร่
        $history_dates = SalarySlip::selectRaw('created_at, count(*) as count')
                                    ->groupBy('created_at')
                                    ->orderBy('created_at', 'desc')
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
            
            Excel::import(new SalaryImport($request), $request->file('file'));

            ActivityLog::record('Import Salary', "อัปโหลดข้อมูลสำเร็จ งวดเดือน: {$request->month}/{$request->year_th}");
            
            return back()->with('success', '✅ นำเข้าข้อมูลรอบนี้สำเร็จเรียบร้อย!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // [แก้ไขใหม่] ฟังก์ชันลบตามรอบเวลา (Batch Delete)
    public function deleteMonth(Request $request)
    {
        $created_at = $request->delete_target; 

        if (!$created_at) {
            return back()->with('error', "❌ กรุณาเลือกรอบเวลาที่ต้องการลบ");
        }

        // ลบทุกคนที่ถูกบันทึกเข้ามาในวินาทีเดียวกันเป๊ะๆ
        $deleted = SalarySlip::where('created_at', $created_at)->delete();

        if ($deleted > 0) {
            ActivityLog::record('Delete Batch', "ลบข้อมูลรอบอัปโหลดเวลา: $created_at (จำนวน $deleted รายการ)");
            
            $date_th = Carbon::parse($created_at)->addYears(543)->format('d/m/Y H:i:s');
            return back()->with('success', "✅ ลบข้อมูลรอบอัปโหลดวันที่ $date_th เรียบร้อยแล้ว ($deleted รายการ)");
        } else {
            return back()->with('error', "❌ ไม่พบข้อมูลในรอบที่เลือก (อาจถูกลบไปแล้ว)");
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