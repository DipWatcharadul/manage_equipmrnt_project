<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "uploads/"; // โฟลเดอร์สำหรับเก็บไฟล์ที่อัปโหลด
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // ตรวจสอบว่ามีการอัปโหลดไฟล์จริงหรือไม่
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            echo "ไฟล์เป็นภาพ - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "ไฟล์ไม่ใช่ภาพ.";
            $uploadOk = 0;
        }
    }

    // ตรวจสอบว่ามีไฟล์เดียวกันอยู่แล้วหรือไม่
    if (file_exists($target_file)) {
        echo "ขออภัย! ไฟล์นี้มีอยู่แล้ว.";
        $uploadOk = 0;
    }

    // ตรวจสอบขนาดไฟล์ (ตั้งไว้ที่ 2MB)
    if ($_FILES["profile_picture"]["size"] > 2000000) {
        echo "ขออภัย! ไฟล์ของคุณใหญ่เกินไป.";
        $uploadOk = 0;
    }

    // ตรวจสอบชนิดไฟล์
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "ขออภัย! เฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้นที่อนุญาต.";
        $uploadOk = 0;
    }

    // เช็คว่าทุกอย่างเรียบร้อยและอัปโหลดไฟล์
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            echo "ไฟล์ ". htmlspecialchars(basename($_FILES["profile_picture"]["name"])). " ถูกอัปโหลดแล้ว.";
        } else {
            echo "ขออภัย! มีปัญหาในการอัปโหลดไฟล์ของคุณ.";
        }
    }
}
?>
