<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO_S2K.png') }}">
    <title>ผู้จัดการ - S2K Salary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    
    {{-- ✅ ส่วนหัว Navbar (แก้ใหม่ให้มีปุ่ม Profile) --}}
    <nav class="navbar navbar-dark mb-4" style="background-color: #ff8e16ff;" >
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/LOGO_S2K.png') }}" width="80" height="auto" alt="Company Logo">
                <span class="navbar-brand fw-bold mb-0 h1 d-none d-md-block">Admin Control Panel</span>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                {{-- 1. ดึงชื่อจริงมาโชว์ (เช่น สวัสดี, นายธนบดี) --}}
                <span class="text-white d-none d-md-block me-2">สวัสดี, {{ Auth::user()->name }}</span>
                
                {{-- 2. ปุ่มแก้ไขข้อมูลส่วนตัว (กดตรงนี้เพื่อไปเปลี่ยนชื่อ) --}}
                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-light text-dark fw-bold shadow-sm" title="แก้ไขข้อมูลส่วนตัว">
                    <i class="bi bi-person-fill-gear"></i> <span class="d-none d-sm-inline">ข้อมูลส่วนตัว</span>
                </a>

                {{-- 3. ปุ่มออก --}}
                <a href="/logout" class="btn btn-sm btn-danger shadow-sm">
                    <i class="bi bi-box-arrow-right"></i> ออก
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h3 class="mb-4">เมนูจัดการระบบ</h3>
        
        <div class="row justify-content-center ">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-file-earmark-spreadsheet text-success" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">นำเข้าเงินเดือน</h4>
                        <p class="text-muted">อัปโหลดไฟล์ Excel เพื่อจ่ายเงินเดือน</p>
                        <a href="/import" class="btn btn-success w-100 stretched-link">ไปที่หน้า Import</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">ข้อมูลพนักงาน</h4>
                        <p class="text-muted">ดูรายชื่อ, ค้นหา และรีเซ็ตรหัสผ่าน</p>
                        
                        <a href="/admin/employees" class="btn btn-primary w-100 stretched-link">
                            จัดการพนักงาน
                        </a>
                    </div>
                </div>
            </div>

             <div class="col-md-4 ">
                <div class="card shadow-sm h-100 ">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-clock-history text-danger" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">ประวัติการใช้งาน</h4>
                        <p class="text-muted">ตรวจสอบ Log ใครทำอะไร เมื่อไหร่</p>
                        <a href="/admin/logs" class="btn btn-danger w-100 stretched-link">ดูประวัติ</a>
                    </div>
                </div>
             </div>
        </div>
    </div>

    <script>
        let idleTime = 0;
        const timeoutMinutes = 15; 
        const timeoutMilliseconds = timeoutMinutes * 60 * 1000;

        function resetTimer() {
            idleTime = 0;
        }

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