<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController; // เพิ่มตรงนี้เพื่อให้เรียกใช้งานได้ง่ายขึ้น

// 1. หน้า Login (เข้าได้ทุกคน)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/change-password', [AuthController::class, 'changePassword']);

// 2. กลุ่มที่ต้องล็อกอินก่อนถึงจะเข้าได้ (Middleware Auth)
Route::middleware(['auth'])->group(function () {
    
    Route::get('/admin/dashboard', [SalaryController::class, 'adminDashboard']);
    Route::get('/admin/employees', [SalaryController::class, 'employees']);

    // หน้า Import ของ Admin
    Route::get('/import', [SalaryController::class, 'index']);
    Route::post('/import', [SalaryController::class, 'import']);
    Route::post('/salary/delete', [SalaryController::class, 'deleteMonth']);
    
    // จัดการพนักงาน
    Route::post('/admin/change-password/{id}', [SalaryController::class, 'changePassword']);
    Route::delete('/admin/employees/{id}', [SalaryController::class, 'deleteEmployee'])->name('admin.employees.delete');
    
    // จัดการ Logs
    Route::get('/admin/logs', [ActivityLogController::class, 'index'])->name('admin.logs.index');
    // [เพิ่มตรงนี้!] Route สำหรับปุ่มล้าง Log ที่เราเพิ่งทำกัน
    Route::post('/admin/logs/clear', [ActivityLogController::class, 'clearLogs'])->name('admin.logs.clear');
    

    // หน้า Dashboard พนักงาน
    Route::get('/dashboard', [DashboardController::class, 'index']); 
    Route::get('/slip/{id}', [DashboardController::class, 'show']);  

    // Route สำหรับหน้า Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // หน้าแรก
    Route::get('/', function () {
        return redirect('/dashboard');
    });
});