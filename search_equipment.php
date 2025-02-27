<?php
// เริ่มต้น session
session_start();
require_once 'connect.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

// ตัวแปรสำหรับเก็บผลการค้นหา
$search_results = [];
$message = '';

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if (isset($_POST['search'])) {
    $query = mysqli_real_escape_string($conn, $_POST['query']); // แปลงค่า query

    // สร้างคำสั่ง SQL สำหรับการค้นหาครุภัณฑ์ โดยใช้ Prepared Statements
    $sql = "SELECT * FROM equipment a 
            INNER JOIN status_equipment b ON a.status_e = b.id_e
            WHERE a.bib LIKE ? OR a.name LIKE ? OR a.description LIKE ?"; // ค้นหาทั้งรหัสครุภัณฑ์ ชื่อ และรายละเอียด
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        $search_query = "%" . $query . "%"; // ใช้เครื่องหมาย % สำหรับการค้นหาที่ไม่ตรงตัว
        mysqli_stmt_bind_param($stmt, 'sss', $search_query, $search_query, $search_query); // Binding parameters
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // เก็บผลการค้นหา
        if ($result && mysqli_num_rows($result) > 0) {
            $search_results = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $message = "ไม่พบครุภัณฑ์ที่ค้นหา";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "เกิดข้อผิดพลาดในการค้นหา";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการครุภัณฑ์</title>
    <link rel="icon" href="image/logo.png" type="image/png"> <!-- เพิ่มโลโก้ในแท็บเบราว์เซอร์ -->
    <!-- เรียกใช้ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        body {
            background-image: url('image/86.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            padding: 0;
            background-attachment: fixed;
        }
        h2 {
            color: white;
        }
        .status {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
            animation: blink 1.5s infinite;
        }

        /* กำหนดสีสถานะ */
        .status-in-use { background-color:rgb(11, 233, 77); } /* พร้อมใช้งาน (สีเขีบว) */
        .status-repair { background-color:rgb(231, 119, 28); } /* ส่งซ่อม (สีเหลือง) */
        .status-decommissioned { background-color: #dc3545; } /* เสียหาย (สีแดง) */

        /* Animation กระพริบ */
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        table th {
            background-color: #0056b3 !important;
            color: white !important;
            text-align: center;
            padding: 15px;
        }

        .equipment-row { background-color: #ffffff; color: #000000; }
        img { width: 100px; height: auto; border-radius: 10px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" style="background-color: #007bff;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="admin.php">
                <i class="bi bi-tools"></i> จัดการครุภัณฑ์  
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="user.php"><i class="bi bi-house"></i> หน้าแรก</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-book"></i> รายงาน</a>
                         <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="equipment_list.php">รายงานครุภัณฑ์</a></li>
                                <li><a class="dropdown-item" href="repair_schedule_list.php">ตารางซ่อมแซม</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="profile.php"><i class="bi-person-circle"></i> โปรไฟล์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <a href="user.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> ย้อนกลับ
        </a>
    </div>

    <div class="container content mt-5">
        <?php if ($message): ?>
            <p class="text-danger"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <?php if (!empty($search_results)): ?>
            <table class="table table-striped table-hover table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>รหัสครุภัณฑ์</th>
                        <th>รูปภาพ</th>
                        <th>ชื่อครุภัณฑ์</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>รายละเอียด</th>
                        <th>สถานที่จัดเก็บ</th>
                        <th>หมายเหตุ</th>
                        <th>ปีงบประมาณ</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_results as $equipment): ?>
                        <tr class="equipment-row">
                            <td><?php echo htmlspecialchars($equipment['bib'] ?? ''); ?></td>
                            <td>
                                <img src="uploads/<?php echo htmlspecialchars($equipment['image'] ?? 'default.jpg'); ?>" alt="รูปภาพของ <?php echo htmlspecialchars($equipment['name'] ?? ''); ?>">
                            </td>
                            <td><?php echo htmlspecialchars($equipment['name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($equipment['brand'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($equipment['model'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($equipment['description'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($equipment['address'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($equipment['remarks'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($equipment['byear'] ?? ''); ?></td>
                            <td>
                                <span class="status 
                                    <?php 
                                    switch ($equipment['name_e'] ?? '') {
                                        case 'พร้อมใช้งาน': echo 'status-in-use'; break;
                                        case 'ส่งซ่อม': echo 'status-repair'; break;
                                        case 'เสียหาย': echo 'status-decommissioned'; break;
                                    }
                                    ?>
                                "></span>
                                <?php echo htmlspecialchars($equipment['name_e'] ?? ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
