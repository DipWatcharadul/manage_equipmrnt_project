<?php
session_start();
require_once('connect.php');

// Retrieve form data
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];

// ตรวจสอบว่า username ซ้ำหรือไม่
$sql_check_username = "SELECT * FROM users WHERE username = ?";
$stmt_check = $conn->prepare($sql_check_username);
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // หากชื่อผู้ใช้ซ้ำ, แสดงข้อความแจ้งเตือน
    echo "<script>alert('ชื่อผู้ใช้นี้ถูกใช้แล้ว กรุณาเลือกชื่อผู้ใช้อื่น'); window.location.href='register.php';</script>";
    exit();
}

// ตรวจสอบความถูกต้องของรหัสผ่าน (เช่น ความยาวขั้นต่ำหรือรูปแบบ)
if (strlen($password) < 8) {
    // หากรหัสผ่านไม่ตรงตามเงื่อนไข, แสดงข้อความแจ้งเตือน
    echo "<script>alert('รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร'); window.location.href='register.php';</script>";
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert data into the database (include create_at field)
$sql = "INSERT INTO users (username, password, email, fname, lname, urole, create_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$urole = 1; // Default user role, change as needed
$stmt->bind_param("sssssi", $username, $hashed_password, $email, $firstname, $lastname, $urole);
// Execute the statement
if ($stmt->execute()) {
    // หากสมัครสมาชิกสำเร็จ, แสดงข้อความแจ้งเตือน
    echo "<script>alert('สมัครสมาชิกสำเร็จ'); window.location.href='login.php';</script>";
    exit();
} else {
    // หากเกิดข้อผิดพลาด, แสดงข้อความแจ้งเตือน
    echo "<script>alert('เกิดข้อผิดพลาดในการสมัครสมาชิก'); window.location.href='register.php';</script>";
    exit();
}

?>
