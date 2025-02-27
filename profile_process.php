<?php
session_start();
require_once('connect.php');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $address_name = isset($_POST['address_name']) ? $_POST['address_name'] : null;

    // ตรวจสอบว่าค่าที่ได้รับไม่ว่าง
    if (!empty($address_name)) {
        // เตรียม statement สำหรับการบันทึกข้อมูล
        $stmt = $conn->prepare("INSERT INTO profiles (address_name) VALUES (?)");
        $stmt->bind_param("s", $address_name);

        // ดำเนินการ statement
        if ($stmt->execute()) {
            echo "บันทึกข้อมูลสำเร็จ";
        } else {
            echo "เกิดข้อผิดพลาด: " . $stmt->error;
        }

        // ปิด statement
        $stmt->close();
    } else {
        echo "กรุณากรอกข้อมูลให้ครบถ้วน";
    }
}

$stmt->close();
$conn->close();
?>
