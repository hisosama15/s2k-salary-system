<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. แสดงหน้าฟอร์ม Login
    public function showLogin()
    {
        return view('login');
    }

    // 2. ตรวจสอบรหัสผ่าน (ตอนกดปุ่ม)
    public function login(Request $request)
    {
        // รับค่าจากฟอร์ม
        $credentials = $request->validate([
            'emp_id' => 'required',
            'password' => 'required',
        ]);

        // สั่งให้ Laravel ตรวจสอบ (มันจะเช็คกับตาราง users ให้อัตโนมัติ)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            \App\Models\ActivityLog::record('Login', "เข้าสู่ระบบสำเร็จ IP: " . $request->ip());

            if (Auth::user()->role == 'admin') {
                // 🔴 แก้ตรงนี้: ลบ ->intended ออก
                return redirect('/admin/dashboard'); 
            }

            // 🔴 แก้ตรงนี้: ลบ ->intended ออก
            return redirect('/dashboard');
        }

        // ถ้าผิด ให้เด้งกลับไปหน้าเดิมแล้วบอกว่าผิด
        return back()->withErrors([
            'emp_id' => 'รหัสพนักงาน หรือ รหัสผ่าน ไม่ถูกต้อง',
        ]);
    }

    // 3. ออกจากระบบ
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ฟังก์ชันให้พนักงานเปลี่ยนรหัสตัวเอง
    public function changePassword(Request $request)
    {
        // 1. ตรวจสอบข้อมูลที่ส่งมา
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:4|confirmed', // confirmed = ต้องมีช่องยืนยันรหัสผ่านที่ตรงกัน
        ]);

        $user = Auth::user();

        // 2. เช็คว่ารหัสเก่าที่กรอกมา ถูกต้องไหม?
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => '❌ รหัสผ่านปัจจุบันไม่ถูกต้อง']);
        }

        // 3. ถ้าถูก -> บันทึกรหัสใหม่
        $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $user->save();

        return back()->with('success', '✅ เปลี่ยนรหัสผ่านสำเร็จเรียบร้อย!');
    }
}