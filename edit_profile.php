<?php
require_once 'connect.php'; // เชื่อมต่อฐานข้อมูล
session_start();

if (!isset($_SESSION['user_login'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_login'];
$successMessage = '';
$errorMessage = '';

// ฟังก์ชันสำหรับดึงข้อมูลโปรไฟล์ผู้ใช้
function getUserProfile($conn, $userId) {
    $sql = "SELECT id_user, username, fname, lname, profile_image, email, phone, job_position, 
                   company, address, birth_date, gender 
            FROM users WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ฟังก์ชันสำหรับคำนวณอายุ
function calculateAge($birthDate) {
    if (!$birthDate) return "ไม่มีข้อมูลวันเกิด";
    try {
        $birthDate = new DateTime($birthDate);
        $today = new DateTime();
        return $today->diff($birthDate)->y;
    } catch (Exception $e) {
        return "ข้อมูลวันเกิดไม่ถูกต้อง";
    }
}

// ฟังก์ชันสำหรับอัปโหลดรูปภาพโปรไฟล์
function uploadProfileImage($file) {
    $targetDir = "uploads/";
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $targetFile = $targetDir . uniqid() . '.' . $imageFileType;

    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) return "ไฟล์ไม่รองรับ";
    if ($file["size"] > 500000) return "ขนาดไฟล์ใหญ่เกินไป";

    return move_uploaded_file($file["tmp_name"], $targetFile) ? $targetFile : "ไม่สามารถอัปโหลดไฟล์ได้";
}

// ดึงข้อมูลโปรไฟล์ผู้ใช้
$userProfile = getUserProfile($conn, $userId);

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : '';
    $lname = isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
    $job_position = isset($_POST['job_position']) ? htmlspecialchars($_POST['job_position']) : '';
    $company = isset($_POST['company']) ? htmlspecialchars($_POST['company']) : '';
    $address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
    $birth_date = isset($_POST['birth_date']) ? htmlspecialchars($_POST['birth_date']) : '';
    $gender = isset($_POST['gender']) ? htmlspecialchars($_POST['gender']) : 'ไม่ระบุ';
    $profile_image = $userProfile['profile_image'];

    // ตรวจสอบการอัปโหลดรูปภาพใหม่
    if (!empty($_FILES['profile_image']['name'])) {
        $uploadResult = uploadProfileImage($_FILES['profile_image']);
        if (strpos($uploadResult, "uploads/") === 0) {
            $profile_image = $uploadResult;
        } else {
            $errorMessage = $uploadResult;
        }
    }

    if (!$errorMessage) {
        try {
            $conn->begin_transaction();
            $sql = "UPDATE users SET fname = ?, lname = ?, email = ?, phone = ?, job_position = ?, 
                        company = ?, address = ?, birth_date = ?, gender = ?, profile_image = ? WHERE id_user = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssi", $fname, $lname, $email, $phone, $job_position, $company, $address, $birth_date, $gender, $profile_image, $userId);
            $stmt->execute();

            $conn->commit();
            $successMessage = "อัปเดตข้อมูลสำเร็จ!";
            $userProfile = getUserProfile($conn, $userId);
        } catch (Exception $e) {
            $conn->rollback();
            $errorMessage = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโปรไฟล์</title>
    <link rel="icon" href="image/logo.png" type="image/png"> <!-- เพิ่มโลโก้ในแท็บเบราว์เซอร์ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <a href="profile.php" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> ย้อนกลับ
    </a>

    <h2 class="mb-4">แก้ไขโปรไฟล์ผู้ใช้</h2>
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="fname" class="form-label">ชื่อจริง</label>
                <input type="text" class="form-control" id="fname" name="fname" value="<?php echo htmlspecialchars($userProfile['fname'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6">
                <label for="lname" class="form-label">นามสกุล</label>
                <input type="text" class="form-control" id="lname" name="lname" value="<?php echo htmlspecialchars($userProfile['lname'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">อีเมล</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userProfile['email'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">เบอร์โทร</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($userProfile['phone'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label for="job_position" class="form-label">ตำแหน่งงาน</label>
            <input type="text" class="form-control" id="job_position" name="job_position" value="<?php echo htmlspecialchars($userProfile['job_position'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label for="company" class="form-label">บริษัท/องค์กร</label>
            <input type="text" class="form-control" id="company" name="company" value="<?php echo htmlspecialchars($userProfile['company'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">ที่อยู่</label>
            <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($userProfile['address'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="birth_date" class="form-label">วันเกิด</label>
            <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($userProfile['birth_date'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="gender" class="form-label">เพศ</label>
            <select class="form-select" id="gender" name="gender" required>
                <option value="ชาย" <?php echo ($userProfile['gender'] == 'ชาย') ? 'selected' : ''; ?>>ชาย</option>
                <option value="หญิง" <?php echo ($userProfile['gender'] == 'หญิง') ? 'selected' : ''; ?>>หญิง</option>
                <option value="อื่นๆ" <?php echo ($userProfile['gender'] == 'อื่นๆ') ? 'selected' : ''; ?>>ไม่ระบุ</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">บันทึก</button>
    </form>
</div>
</body>
</html>
