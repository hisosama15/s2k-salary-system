<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

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
    Route::post('/admin/change-password/{id}', [SalaryController::class, 'changePassword']);
    Route::get('/admin/logs', [App\Http\Controllers\ActivityLogController::class, 'index']);
    

    // หน้า Dashboard พนักงาน (เดี๋ยวสร้าง EP.หน้า)
    Route::get('/dashboard', [DashboardController::class, 'index']); // หน้าตารางรวม
    Route::get('/slip/{id}', [DashboardController::class, 'show']);  // หน้าดูรายละเอียด

    // หน้าแรก: ถ้าเข้าเว็บมาเฉยๆ ให้เด้งไปหน้า Login
    Route::get('/', function () {
        return redirect('/dashboard');
    });
});