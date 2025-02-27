<?php
session_start();

require_once 'connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    // Get form data
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $description = $_POST['description'];
    $remarks = $_POST['remarks'];
    $purchase_date = $_POST['purchase'];
    $status = $_POST['status'];
    $address = $_POST['address'];
    $category = $_POST['category'];
    $year = $_POST['year']; //echo $year; die;
    $ts = date('Y-m-d H:i:s');
    echo $_SESSION['admin_login']; //die;
    $user_id = $_SESSION['admin_login'] ?? null;  // เพิ่ม user_id (ต้องมี session start)

    // ตรวจสอบว่ามี category หรือไม่
    if (empty($category)) {
        echo "<script>alert('กรุณากรอกหมวดหมู่');</script>";
        exit();
    }

    // สุ่ม bib number
    $bib = rand(10000, 99999);

    // ตรวจสอบว่ามีไฟล์ภาพอัปโหลดหรือไม่
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "uploads/";
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_file_name = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_file_name;

        // ตรวจสอบว่าเป็นไฟล์ภาพจริงหรือไม่
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // ย้ายไฟล์ไปยังโฟลเดอร์ uploads
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = $new_file_name;

                // **เพิ่มคอลัมน์ที่ขาดหาย**
                $sql = "INSERT INTO equipment (name, description, purchase_date, status_e, created_at,image, bib, remarks, address, brand, model, category, user_id, byear) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                //echo $sql; die;

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssssssssi", $name, $description, $purchase_date, $status, $ts, $image_url, $bib, $remarks, $address, $brand, $model, $category, $user_id, $year);

                if ($stmt->execute()) {
                    echo "<script>alert('เพิ่มครุภัณฑ์สำเร็จ');</script>";
                    header('refresh:1;url=manage_equipment.php');
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }

                // ปิด statement
                $stmt->close();
            } else {
                echo "ขออภัย, มีข้อผิดพลาดในการอัปโหลดไฟล์ของคุณ";
            }
        } else {
            echo "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ";
        }
    } else {
        echo "กรุณาอัปโหลดไฟล์รูปภาพ";
    }
}
?>
