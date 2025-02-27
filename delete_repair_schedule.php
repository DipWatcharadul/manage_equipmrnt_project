<?php
session_start();
require_once 'connect.php';

// Check if repair_schedule is logged in as admin
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

// Get repair_schedule ID from query parameter
$id = $_GET['id'];

// Prepare SQL query to delete repair_schedule
$sql = "DELETE FROM repair_schedule WHERE equipment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

// Execute the query
if ($stmt->execute()) {
    echo "<script>alert('ลบข้อมูลการซ่อมครุภัณฑ์');</script>";
    header('refresh:1;url=manage_repair_schedule.php');
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>