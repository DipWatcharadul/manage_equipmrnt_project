<?php
// เริ่มต้นการใช้งาน session
session_start();
require_once 'connect.php';

// ตรวจสอบว่าผู้ใช้เป็นผู้ดูแลระบบหรือไม่
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

// ฟังก์ชันสำหรับดึงข้อมูลครุภัณฑ์
function getEquipmentList($conn, $status)
{
    $stmt = $conn->prepare("SELECT * FROM equipment WHERE status_e = ?");
    $stmt->bind_param("i", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

// ดึงข้อมูลตามสถานะต่างๆ
$status = isset($_GET['status']) ? $_GET['status'] : 1; // ค่าเริ่มต้นเป็นสถานะพร้อมใช้งาน
$equipment_list = getEquipmentList($conn, $status);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดครุภัณฑ์</title>
    <link rel="icon" href="image/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }
        .content {
            margin: 20px;
        }
        .table {
            border-radius: 10px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
            font-size: 0.9rem;
        }
        .table th, .table td {
            padding: 1rem;
            text-align: center;
            vertical-align: middle;
        }
        .table th {
            background-color: rgb(0, 0, 0);
            color: white;
        }
        .table td {
            background-color: rgba(245, 247, 248, 0.92);
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        .navbar {
            background-color: #007bff;
        }
        .table img {
            max-width: 80px;
            max-height: 80px;
            object-fit: cover;
        }
        .status-dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            animation: blink 1.5s infinite;
        }
        .status-dot.available {
            background-color: #28a745;
        }
        .status-dot.repairing {
            background-color: #ffc107;
        }
        .status-dot.damaged {
            background-color: #dc3545;
        }

        @keyframes blink {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
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
    <div class="content">
        <a href="admin.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> ย้อนกลับ
        </a>
    </div>
    <!-- Content -->
    <div class="content">
        <h2>รายละเอียดครุภัณฑ์</h2>
        <p>แสดงข้อมูลครุภัณฑ์ตามสถานะที่เลือก</p>
        <div class="table-container">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>รูปภาพ</th>
                        <th>รหัสครุภัณฑ์</th>
                        <th>ชื่อครุภัณฑ์</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>สถานที่จัดเก็บ</th>
                        <th>หมายเหตุ</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($equipment_list->num_rows > 0): ?>
                        <?php while ($row = $equipment_list->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if ($row['image']): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="image">
                                    <?php else: ?>
                                        <img src="uploads/default.jpg" alt="Default Image">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['bib']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['brand']); ?></td>
                                <td><?php echo htmlspecialchars($row['model']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                <td>
                                    <?php
                                    $status_label = '';
                                    $status_class = '';
                                    switch ($row['status_e']) {
                                        case 1: 
                                            $status_label = 'พร้อมใช้งาน'; 
                                            $status_class = 'available'; 
                                            break;
                                        case 2: 
                                            $status_label = 'ส่งซ่อม'; 
                                            $status_class = 'repairing'; 
                                            break;
                                        case 3: 
                                            $status_label = 'เสียหาย'; 
                                            $status_class = 'damaged'; 
                                            break;
                                    }
                                    echo '<span class="status-dot ' . $status_class . '"></span> ' . $status_label;
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">ไม่มีข้อมูล</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
