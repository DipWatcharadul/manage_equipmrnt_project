<?php
include 'connect.php';

// ตรวจสอบว่า ID ถูกส่งมาใน URL หรือไม่
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // ดึงข้อมูลการซ่อมแซมตาม ID ที่ส่งมา
    $sql = "SELECT rs.id, rs.repair_date, rs.repair_complete_date, s.name_rs AS status_rs, s.id_rs AS status_id, e.bib, e.name AS equipment_name, e.brand, e.model, e.image , e.remarks
            FROM repair_schedule rs
            LEFT JOIN status_repair_schedule s ON rs.status_rs = s.id_rs
            LEFT JOIN equipment e ON rs.equipment_id = e.id_equipment
            WHERE rs.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
} else {
    echo "<script>alert('ไม่พบข้อมูลหรือ ID ไม่ถูกต้อง'); window.location.href='repair_schedule_list.php';</script>";
    exit();
}

// ฟังก์ชันสำหรับแสดงสถานะพร้อมสีและจุดกระพริบ
function getStatusIndicator($status_id, $status_name) {
    switch ($status_id) {
        case 1: // รอดำเนินการ
            return "<span class='blinking' style='color: #ADD8E6;'>🔵 $status_name</span>";              
        case 2: // กำลังดำเนินการ
            return "<span class='text-warning blinking'>🟠 $status_name</span>";
        case 3: // ดำเนินการเสร็จสิ้น
            return "<span class='text-success blinking'>🟢 $status_name</span>";
        default:
            return "<span class='text-muted'>ไม่ทราบสถานะ</span>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        body {
            background: url('image/86.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .blinking {
            animation: blinking 1.5s infinite;
        }
        @keyframes blinking {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .card img {
            max-width: 100%;
            border-radius: 10px;
        }
        h2 {
            color: white;
        }
        tr {
            color: white;
        }
    </style>
</head>
<body>
     <!-- Navbar -->
     <nav class="navbar navbar-expand-lg" style="background-color: #007bff;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="admin.php">
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
     <!-- ปุ่มย้อนกลับ -->
     <div class="container mt-3 back-btn">
        <a href="repair_schedule_list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> ย้อนกลับ
        </a>
    </div>
    <div class="container mt-5">
        <h2>รายละเอียดการซ่อมแซม</h2>
        <?php if ($row): ?>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="รูปภาพครุภัณฑ์">
                    </div>
                </div>
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th>รหัสครุภัณฑ์</th>
                            <td><?php echo htmlspecialchars($row['bib'] ?? 'ไม่มีข้อมูล'); ?></td>
                        </tr>
                        <tr>
                            <th>ชื่อครุภัณฑ์</th>
                            <td><?php echo htmlspecialchars($row['equipment_name'] ?? 'ไม่มีข้อมูล'); ?></td>
                        </tr>
                        <tr>
                            <th>ยี่ห้อ</th>
                            <td><?php echo htmlspecialchars($row['brand'] ?? 'ไม่มีข้อมูล'); ?></td>
                        </tr>
                        <tr>
                            <th>รุ่น</th>
                            <td><?php echo htmlspecialchars($row['model'] ?? 'ไม่มีข้อมูล'); ?></td>
                        </tr>
                        <tr>
                            <th>วันที่ซ่อมแซม</th>
                            <td><?php echo htmlspecialchars($row['repair_date'] ?? 'ไม่มีข้อมูล'); ?></td>
                        </tr>
                        <tr>
                            <th>วันที่ซ่อมเสร็จ</th>
                            <td><?php echo htmlspecialchars($row['repair_complete_date'] ?? 'ไม่มีข้อมูล'); ?></td>
                        </tr>
                        <tr>
                            <th>สถานะการซ่อมแซม</th>
                            <td><?php echo getStatusIndicator($row['status_id'] ?? 0, $row['status_rs'] ?? 'ไม่ทราบสถานะ'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <p>ไม่พบข้อมูล</p>
        <?php endif; ?>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</html>
