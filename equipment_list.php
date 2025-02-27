<?php
// เริ่มต้น session
session_start();
require_once 'connect.php';

// ตรวจสอบสิทธิ์การเข้าใช้งาน
if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

// ดึงข้อมูลครุภัณฑ์พร้อมกับสถานะและชื่อผู้ใช้
$sql = "SELECT e.id_equipment, e.bib, e.name, e.address, e.byear , se.name_e AS status, e.image, e.brand, e.model, e.description, e.remarks, u.username
        FROM equipment e
        LEFT JOIN status_equipment se ON e.status_e = se.id_e
        LEFT JOIN users u ON e.user_id = u.id_user";

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
    <link rel="icon" href="image/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('image/86.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            z-index: 1030;
            background-color: rgba(0, 123, 255, 0.8);
        }
        .content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 30px;
            border-radius: 10px;
            margin: 20px;
        }
        h2 {
            font-size: 2.5rem;
            color: white;
        }
        .table {
            background-color: rgba(95, 72, 230, 0.8);
            border-radius: 10px;
            box-shadow: 0 5px 8px rgba(0, 0, 0, 0.1);
            font-size: 0.6rem;
        }
        .table th, .table td {
            padding: 1rem;
            text-align: center;
            vertical-align: middle;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .table td {
            background-color:rgba(245, 247, 248, 0.92);
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        .btn-info {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-info:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .img-thumbnail {
            max-width: 100px;
            max-height: 100px;
        }
        .container {
            margin-top: 30px;
        }
        h2 {
            color: white;
        }

        /* CSS สำหรับจุดสถานะที่กระพริบ */
        .status-dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            animation: blink 1.5s infinite ease-in-out;
        }
        @keyframes blink {
            0% {
                opacity: 0.3;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0.3;
            }
        }
        .status-active {
            background-color: green;
        }
        .status-repair {
            background-color: orange;
        }
        .status-broken {
            background-color: red;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" style="background-color: #007bff;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="user.php">
                <i class="bi bi-tools"></i> ระบบจัดการครุภัณฑ์  
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
                            <li><a class="dropdown-item" href="repair_schedule_list.php">รายงานซ่อมแซม</a></li>
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

    <div class="container mt-5">
        <h2 class="text-white text-center">รายการครุภัณฑ์</h2>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>รหัสครุภัณฑ์</th>
                    <th>รูปภาพ</th>
                    <th>ชื่อครุภัณฑ์</th>
                    <th>รายละเอียด</th>
                    <th>ยี่ห้อ</th>
                    <th>รุ่น</th>
                    <th>สถานที่จัดเก็บ</th>
                    <th>ชื่อผู้ใช้งาน</th>
                    <th>หมายเหตุ</th>
                    <th>ปีงบประมาณ</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $statusClass = match ($row['status']) {
                                'พร้อมใช้งาน' => 'status-active',
                                'ส่งซ่อม' => 'status-repair',
                                'เสียหาย' => 'status-broken',
                                default => '',
                            };
                        ?>
                        <tr>
                            <td><a href="equipment_detail.php?id=<?= $row['id_equipment'] ?>" class="text-decoration-none"><?= htmlspecialchars($row['bib'] ?? '-') ?></a></td>
                            <td><img src="uploads/<?= htmlspecialchars($row['image'] ?? 'default.jpg') ?>" class="img-thumbnail" alt="รูปภาพครุภัณฑ์"></td>
                            <td><?= htmlspecialchars($row['name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['description'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['brand'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['model'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['address'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['username'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['remarks'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['byear'] ?? '-') ?></td>
                            <td><span class="status-dot <?= $statusClass ?>"></span> <?= htmlspecialchars($row['status'] ?? '-') ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="10">ไม่พบข้อมูล</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>