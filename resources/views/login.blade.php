<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO_S2K.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - S2K Salary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffffff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .brand-header {
            background-color: #ff8e16ff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
    </style>
</head>
<body>

    <div class="card login-card">
        <div class="brand-header">
            <br>
            <img src="{{ asset('images/LOGO_S2K.png') }}" width="120" height="auto" alt="Company Logo">
            <h3 class="mb-0 fw-bold"></h3>
            <br>
            <h5>ระบบสลิปเงินเดือนพนักงาน</h5>
        </div>
        <div class="card-body p-4">
            
            @if($errors->any())
                <div class="alert alert-danger text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-muted">รหัสพนักงาน</label>
                    <input type="text" name="emp_id" class="form-control form-control-lg" required autofocus placeholder="รหัสประจำตัวเช่น 12457">
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control form-control-lg" required placeholder="กรุณาใส่รหัสผ่าน">
                </div>

                <button type="submit" class="btn w-100 btn-lg mt-3 text-white" 
                        style="background-color: #ff8e16ff; border-color: #ff8e16ff;">
                        เข้าสู่ระบบ
                </button>
            </form>
        </div>
        <div class="card-footer text-center py-3 bg-white text-muted border-top-0">
            <h6>🔔ติดปัญหาการใช้งาน ติดต่อฝ่ายบุคคล🔔</h6>
        </div>
    </div>

</body>
</html>