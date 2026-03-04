<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO_S2K.png') }}">
    <title>ระบบนำเข้าเงินเดือน S2K</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="card shadow border-0">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #ff8e16ff;">
                <h4 class="mb-0"><i class="bi bi-file-earmark-excel-fill"></i> ระบบนำเข้าเงินเดือน</h4>
                <a href="/admin/dashboard" class="btn btn-light btn-sm fw-bold">⬅ กลับเมนูหลัก</a>
            </div>
            <div class="card-body">
                @if(session('success'))<div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>@endif
                <form action="/import" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold mb-1">1. ระบุงวดเดือน/ปี (พ.ศ.)</label>
                            <div class="input-group">
                                <select name="month" class="form-select bg-white">@php $th_months = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"]; @endphp @for($i=1; $i<=12; $i++) <option value="{{$i}}" {{ $i == date('n') ? 'selected' : '' }}>{{ $th_months[$i] }}</option> @endfor </select>
                                <select name="year_th" class="form-select bg-white">@php $cur_year_th = date('Y') + 543; @endphp @for($y = $cur_year_th + 1; $y >= $cur_year_th - 5; $y--) <option value="{{$y}}" {{ $y == $cur_year_th ? 'selected' : '' }}>{{$y}}</option> @endfor </select>
                            </div>
                        </div>
                        <div class="col-md-5"><label class="fw-bold mb-1 text-danger">2. งวดที่จ่าย (ตัดวิก)</label><div class="input-group"><span class="input-group-text">จาก</span><input type="date" name="start_date" class="form-control" required><span class="input-group-text">ถึง</span><input type="date" name="end_date" class="form-control" required></div></div>
                        <div class="col-md-3"><label class="fw-bold mb-1 text-success">3. วันที่จ่ายเงิน</label><input type="date" name="pay_date" class="form-control border-success" value="{{ date('Y-m-d') }}" required></div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6"><label class="fw-bold mb-1 text-primary">4. ประเภทการจ่ายเงิน</label><select name="payment_type" class="form-select border-primary" required><option value="รายวัน">🟢 พนักงานรายวัน (Daily)</option><option value="รายเดือน">🔵 พนักงานรายเดือน (Monthly)</option></select></div>
                        <div class="col-md-6"><label class="fw-bold mb-1">5. เลือกไฟล์ Excel (.xlsx หรือ .csv)</label><input type="file" name="file" class="form-control" accept=".xlsx, .csv" required></div>
                    </div>
                    <div class="text-center"><button type="submit" class="btn btn-success btn-lg px-5 shadow-sm"><i class="bi bi-cloud-arrow-up-fill"></i> เริ่มอัปโหลดและประมวลผล</button></div>
                </form>
                <hr class="my-5">
                <div class="p-4 rounded-3 border border-warning bg-light shadow-sm">
                    <h5 class="text-warning-emphasis fw-bold mb-3"><i class="bi bi-clock-history"></i> ลบข้อมูลตามงวดวันที่ (Period Delete)</h5>
                    <form action="/salary/delete" method="POST" class="row g-3" onsubmit="return confirm('⚠️ ยืนยันที่จะลบ? ข้อมูลจะหายถาวร');">
                        @csrf
                        <div class="col-md-9"><select name="delete_target" class="form-select border-danger fw-bold" required><option value="">-- เลือกรอบงวดวันที่ที่ต้องการลบ --</option>@foreach($history_dates as $index => $history) @php $start_th = \Carbon\Carbon::parse($history->start_date)->addYears(543)->format('d/m/Y'); $end_th = \Carbon\Carbon::parse($history->end_date)->addYears(543)->format('d/m/Y'); @endphp <option value="{{ $history->start_date }}|{{ $history->end_date }}">📅 งวดที่จ่าย: {{ $start_th }} ถึง {{ $end_th }} (รวมทั้งหมด {{ $history->count }} รายการ)</option> @endforeach </select></div>
                        <div class="col-md-3"><button type="submit" class="btn btn-danger w-100 fw-bold">ลบข้อมูลทั้งงวดนี้</button></div>
                    </form>
                </div>
                <div class="mt-5">
                    <h5 class="fw-bold mb-3 text-secondary">รายการนำเข้าล่าสุด 10 รายการ</h5>
                    <div class="table-responsive">
                        <table class="table table-hover border">
                            <thead class="table-secondary"><tr><th>รหัสพนักงาน</th><th>ชื่อ-นามสกุล</th><th class="text-center">ประเภท</th><th class="text-end">เงินเดือน</th><th class="text-end">สุทธิ</th></tr></thead>
                            <tbody>
                                @forelse($slips as $slip)
                                <tr>
                                    <td class="fw-bold">{{ $slip->emp_id }}</td><td>{{ $slip->emp_name }}</td>
                                    <td class="text-center"><span class="badge {{ $slip->salary_type == 'รายเดือน' ? 'bg-primary' : 'bg-info text-dark' }}">{{ $slip->salary_type ?? 'ไม่ระบุ' }}</span></td>
                                    <td class="text-end">{{ number_format($slip->salary, 2) }}</td><td class="text-end fw-bold text-success">{{ number_format($slip->net_salary, 2) }}</td>
                                </tr>
                                @empty<tr><td colspan="5" class="text-center text-muted py-4">ยังไม่มีข้อมูลในระบบ</td></tr>@endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>