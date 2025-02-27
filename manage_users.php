<?php
// เริ่มต้นการใช้งาน session
session_start();

// ตรวจสอบว่าผู้ใช้เป็นผู้ดูแลระบบหรือไม่
if (!isset($_SESSION['admin_login'])) {
    header('Location: index.php');
    exit();
}

require_once 'connect.php';

// กำหนดค่าเริ่มต้นให้กับ $result เพื่อหลีกเลี่ยงคำเตือน
$result = null;

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $username = $_POST['username'];
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $urole = $_POST['urole'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน

    // ตรวจสอบว่ามีชื่อผู้ใช้หรืออีเมลซ้ำหรือไม่
    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "ชื่อผู้ใช้หรืออีเมลนี้มีผู้ใช้แล้ว";
    } else {
        // เพิ่มสมาชิกใหม่
        $create_at = date("Y-m-d H:i:s"); // กำหนดค่าเวลาปัจจุบัน
        $sql = "INSERT INTO users (username, email, fname, lname, address, urole, password, create_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $username, $email, $fname, $lname, $address, $urole, $password, $create_at);

        if ($stmt->execute()) {
            $success_message = "เพิ่มสมาชิกใหม่สำเร็จ!";
        } else {
            $error_message = "เกิดข้อผิดพลาดในการเพิ่มสมาชิก";
        }
    }
}

// ดึงข้อมูลสมาชิกจากฐานข้อมูล
$sql = "SELECT * FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการครุภัณฑ์</title>
    <link rel="icon" href="image/logo.png" type="image/png"> <!-- เพิ่มโลโก้ในแท็บเบราว์เซอร์ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<style>
    .navbar {
        z-index: 2500;
        background-color: rgba(0, 123, 255, 0.8);
    }
    .table {
        border-radius: 100;
        box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
        font-size: 0.9rem;
    }
</style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" style="background-color: #007bff;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="admin.php">
                <i class="bi bi-tools"></i> จัดการครุภัณฑ์ - ผู้ดูแลระบบ
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                        <a class="nav-link text-white" href="admin.php"><i class="bi bi-house"></i>หน้าแรก</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-book"></i> จัดการข้อมูล</a>
                         <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="manage_users.php">จัดการสมาชิก</a></li>
                                <li><a class="dropdown-item" href="manage_equipment.php">จัดการครุภัณฑ์</a></li>
                                <li><a class="dropdown-item" href="manage_repair_schedule.php">จัดการกำหนดการซ่อมครุภัณฑ์</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="admin_profile.php"><i class="bi-person-circle"></i> โปรไฟล์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- เนื้อหา -->
    <div class="container mt-5">
        <h2>จัดการสมาชิก</h2>
        <!-- แสดงข้อความเมื่อสำเร็จหรือเกิดข้อผิดพลาด -->
        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php } elseif (isset($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>

        <!-- ฟอร์มเพิ่มสมาชิก -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="fname" class="form-label">ชื่อ</label>
                <input type="text" class="form-control" id="fname" name="fname" required>
            </div>
            <div class="mb-3">
                <label for="lname" class="form-label">นามสกุล</label>
                <input type="text" class="form-control" id="lname" name="lname" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="urole" class="form-label">บทบาท</label>
                <select class="form-control" id="urole" name="urole" required>
                    <option value="1">ผู้ใช้ทั่วไป</option>
                    <option value="2">ผู้ดูแลระบบ</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">เพิ่มสมาชิก</button>
        </form>
    </div>

    <!-- ตารางแสดงข้อมูลสมาชิก -->
    <div class="container content mt-5">
    <h3 class="text-center mb-4">ข้อมูลสมาชิก</h3>
    <table class="table table-striped table-hover table-bordered text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>ชื่อผู้ใช้</th>
                <th>ชื่อ</th>
                <th>นามสกุล</th>
                <th>อีเมล</th>
                <th>ที่อยู่</th>
                <th>วันที่ลงทะเบียน</th>
                <th>บทบาท</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['fname'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['lname'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['address'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['create_at'] ?? ''); ?></td>
                        <td>
                            <?php
                            if ($row['urole'] == 1) {
                                echo "<span class='badge bg-primary'>ผู้ใช้ทั่วไป</span>";
                            } elseif ($row['urole'] == 2) {
                                echo "<span class='badge bg-success'>ผู้ดูแลระบบ</span>";
                            } else {
                                echo "<span class='badge bg-secondary'>ไม่ระบุ</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="edit_member.php?id_user=<?php echo $row['id_user']; ?>" class="btn btn-warning btn-sm me-2">
                                    <i class="bi bi-pencil-square"></i> แก้ไข
                                </a>
                                <?php if ($row['urole'] == 1) { ?>
                                    <a href="delete_member.php?id_user=<?php echo $row['id_user']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณต้องการลบสมาชิกนี้หรือไม่?');">
                                        <i class="bi bi-trash"></i> ลบ
                                    </a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php }
            } else {
                echo "<tr><td colspan='8' class='text-center'>ไม่มีข้อมูล</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>


    <!-- เรียกใช้ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
