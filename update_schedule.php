<?php
// เริ่มต้นการใช้งาน session
session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

// ตรวจสอบว่ามีการส่งค่า 'equipment_id', 'repair_date', 'status_rs' มาหรือไม่
if (isset($_POST['id'], $_POST['equipment_id'], $_POST['repair_date'], $_POST['status_rs'], $_POST['repair_complete_date'])) {
    // รับค่าจากฟอร์ม
    $id = $_POST['id'];
    $equipment_id = $_POST['equipment_id'];
    $repair_date = $_POST['repair_date'];
    $status_rs = $_POST['status_rs'];
    $repair_complete_date = !empty($_POST['repair_complete_date']) ? $_POST['repair_complete_date'] : NULL;

    // อัปเดตข้อมูลกำหนดการซ่อม
    $sql_update = "UPDATE repair_schedule SET equipment_id = ?, repair_date = ?, status_rs = ?, repair_complete_date = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    
    // แก้ไขประเภทข้อมูลในการ bind_param
    $stmt_update->bind_param("isssi", $equipment_id, $repair_date, $status_rs, $repair_complete_date, $id);

    if ($stmt_update->execute()) {
        echo "อัปเดตการซ่อมแซมเรียบร้อยแล้ว";
        header('refresh:1;url=manage_repair_schedule.php');
    } else {
        echo "Error: " . $stmt_update->error;
    }

    $stmt_update->close();
} else {
    // ถ้ามีข้อมูลที่จำเป็นไม่ถูกส่งมาครบ
    echo "ข้อผิดพลาด: ไม่มีข้อมูล";
    header('refresh:2;url=manage_repair_schedule.php');
}
?>
