<?php
session_start();
include('connect.php'); // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $date_completed = $_POST['date_completed'];

    // คำสั่ง SQL สำหรับอัปเดตวันที่เสร็จสิ้น
    $query = "UPDATE equipment SET date_completed = '$date_completed' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('อัปเดตวันที่เสร็จสิ้นเรียบร้อยแล้ว'); window.location.href='view_equipment.php';</script>";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
mysqli_close($conn);
?>
