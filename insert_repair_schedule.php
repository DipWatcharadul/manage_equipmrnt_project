<?php
session_start();
require_once 'connect.php';

// ตรวจสอบว่า login แล้วหรือยัง
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

$id_user = $_SESSION['admin_login'];

$equipment_id = $_POST['equipment_id'];
$repair_date = $_POST['repair_date'];
$status_rs = $_POST['status'];

// ตรวจสอบว่า equipment_id มีอยู่ในตาราง equipment หรือไม่
$sql_check = "SELECT id_equipment FROM equipment WHERE id_equipment = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $equipment_id); // "i" สำหรับ integer
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // equipment_id มีอยู่ในตาราง equipment แล้ว ทำการเพิ่มข้อมูล
    $sql = "INSERT INTO repair_schedule (equipment_id, repair_date, status_rs) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $equipment_id, $repair_date, $status_rs); // "iss" สำหรับ integer, string, string

    if ($stmt->execute()) {
        echo "<script>alert('เพิ่มกำหนดการซ่อมครุภัณฑ์สำเร็จ');</script>";
        header('refresh:1;url=manage_repair_schedule.php');
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    // หาก equipment_id ไม่พบในตาราง equipment
    echo "<script>alert('ไม่พบอุปกรณ์ที่เลือกในระบบ');</script>";
    header('refresh:1;url=manage_repair_schedule.php');
}

$stmt_check->close();
?>
