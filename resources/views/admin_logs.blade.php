<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO_S2K.png') }}">
    <title>ประวัติการใช้งานระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0">📜 ประวัติการใช้งาน</h4>
                </div>
                <a href="/admin/dashboard" class="btn btn-secondary btn-sm" style="background-color: #686766;">⬅ กลับเมนูหลัก</a>
            </div>
            
            <div class="card-body bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                <form action="/admin/logs" method="GET" class="d-flex align-items-center gap-2">
                    <label class="fw-bold"><i class="bi bi-funnel-fill"></i> เลือกปี:</label>
                    <select name="year" class="form-select w-auto border-dark" onchange="this.form.submit()">
                        <option value="">-- ดูทั้งหมด --</option>
                        @foreach($years_list as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                ปี {{ $y + 543 }} </option>
                        @endforeach
                    </select>
                    
                    @if(request('year'))
                        <a href="/admin/logs" class="btn btn-outline-danger btn-sm">❌ ล้างค่า</a>
                    @endif
                </form>

                <div class="d-flex gap-2">
                    <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="return confirm('⚠️ ยืนยันที่จะลบ Log ที่เก่ากว่า 30 วัน?')">
                        @csrf
                        <input type="hidden" name="type" value="old">
                        <button type="submit" class="btn btn-outline-warning btn-sm text-dark">
                            <i class="bi bi-trash"></i> ลบที่เก่ากว่า 30 วัน
                        </button>
                    </form>

                    <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="return confirm('‼️ ยืนยันที่จะลบประวัติทั้งหมด? (ข้อมูลจะหายถาวร)')">
                        @csrf
                        <input type="hidden" name="type" value="all">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-fire"></i> ล้าง Log ทั้งหมด
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 20%;">เวลา</th>
                                <th style="width: 20%;">ผู้ใช้งาน</th>
                                <th style="width: 15%;">การกระทำ (Action)</th>
                                <th>รายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>
                                    <i class="bi bi-clock"></i> 
                                    {{ $log->created_at->addYears(543)->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="fw-bold text-danger">{{ $log->user_name }}</td>
                                <td>
                                    @php
                                        $badgeColor = 'bg-secondary';
                                        if(str_contains($log->action, 'Login')) $badgeColor = 'bg-success';
                                        if(str_contains($log->action, 'Delete')) $badgeColor = 'bg-danger';
                                        if(str_contains($log->action, 'Import')) $badgeColor = 'bg-primary';
                                        if(str_contains($log->action, 'Password')) $badgeColor = 'bg-warning text-dark';
                                    @endphp
                                    <span class="badge {{ $badgeColor }}">{{ $log->action }}</span>
                                </td>
                                <td class="text-muted small">{{ $log->description }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    ไม่พบประวัติการใช้งาน
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $logs->appends(['year' => request('year')])->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>