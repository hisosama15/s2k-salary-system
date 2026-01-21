<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO_S2K.png') }}">
    <title>Dashboard - S2K Salary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
    /* เอฟเฟกต์หัวใจเต้นเรียกร้องความสนใจ */
    .badge-pulse {
        animation: pulse-animation 2s infinite;
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }

    @keyframes pulse-animation {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(243, 61, 20, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    
    .summary-box {
        transition: transform 0.2s;
    }
    .summary-box:hover {
        transform: translateY(-2px);
    }
</style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark shadow-sm mb-4" style="background-color: #ff8e16ff;" >
        <div class="container">
            <span class="navbar-brand fw-bold">
                <img src="{{ asset('images/LOGO_S2K.png') }}" width="80" height="auto" alt="Company Logo">
                <i class="bi bi-wallet2"></i> Salary
            </span>
            <div class="d-flex text-white align-items-center gap-2">
                <i class="bi bi-person"></i>
                <span class="me-2 d-none d-md-inline">สวัสดี, {{ Auth::user()->name }}</span>
                
                <button type="button" class="btn btn-sm btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="bi bi-key"></i> <span class="d-none d-sm-inline">เปลี่ยนรหัส</span>
                </button>

                <a href="/logout" class="btn btn-sm btn-danger fw-bold">ออก</a>
            </div>
        </div>
    </nav>

    <div class="container mb-4">
        <div class="row g-3">
            
            <div class="col-md-5">
                <div class="card text-white shadow-sm h-100" style="background-color: #ff8e16ff;">
                    <div class="card-body">
                        <form action="/dashboard" method="GET" id="rangeForm" class="d-flex justify-content-between align-items-center mb-2" >
                            <input type="hidden" name="year" value="{{ $selected_year }}">

                            <h6 class="card-title opacity-75 mb-0"><i class="bi bi-clock-history"></i> ยอดรับสุทธิ</h6>
                            
                            <select name="range" class="form-select form-select-sm w-auto text-black fw-bold" onchange="document.getElementById('rangeForm').submit()">
                                <option value="3" {{ $range == 3 ? 'selected' : '' }}>3 เดือนล่าสุด</option>
                                <option value="6" {{ $range == 6 ? 'selected' : '' }}>6 เดือนล่าสุด</option>
                            </select>
                        </form>

                        <h2 class="fw-bold my-3 text-center">{{ number_format($total_recent, 2) }} ฿</h2>
                        <div class="text-center">
                            <small>รวมจาก {{ $range }} รายการล่าสุดของคุณ</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body">
                        <form action="/dashboard" method="GET" id="yearForm" class="d-flex justify-content-between align-items-center mb-3">
                            <input type="hidden" name="range" value="{{ $range ?? 3 }}">

                            <h6 class="text-black mb-0 fw-bold"><i class="bi bi-calendar-check"></i> สรุปรายได้ปี {{ $selected_year + 543 }}</h6>
                            
                            <select name="year" class="form-select form-select-sm w-auto border-primary" onchange="document.getElementById('yearForm').submit()">
                                @foreach($years_list as $y)
                                    <option value="{{ $y }}" {{ $selected_year == $y ? 'selected' : '' }}>
                                        ปี {{ $y + 543 }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 border rounded bg-light summary-box h-100">
                                    <small class="text-muted d-block">💰 เงินได้สะสม</small>
                                    <span class="fw-bold text-primary fs-5">{{ number_format($total_year_income, 2) }}</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="p-2 border rounded bg-light summary-box h-100">
                                    <small class="text-muted d-block">💸 เงินหักสะสม</small>
                                    <span class="fw-bold text-danger fs-5">{{ number_format($total_year_deduct, 2) }}</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="p-2 border rounded bg-light summary-box h-100">
                                    <small class="text-muted d-block">🏦 ภาษีสะสม</small>
                                    <span class="fw-bold text-dark fs-5">{{ number_format($total_year_tax, 2) }}</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="p-2 border rounded bg-light summary-box h-100">
                                    <small class="text-muted d-block">🏥 ประกันสังคมสะสม</small>
                                    <span class="fw-bold text-info fs-5">{{ number_format($total_year_sso, 2) }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    

    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-black fw-bold">
                    <i class="bi bi-list-ul"></i> ประวัติการรับเงินเดือน (ปี {{ $selected_year + 543 }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>งวดเดือน</th>
                                <th>วันที่จ่าย</th>
                                <th class="text-end text-success">รายรับรวม</th>
                                <th class="text-end text-danger">รายการหัก</th>
                                <th class="text-end fw-bold">รับสุทธิ</th>
                                <th class="text-center">สลิป</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($slips as $slip)
                            <tr class="{{ $loop->first ? 'table-warning' : '' }}" 
                                onclick="window.location.href='/slip/{{ $slip->id }}'" 
                                style="cursor: pointer;">
                                
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $slip->month }}/{{ $slip->year }}
                                    </span>

                                    @if($loop->first)
                                        <span class="badge bg-danger text-white ms-2 badge-pulse">
                                            ✨ ใหม่! 
                                        </span>
                                    @endif
                                </td>
                                
                                <td>{{ \Carbon\Carbon::parse($slip->pay_date)->format('d/m/Y') }}</td>
                                <td class="text-end text-success">{{ number_format($slip->total_income, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($slip->total_deduct, 2) }}</td>
                                <td class="text-end fw-bold fs-5 text-primary">{{ number_format($slip->net_salary, 2) }}</td>
                                
                                <td class="text-center">
                                    <a href="/slip/{{ $slip->id }}" class="btn {{ $loop->first ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                        <i class="bi bi-file-earmark-text"></i> ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    ยังไม่มีข้อมูลเงินเดือนสำหรับปี {{ $selected_year + 543 }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #ff8e16ff;">
                    <h5 class="modal-title"><i class="bi bi-shield-lock"></i> เปลี่ยนรหัสผ่านส่วนตัว</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="/change-password" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger p-2 mb-3">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">รหัสผ่านปัจจุบัน</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label text-primary fw-bold">รหัสผ่านใหม่</label>
                            <input type="password" name="new_password" class="form-control" placeholder="อย่างน้อย 4 ตัวอักษร" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-primary fw-bold">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="พิมพ์รหัสใหม่อีกครั้ง" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn text-bg-success">บันทึกรหัสผ่านใหม่</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @if($errors->any())
    <script>
        var myModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
        myModal.show();
    </script>
    @endif

    <script>
        let idleTime = 0;
        const timeoutMinutes = 15; 
        const timeoutMilliseconds = timeoutMinutes * 60 * 1000;

        function resetTimer() { idleTime = 0; }

        document.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onmousedown = resetTimer;
        document.ontouchstart = resetTimer;
        document.onclick = resetTimer;
        document.onkeypress = resetTimer;

        setInterval(function() {
            idleTime += 60000;
            if (idleTime >= timeoutMilliseconds) {
                alert('⏳ หมดเวลาการเชื่อมต่อ! ระบบจะออกจากระบบอัตโนมัติเพื่อความปลอดภัย');
                window.location.href = '/logout';
            }
        }, 60000); 
    </script>
    
</body>
</html>