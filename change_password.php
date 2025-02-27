<?php
require 'connect.php'; // เชื่อมต่อฐานข้อมูล

session_start();

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_login'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_login'];

$message = ""; // ใช้เก็บข้อความสำหรับแจ้งเตือน

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>รหัสผ่านใหม่และการยืนยันไม่ตรงกัน</div>";
    } else {
        $sql = "SELECT password FROM users WHERE id_user = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($hashed_password);
                $stmt->fetch();

                if (password_verify($current_password, $hashed_password)) {
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $update_sql = "UPDATE users SET password = ? WHERE id_user = ?";
                    if ($update_stmt = $conn->prepare($update_sql)) {
                        $update_stmt->bind_param("si", $new_hashed_password, $userId);
                        if ($update_stmt->execute()) {
                            $message = "<div class='alert alert-success'>รหัสผ่านถูกเปลี่ยนเรียบร้อย</div>";
                        } else {
                            $message = "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน</div>";
                        }
                    }
                } else {
                    $message = "<div class='alert alert-danger'>รหัสผ่านปัจจุบันไม่ถูกต้อง</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>ไม่พบข้อมูลผู้ใช้</div>";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            max-width: 500px;
            margin: auto;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
    <a href="profile.php" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> ย้อนกลับ </a>
    </div>
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4>เปลี่ยนรหัสผ่าน</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($message)) echo $message; ?>
                <form method="POST" action="change_password.php">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">รหัสผ่านปัจจุบัน</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">รหัสผ่านใหม่</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">เปลี่ยนรหัสผ่าน</button>
                </form>
            </div>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
