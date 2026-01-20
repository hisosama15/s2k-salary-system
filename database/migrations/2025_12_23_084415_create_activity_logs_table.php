<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_name'); // ชื่อคนที่ทำรายการ (เผื่อ User ถูกลบไปแล้ว ชื่อจะยังอยู่)
            $table->string('action');    // ทำอะไร? (เช่น 'Login', 'Import', 'Delete')
            $table->text('description')->nullable(); // รายละเอียดเพิ่มเติม
            $table->timestamps(); // เก็บเวลาที่ทำ (created_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
