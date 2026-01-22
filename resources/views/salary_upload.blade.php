<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO_S2K.png') }}">
    <title>ระบบนำเข้าเงินเดือน S2K</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        
        <div class="card shadow">
            <div class="card-header  text-white d-flex justify-content-between align-items-center" style="background-color: #ff8e16ff;">
                <h4 class="mb-0">📂 ระบบนำเข้าเงินเดือน</h4>
    
                 <a href="/admin/dashboard" class="btn btn-light btn-sm fw-bold ">
                     ⬅ กลับเมนูหลัก
                 </a>
            </div>

            <div class="card-body">
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                    <form action="/import" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="fw-bold">1. ระบุงวดเดือน/ปี (พ.ศ.)</label>
                                <div class="input-group">
                                    <select name="month" class="form-select bg-light">
                                        @php $th_months = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"]; @endphp
                                        @for($i=1; $i<=12; $i++) 
                                            <option value="{{$i}}" {{ $i == date('n') ? 'selected' : '' }}>{{ $th_months[$i] }}</option> 
                                        @endfor
                                    </select>
                                    <select name="year_th" class="form-select bg-light">
                                        @php 
                                            $cur_year = date('Y') + 543; // ปีปัจจุบัน (2568)
                                            $start_year = $cur_year + 1; // เผื่ออนาคตให้ 1 ปี (2569)
                                            $end_year = $cur_year - 5;   // ย้อนหลังได้ 5 ปี
                                        @endphp

                                        @for($y=$start_year; $y>=$end_year; $y--) 
                                            <option value="{{$y}}" {{ $y == $cur_year ? 'selected' : '' }}>
                                                {{$y}}
                                            </option> 
                                        @endfor
                                    </select>
                                </div>
                                <small class="text-muted">*ใช้สำหรับระบุหัวกระดาษสลิป</small>
                            </div>

                            <div class="col-md-5">
                                <label class="fw-bold text-danger">2. งวดที่จ่าย (ตัดวิก)</label>
                                <div class="input-group">
                                    <span class="input-group-text">จาก</span>
                                    <input type="date" name="start_date" class="form-control" required>
                                    <span class="input-group-text">ถึง</span>
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                                <small class="text-muted">เช่น 26/11/2568 - 25/12/2568</small>
                            </div>

                            <div class="col-md-3">
                                <label class="fw-bold text-success">3. วันที่จ่าย</label>
                                <input type="date" name="pay_date" class="form-control border-success" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold text-primary">4. ประเภทการจ่ายเงิน (Payment Type)</label>
                            <select name="payment_type" class="form-select border-primary" required>
                                <option value="รายวัน">พนักงานรายวัน (Daily)</option>
                                <option value="รายเดือน">พนักงานรายเดือน (Monthly)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">5. เลือกไฟล์ Excel (.csv)</label> <input type="file" name="file" class="form-control" required>
                        </div>


                        <button type="submit" class="btn w-50 py-2 text-white d-block mx-auto" style="background-color: #00b503ff;">
                            <i class="bi bi-cloud-upload-fill"></i> อัปโหลดและประมวลผล
                        </button>
                    </form>

                <hr class="my-4">

                <div class="alert alert-warning">
                    <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> ลบข้อมูลที่ผิดพลาด</h5>
                    <p class="mb-0">หากอัปโหลดผิด สามารถเลือกลบข้อมูลทั้งเดือนได้ที่นี่ (ข้อมูลจะหายไปทั้งหมด)</p>
                    
                    <form action="/salary/delete" method="POST" class="mt-3 row g-2 align-items-center" onsubmit="return confirm('⚠️ ยืนยันที่จะลบข้อมูลงวดนี้ทั้งหมด? \n(ข้อมูลพนักงานทุกคนในงวดนี้จะหายไป)');">
                        @csrf
                        
                        <div class="col-auto">
                            <label class="col-form-label fw-bold">เลือกงวดที่จะลบ:</label>
                        </div>

                        <div class="col-md-6">
                            <select name="pay_date" class="form-select text-danger fw-bold">
                                <option value="">-- กรุณาเลือกงวดที่ต้องการลบ --</option>
                                
                                @foreach($history_dates as $index => $history)
                                    @php
                                        // แปลงวันที่เป็นไทย (พ.ศ.)
                                        $date_obj = \Carbon\Carbon::parse($history->pay_date);
                                        $year_th = $date_obj->year + 543;
                                        $date_th = $date_obj->format('d/m') . '/' . $year_th;
                                        
                                        // เช็คว่าเป็นตัวแรกสุดไหม (ถ้าใช่ ให้ขึ้นว่า ล่าสุด)
                                        $is_latest = ($index === 0) ? ' (✨ ล่าสุด/New)' : '';
                                    @endphp

                                    <option value="{{ $history->pay_date }}">
                                        งวดจ่าย {{ $date_th }} (มีข้อมูล {{ $history->count }} คน){{ $is_latest }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="col-auto">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash3-fill"></i> ลบทิ้ง
                            </button>
                        </div>
                    </form>
                </div>

                <hr>
                <h5>รายการล่าสุดในระบบ</h5>
                <table class="table table-bordered table-striped">
                    <thead><tr><th>รหัส</th><th>ชื่อ</th><th>เงินเดือน</th><th>สุทธิ</th></tr></thead>
                    <tbody>
                        @foreach($slips as $slip)
                        <tr>
                            <td>{{ $slip->emp_id }}</td>
                            <td>{{ $slip->emp_name }}</td>
                            <td>{{ number_format($slip->salary, 2) }}</td>
                            <td class="fw-bold text-success">{{ number_format($slip->net_salary, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>