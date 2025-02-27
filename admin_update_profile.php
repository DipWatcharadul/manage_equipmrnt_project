<?php
session_start(); // เริ่มต้นเซสชันเพื่อใช้งาน user_id

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // เชื่อมต่อฐานข้อมูล
        $pdo = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ตรวจสอบว่า ID ผู้ใช้ถูกตั้งค่าในเซสชัน
        if (!isset($_SESSION['admin_login'])) {
            throw new Exception("กรุณาล็อกอินก่อน");
        }
        
        $user_id = $_SESSION['admin_login'];  
        // รับข้อมูลจากฟอร์มและทำความสะอาดข้อมูล
        $fname = htmlspecialchars(trim($_POST['fname']));
        $lname = htmlspecialchars(trim($_POST['lname']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $address = htmlspecialchars(trim($_POST['address']));

        // ถ้ามีการอัพโหลดรูปโปรไฟล์
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $profile_image = 'uploads/' . basename($_FILES['profile_image']['name']);
            // ตรวจสอบประเภทไฟล์
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['profile_image']['type'], $allowedTypes)) {
                // ตรวจสอบขนาดไฟล์ไม่เกิน 2MB
                if ($_FILES['profile_image']['size'] <= 2000000) {
                    move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image);
                } else {
                    throw new Exception("ขนาดไฟล์ใหญ่เกินไป");
                }
            } else {
                throw new Exception("ไฟล์ไม่รองรับรูปแบบ");
            }
        } else {
            // ใช้รูปเดิมถ้าไม่อัปโหลดใหม่
            $profile_image = isset($userProfile['profile_image']) ? $userProfile['profile_image'] : 'uploads/default.png';
        }

        // อัพเดทข้อมูลในฐานข้อมูล
        $stmt = $pdo->prepare("UPDATE users SET fname = :fname, lname = :lname, email = :email, phone = :phone, address = :address, profile_image = :profile_image WHERE id_user = :id_user");
        $stmt->execute([
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'profile_image' => $profile_image,
            'id_user' => $user_id
        ]);

        // แสดงข้อความว่าอัพเดทสำเร็จ
        echo "ข้อมูลของคุณได้รับการอัพเดทเรียบร้อย";
    } catch (Exception $e) {
        // แสดงข้อความข้อผิดพลาด
        echo "Error: " . $e->getMessage();
    }
}
?>
