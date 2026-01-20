<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO_S2K.png') }}">
    <title>รายชื่อพนักงาน - S2K Salary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header  text-white d-flex justify-content-between align-items-center" style="background-color: #ff8e16ff;">
                <h4 class="mb-0">👥 รายชื่อพนักงานในระบบ</h4>
                <a href="/admin/dashboard" class="btn btn-secondary text-black " style="background-color: #ffffffff;"  > ⬅กลับเมนูหลัก</a>
            </div>
            <div class="card-body">
                <form action="/admin/employees" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ หรือ รหัสพนักงาน..." value="{{ $search ?? '' }}">
                        <button class="btn btn-primary" type="submit">🔍 ค้นหา</button>
                        @if(isset($search))
                            <a href="/admin/employees" class="btn btn-outline-secondary">ล้างค่า</a>
                        @endif
                    </div>
                </form>

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>รหัส</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                        <tr>
                            <td class="fw-bold text-Dark">{{ $emp->emp_id }}</td>
                            <td>{{ $emp->name }}</td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" 
                                        onclick="openPasswordModal('{{ $emp->id }}', '{{ $emp->name }}')">
                                    <i class="bi bi-key-fill"></i> เปลี่ยนรหัส
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">ไม่พบข้อมูลพนักงาน</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="passwordForm" method="POST">
                    @csrf
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">🔑 เปลี่ยนรหัสผ่าน</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>กำลังเปลี่ยนรหัสผ่านให้: <strong id="empNameDisplay"></strong></p>
                        <div class="mb-3">
                            <label>รหัสผ่านใหม่ที่ต้องการ:</label>
                            <input type="text" name="new_password" class="form-control" placeholder="เช่น 1234, 8888" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openPasswordModal(userId, userName) {
            // เอา ID พนักงานไปใส่ใน Action ของ Form
            document.getElementById('passwordForm').action = '/admin/change-password/' + userId;
            // เอารายชื่อไปโชว์
            document.getElementById('empNameDisplay').innerText = userName;
            // เปิด Modal
            var myModal = new bootstrap.Modal(document.getElementById('passwordModal'));
            myModal.show();
        }
    </script>

</body>
</html>