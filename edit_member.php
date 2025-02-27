<?php
session_start();
require_once 'connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_login'])) {
    header('Location: index.php');
    exit();
}

// Get user ID from query parameter
$id_user = $_GET['id_user'];

// Fetch user data from the database
$sql = "SELECT * FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการครุภัณฑ์</title>
    <link rel="icon" href="image/logo.png" type="image/png"> <!-- เพิ่มโลโก้ในแท็บเบราว์เซอร์ -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .form-control {
            border-radius: 0.25rem;
        }
        h2 {
            font-weight: bold;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>

<body>
<div class="container mt-5">
    <a href="manage_users.php" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> ย้อนกลับ
    </a>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <h2 class="text-center text-primary mb-4">แก้ไขข้อมูลผู้ใช้</h2>
                <form action="update_user.php" method="post">
                    <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($user['id_user']); ?>">
                    
                    <!-- ชื่อผู้ใช้ -->
                    <div class="form-group">
                        <label for="username">ชื่อผู้ใช้:</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <!-- รหัสผ่านปัจจุบัน -->
                    <div class="form-group">
                        <label for="current_password">รหัสผ่านปัจจุบัน:</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>

                    <!-- รหัสผ่านใหม่ -->
                    <div class="form-group">
                        <label for="new_password">รหัสผ่านใหม่:</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                        <small class="form-text text-muted">หากไม่ต้องการเปลี่ยนรหัสผ่าน กรุณาปล่อยให้ช่องนี้ว่าง</small>
                    </div>

                    <!-- ยืนยันรหัสผ่านใหม่ -->
                    <div class="form-group">
                        <label for="confirm_password">ยืนยันรหัสผ่านใหม่:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>

                    <!-- อีเมล -->
                    <div class="form-group">
                        <label for="email">อีเมล:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <!-- ชื่อ -->
                    <div class="form-group">
                        <label for="firstname">ชื่อ:</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['fname']); ?>" required>
                    </div>

                    <!-- นามสกุล -->
                    <div class="form-group">
                        <label for="lastname">นามสกุล:</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lname']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">อัพเดตข้อมูล</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
