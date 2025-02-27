<?php
// เริ่มต้นการใช้งาน session
session_start();
require_once 'connect.php';

// ตรวจสอบว่าผู้ใช้เป็นผู้ดูแลระบบหรือไม่
if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

// ฟังก์ชันสำหรับดึงข้อมูลตามสถานะ
function getCountByStatus($conn, $table, $column, $status)
{
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM $table WHERE $column = ?");
    $stmt->bind_param("i", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data['total'];
}

// สรุปข้อมูลครุภัณฑ์
$equipment_ready = getCountByStatus($conn, 'equipment', 'status_e', 1); // พร้อมใช้งาน
$equipment_repair = getCountByStatus($conn, 'equipment', 'status_e', 2); // ส่งซ่อม
$equipment_damaged = getCountByStatus($conn, 'equipment', 'status_e', 3); // เสียหาย

// สรุปข้อมูลการซ่อมแซม
$repair_pending = getCountByStatus($conn, 'repair_schedule', 'status_rs', 1); // รอดำเนินการ
$repair_in_progress = getCountByStatus($conn, 'repair_schedule', 'status_rs', 2); // กำลังดำเนินการ
$repair_completed = getCountByStatus($conn, 'repair_schedule', 'status_rs', 3); // ดำเนินการเสร็จสิ้น
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
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 30px;
            border-radius: 10px;
            margin: 30px;
        }
        .card {
            cursor: pointer;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .section-title {
            margin-top: 40px;
        }
        .input-group {
            margin-bottom: 30px;
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
                        <a class="nav-link text-white" href="profile.php"><i class="bi bi-person-circle"></i> โปรไฟล์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content">
        <h2>ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        
        <!-- ฟอร์มค้นหาครุภัณฑ์ -->
        <div class="container mt-5">
            <form method="POST" action="search_equipment.php">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="กรอกค้นหารหัสครุภัณฑ์หรือชื่อครุภัณฑ์" required>
                    <button class="btn btn-primary" type="submit" name="search"><i class="bi bi-search"></i> ค้นหา</button>
                </div>
            </form>
            
            <!-- สรุปข้อมูลครุภัณฑ์ -->
            <h4 class="section-title">ข้อมูลครุภัณฑ์</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3" onclick="window.location='equipment_status.php?status=1'">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-check-circle"></i> พร้อมใช้งาน</h5>
                            <p class="card-text fs-1"><?php echo $equipment_ready; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white" style="background-color: #FF8C00;" onclick="window.location='equipment_status.php?status=2'">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-tools"></i> ส่งซ่อม</h5>
                            <p class="card-text fs-1"><?php echo $equipment_repair; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3" onclick="window.location='equipment_status.php?status=3'">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-x-circle-fill"></i> เสียหาย</h5>
                            <p class="card-text fs-1"><?php echo $equipment_damaged; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- สรุปข้อมูลการซ่อมแซม -->
            <h4 class="section-title">ข้อมูลการซ่อมแซม</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3" onclick="window.location='repair_schedule_status_.php?status=1'">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-clock"></i> รอดำเนินการ</h5>
                            <p class="card-text fs-1"><?php echo $repair_pending; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white" style="background-color: #FF8C00;" onclick="window.location='repair_schedule_status_.php?status=2'">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-hammer"></i> กำลังดำเนินการ</h5>
                            <p class="card-text fs-1"><?php echo $repair_in_progress; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3" onclick="window.location='repair_schedule_status_.php?status=3'">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-check-circle"></i> ดำเนินการเสร็จสิ้น</h5>
                            <p class="card-text fs-1"><?php echo $repair_completed; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
