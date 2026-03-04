<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO_S2K.png') }}">
    <title>สลิปเงินเดือน - {{ $slip->emp_id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { background: #e9ecef; font-family: 'Sarabun', sans-serif; padding: 20px; }
        .slip-container { background: white; width: 210mm; min-height: 148mm; margin: 0 auto; padding: 20px 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); position: relative; }
        .table-salary { width: 100%; border-collapse: collapse; font-size: 13px; }
        .table-salary th, .table-salary td { border-left: 1px solid #000; border-right: 1px solid #000; }
        .table-salary th { border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: center; padding: 5px; font-weight: bold; }
        .table-salary td { padding: 4px 8px; vertical-align: top; }
        .table-salary tr:last-child td { border-bottom: 1px solid #000; }
        .bg-orange { background-color: #ffd198ff !important; }
        .bg-blue { background-color: #abceffff !important; }
        .bg-yellow { background-color: #fff711ff !important; }
        .bg-green { background-color: #b0ebb2ff !important; }
        .box-info { border: 1px solid #000; text-align: center; margin-bottom: 10px; }
        .box-header { border-bottom: 1px solid #000; font-weight: bold; padding: 4px; font-size: 14px; }
        .box-content { padding: 8px; font-weight: bold; font-size: 14px; }
        .amount { text-align: right; }
        .emp-info { width: 100%; margin-bottom: 15px; font-size: 14px; }
        .emp-info td { padding: 4px 0; }
        .stat-text { color: #0d6efd !important; font-size: 12px !important; font-weight: normal; }
        @media print {
            body { background: white; padding: 0; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .slip-container { box-shadow: none; margin: 0; width: 100%; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    @php
        $cleanNum = function($val) { return (float)$val + 0; };
        $incomes = [];
        $incomes[] = ['name' => "วันทำงานปกติ", 'amount' => number_format($slip->work_days, 2)];
        if($slip->wage_rate > 0) { $incomes[] = ['name' => "อัตราค่าแรง " . number_format($slip->wage_rate, 2) . " บาท", 'amount' => number_format($slip->salary, 2)]; }
        if($slip->ot_1_5 > 0) $incomes[] = ['name' => "OT x 1.5 ({$slip->ot_1_5_hrs} ชม.)", 'amount' => number_format($slip->ot_1_5, 2)];
        if($slip->ot_1_0 > 0) $incomes[] = ['name' => "OT x 1.0 ({$slip->ot_1_0_hrs} ชม.)", 'amount' => number_format($slip->ot_1_0, 2)];
        if($slip->ot_2_0 > 0) $incomes[] = ['name' => "OT x 2.0 ({$slip->ot_2_0_hrs} ชม.)", 'amount' => number_format($slip->ot_2_0, 2)];
        if($slip->ot_3_0 > 0) $incomes[] = ['name' => "OT x 3.0 ({$slip->ot_3_0_hrs} ชม.)", 'amount' => number_format($slip->ot_3_0, 2)];
        if($slip->shift_allowance > 0) $incomes[] = ['name' => "ค่ากะ", 'amount' => number_format($slip->shift_allowance, 2)];
        if($slip->diligence > 0) $incomes[] = ['name' => "เบี้ยขยัน", 'amount' => number_format($slip->diligence, 2)];
        if($slip->living_allowance > 0) $incomes[] = ['name' => "ค่าครองชีพ", 'amount' => number_format($slip->living_allowance, 2)];
        if($slip->medical_allowance > 0) $incomes[] = ['name' => "ค่ารักษาพยาบาล", 'amount' => number_format($slip->medical_allowance, 2)];
        if($slip->trip_allowance > 0) $incomes[] = ['name' => "ค่าเที่ยว", 'amount' => number_format($slip->trip_allowance, 2)];
        if($slip->per_diem > 0) $incomes[] = ['name' => "เบี้ยเลี้ยง", 'amount' => number_format($slip->per_diem, 2)];
        if($slip->commission > 0) $incomes[] = ['name' => "ค่าคอมมิชชั่น", 'amount' => number_format($slip->commission, 2)];
        if($slip->bonus > 0) $incomes[] = ['name' => "โบนัส", 'amount' => number_format($slip->bonus, 2)];
        if($slip->other_income > 0) $incomes[] = ['name' => "รายได้อื่นๆ", 'amount' => number_format($slip->other_income, 2)];
        $deductions = [];
        if($slip->sso > 0) $deductions[] = ['name' => "ประกันสังคม", 'amount' => number_format($slip->sso, 2)];
        if($slip->tax > 0) $deductions[] = ['name' => "ภาษีเงินได้", 'amount' => number_format($slip->tax, 2)];
        if($slip->student_loan_deduction > 0) $deductions[] = ['name' => "กยศ.", 'amount' => number_format($slip->student_loan_deduction, 2)];
        if($slip->excess_leave_deduction > 0) $deductions[] = ['name' => "หักลาเกินสิทธิ์", 'amount' => number_format($slip->excess_leave_deduction, 2)];
        if($slip->late_deduction > 0) $deductions[] = ['name' => "หักมาสาย", 'amount' => number_format($slip->late_deduction, 2)];
        if($slip->absent_deduct > 0) $deductions[] = ['name' => "หักขาดงาน", 'amount' => number_format($slip->absent_deduct, 2)];
        if($slip->other_deduct > 0) $deductions[] = ['name' => "หักอื่นๆ", 'amount' => number_format($slip->other_deduct, 2)];
        if($slip->sick_leave > 0) $deductions[] = ['name' => "ป่วย ({$cleanNum($slip->sick_leave)} วัน)", 'amount' => '-', 'is_stat' => true];
        if($slip->sick_leave_no_cert > 0) $deductions[] = ['name' => "ป่วยไม่มีใบ ({$cleanNum($slip->sick_leave_no_cert)} วัน)", 'amount' => '-', 'is_stat' => true];
        if($slip->personal_leave > 0) $deductions[] = ['name' => "ลากิจ ({$cleanNum($slip->personal_leave)} ชม.)", 'amount' => '-', 'is_stat' => true];
        if($slip->annual_leave > 0) $deductions[] = ['name' => "พักร้อน ({$cleanNum($slip->annual_leave)} ชม.)", 'amount' => '-', 'is_stat' => true];
        if($slip->absent > 0) $deductions[] = ['name' => "ขาดงาน ({$cleanNum($slip->absent)} ชม.)", 'amount' => '-', 'is_stat' => true];
        if($slip->other_leave > 0) $deductions[] = ['name' => "ลาอื่นๆ ({$cleanNum($slip->other_leave)} ชม.)", 'amount' => '-', 'is_stat' => true];
        if($slip->late > 0) $deductions[] = ['name' => "สาย ({$cleanNum($slip->late)} นาที)", 'amount' => '-', 'is_stat' => true];
        if(!empty($slip->remark)) { $deductions[] = ['name' => "หมายเหตุ: {$slip->remark}", 'amount' => '', 'is_stat' => true]; }
        $maxRows = max(count($incomes), count($deductions), 1);
    @endphp

    <div class="container mb-3 no-print text-center">
        <a href="/dashboard" class="btn btn-secondary me-2">⬅ กลับ</a>
        <button onclick="window.print()" class="btn btn-warning me-2">🖨️ ปริ้น</button>
        <button onclick="downloadPDF()" class="btn text-bg-success">⬇️ โหลด PDF</button>
    </div>

    <div class="slip-container" id="slip-content">
        <div class="row mb-2 align-items-center">
            <div class="col-7 d-flex align-items-center gap-3">
                <img src="{{ asset('images/LOGO_S2K.png') }}" width="80" height="auto">
                <div><h5 class="fw-bold mb-0">บริษัท เอสทูเค ฟู้ดส์ จำกัด</h5><small>111 หมู่ 2 ต.หนองยาว อ.เมืองสระบุรี จ.สระบุรี 18000</small></div>
            </div>
            <div class="col-5 text-end"><h4 class="fw-bold p-2 d-inline-block">ใบแจ้งรายได้ / PAY SLIP</h4></div>
        </div>
        <hr style="border-top: 2px solid #000; opacity: 1;">
        <table class="emp-info">
            <tr>
                <td width="10%"><strong>รหัส:</strong></td><td width="40%">{{ $slip->emp_id }}</td>
                <td width="15%"><strong>ชื่อ-สกุล:</strong></td><td>{{ $slip->emp_name }}</td>
            </tr>
            <tr>
                <td><strong>งวดที่จ่าย:</strong></td><td>{{ \Carbon\Carbon::parse($slip->start_date)->addYears(543)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($slip->end_date)->addYears(543)->format('d/m/Y') }}</td>
                <td><strong>แผนก/ฝ่าย:</strong></td><td>{{ $slip->department ?? '-' }} &nbsp;|&nbsp; <strong>ประเภท:</strong> {{ $slip->salary_type ?? '-' }}</td>
            </tr>
        </table>
        <div class="row">
            <div class="col-9 pe-4">
                <table class="table-salary">
                    <thead><tr><th width="35%" class="bg-orange">รายได้ (Income)</th><th width="15%" class="bg-orange">จำนวนเงิน</th><th width="35%" class="bg-blue">รายการหัก (Deduction)</th><th width="15%" class="bg-blue">จำนวนเงิน</th></tr></thead>
                    <tbody>
                        @for ($i = 0; $i < $maxRows; $i++)
                            <tr>
                                <td>{{ $incomes[$i]['name'] ?? '' }}</td><td class="amount">{{ $incomes[$i]['amount'] ?? '' }}</td>
                                <td class="{{ isset($deductions[$i]['is_stat']) ? 'stat-text' : '' }}">{{ $deductions[$i]['name'] ?? '' }}</td>
                                <td class="amount {{ isset($deductions[$i]['is_stat']) ? 'stat-text' : '' }}">{{ $deductions[$i]['amount'] ?? '' }}</td>
                            </tr>
                        @endfor
                        <tr style="font-weight:bold; border-top:1px solid #000;">
                            <td class="text-end bg-orange">รวมรายได้</td><td class="amount bg-orange">{{ number_format($slip->total_income, 2) }}</td>
                            <td class="text-end bg-blue">รวมรายการหัก</td><td class="amount bg-blue text-danger">{{ number_format($slip->total_deduct, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-3 ps-0">
                <div class="box-info"><div class="box-header bg-yellow">วันที่จ่าย</div><div class="box-content">{{ \Carbon\Carbon::parse($slip->pay_date)->addYears(543)->format('d/m/Y') }}</div></div>
                <div class="box-info"><div class="box-header bg-green">เลขบัญชี</div><div class="box-content">{{ $slip->bank_account_no ?? '-' }}</div></div>
                <div class="box-info"><div class="box-header bg-green">ผู้จัดทำ</div><div class="box-content">{{ $slip->prepared_by ?? 'ADMIN' }}</div></div>
                <div class="border border-2 border-dark p-3 text-center bg-yellow mb-2"><h6 class="mb-1 fw-bold">เงินได้สุทธิ</h6><h2 class="fw-bold text-primary mb-0">{{ number_format($slip->net_salary, 2) }}</h2></div>
            </div>
        </div>
    </div>
    <script>function downloadPDF() { var element = document.getElementById('slip-content'); var opt = { margin: 0.2, filename: 'Payslip_{{ $slip->emp_id }}.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' } }; html2pdf().set(opt).from(element).save(); }</script>
</body>
</html>