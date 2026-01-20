<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. สร้าง Query เริ่มต้น
        $query = \App\Models\ActivityLog::query();

        // 2. ถ้ามีการเลือกปีมา (และไม่ใช่ค่าว่าง) ให้กรองตามปีนั้น
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('created_at', $request->year);
        }

        // 3. ดึงข้อมูล (เรียงล่าสุดก่อน + แบ่งหน้า)
        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // 4. [เพิ่มใหม่] หาว่าในระบบมีปีอะไรบ้าง (เอาไปทำ Dropdown)
        // ดึงปีจาก created_at มา แล้วเอาปีที่ไม่ซ้ำกัน
        $years_list = \App\Models\ActivityLog::selectRaw('YEAR(created_at) as year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year');

        // ส่งข้อมูลไปที่หน้า View (ส่งปีที่เลือกปัจจุบันไปด้วย)
        return view('admin_logs', compact('logs', 'years_list'));
    }
    
}