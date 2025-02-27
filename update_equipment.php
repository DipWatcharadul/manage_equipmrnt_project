<?php
require_once 'connect.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Get form data
    $id = $_POST['id_equipment'];
    $name = $_POST['name'];
    $purchase = $_POST['purchase'];
    $description = $_POST['description'];
    $remarks = $_POST['remarks'];  // Capture remarks from the form
    $status = $_POST['status'];
    $address = $_POST['address']; // New address field
    $year = $_POST['year']; // Capture the year from the form
    $image = $_FILES['image']['name'];

    // Fetch current equipment details
    $sql = "SELECT * FROM equipment WHERE id = $id";
    $result = $conn->query($sql);
    $equipment = $result->fetch_assoc();

    // Handle file upload
    if ($image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $target_file = $equipment['image']; // Keep the existing image if no new one is uploaded
    }

    // Update the equipment details including 'year'
    $sql = "UPDATE equipment 
            SET name='$name', purchase_date='$purchase', description='$description', remarks='$remarks', 
                status='$status', address='$address', image='$target_file', year='$year' 
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('แก้ไขครุภัณฑ์สำเร็จ');</script>";
        header('refresh:1;url=manage_equipment.php');
    } else {
        echo "เกิดข้อผิดพลาดในการอัพเดตบันทึก: " . $conn->error;
    }
}
?>
