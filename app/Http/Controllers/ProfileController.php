<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // แสดงหน้าฟอร์มแก้ไขข้อมูล
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user()
        ]);
    }

    // บันทึกข้อมูล
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // รหัสผ่านถ้าไม่เปลี่ยน ให้เว้นว่างได้
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->name = $request->name;

        // ถ้ามีการกรอกรหัสผ่านใหม่ค่อยเปลี่ยน
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว!');
    }
}