<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $guarded = []; // อนุญาตให้บันทึกข้อมูลได้ทุกช่อง

    // ฟังก์ชันช่วยบันทึกแบบง่ายๆ (Static Helper)
    public static function record($action, $description = null)
    {
        // ถ้ามีคนล็อกอินอยู่ ให้ดึงชื่อมา ถ้าไม่มี (เช่นหน้า Login) ให้เป็น Guest
        $name = auth()->check() ? auth()->user()->name : 'Guest';
        $role = auth()->check() ? ' (' . auth()->user()->role . ')' : '';

        self::create([
            'user_name' => $name . $role,
            'action' => $action,
            'description' => $description
        ]);
    }
}