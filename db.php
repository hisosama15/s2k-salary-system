<?php
// db.php : ไฟล์สำหรับเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP ปกติรหัสผ่านจะว่างไว้
$dbname = "s2k_salary";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตั้งค่าภาษาไทยให้ถูกต้อง
$conn->set_charset("utf8");

// ตรวจสอบการเชื่อมต่อ (ถ้าเชื่อมไม่ได้ให้แจ้งเตือน)
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
//} else {
    // ถ้าบรรทัดนี้ขึ้นแสดงบนหน้าเว็บ แปลว่าเชื่อมต่อสำเร็จ!
//    echo "เชื่อมต่อ Database สำเร็จแล้วจ้า! พร้อมลุยต่อ"; 
}
?>