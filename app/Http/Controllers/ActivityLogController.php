<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // 1. สร้าง Query เริ่มต้น
        $query = ActivityLog::query();

        // 2. ถ้ามีการเลือกปีมา ให้กรองตามปีนั้น
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('created_at', $request->year);
        }

        // 3. ดึงข้อมูล (เรียงล่าสุดก่อน + แบ่งหน้า 50 รายการ)
        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // 4. หาว่าในระบบมีปีอะไรบ้าง เพื่อไปทำ Dropdown
        $years_list = ActivityLog::selectRaw('YEAR(created_at) as year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year');

        return view('admin_logs', compact('logs', 'years_list'));
    }

    // [เพิ่มใหม่] ฟังก์ชันสำหรับล้างประวัติการใช้งาน
    public function clearLogs(Request $request)
    {
        try {
            if ($request->type == 'old') {
                // ลบข้อมูลที่เก่ากว่า 30 วันนับจากวันนี้
                $count = ActivityLog::where('created_at', '<', now()->subDays(30))->delete();
                $message = "✅ ล้างประวัติที่เก่ากว่า 30 วันเรียบร้อยแล้ว (ลบไป $count รายการ)";
            } else {
                // ลบทั้งหมด (ใช้ truncate เพื่อรีเซ็ต ID เริ่มต้นใหม่ด้วย)
                ActivityLog::truncate();
                $message = "✅ ล้างประวัติทั้งหมดในระบบเรียบร้อยแล้ว";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', "❌ เกิดข้อผิดพลาด: " . $e->getMessage());
        }
    }
}