<?php
include 'connect.php';

// ตรวจสอบว่ามีการส่งค่า id มาใน URL และต้องเป็นตัวเลขเท่านั้น
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $equipment_id = $_GET['id'];

    // ดึงข้อมูลครุภัณฑ์พร้อมดึง username จาก user
    $sql = "SELECT e.id_equipment, e.bib, e.name, e.address, e.image, e.brand, e.model, 
                   e.remarks, e.description, se.name_e AS status, se.id_e AS status_id,
                   u.username 
            FROM equipment e
            LEFT JOIN status_equipment se ON e.status_e = se.id_e
            LEFT JOIN users u ON e.user_id = u.id_user
            WHERE e.id_equipment = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $equipment_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "<script>alert('ไม่พบข้อมูล'); window.location.href='equipment_list.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการดึงข้อมูล'); window.location.href='equipment_list.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ไม่มีการระบุ id หรือ id ไม่ถูกต้อง'); window.location.href='equipment_list.php';</script>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        body {
            background: url('image/86.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
        .blinking {
            animation: blinking 1s infinite;
        }
        @keyframes blinking {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .status-green {
            color: green;
        }
        .status-orange {
            color: orange;
        }
        .status-red {
            color: red;
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
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="user.php">
                <i class="bi bi-tools"></i> ระบบจัดการครุภัณฑ์  
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link text-white" href="user.php"><i class="bi bi-house"></i> หน้าแรก</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-book"></i> รายงาน
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="equipment_list.php">รายงานครุภัณฑ์</a></li>
                            <li><a class="dropdown-item" href="repair_schedule_list.php">รายงานซ่อมแซม</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link text-white" href="profile.php"><i class="bi bi-person-circle"></i> โปรไฟล์</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ปุ่มย้อนกลับ -->
    <div class="container mt-3">
        <a href="equipment_list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> ย้อนกลับ
        </a>
    </div>

    <!-- แสดงรายละเอียดครุภัณฑ์ -->
    <div class="container mt-4">
        <h2 class="text-center">รายละเอียดครุภัณฑ์</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="รูปภาพครุภัณฑ์" class="equipment-image">
                </div>
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th>รหัสครุภัณฑ์</th><td><?php echo htmlspecialchars($row['bib']); ?></td></tr>
                    <tr><th>ชื่อครุภัณฑ์</th><td><?php echo htmlspecialchars($row['name']); ?></td></tr>
                    <tr><th>ยี่ห้อ</th><td><?php echo htmlspecialchars($row['brand']); ?></td></tr>
                    <tr><th>รุ่น</th><td><?php echo htmlspecialchars($row['model']); ?></td></tr>
                    <tr><th>สถานที่จัดเก็บ</th><td><?php echo htmlspecialchars($row['address']); ?></td></tr>
                    <tr><th>รายละเอียด</th><td><?php echo htmlspecialchars($row['description']); ?></td></tr>
                    <tr><th>หมายเหตุ</th><td><?php echo htmlspecialchars($row['remarks']); ?></td></tr>
                    <tr><th>ชื่อผู้ใช้</th><td><?php echo htmlspecialchars($row['username']); ?></td></tr>
                    <tr><th>สถานะ</th>
                        <td>
                            <span class="blinking 
                                <?php echo ($row['status_id'] == 1) ? 'status-green">🟢 พร้อมใช้งาน' :
                                            (($row['status_id'] == 2) ? 'status-orange">🟠 ส่งซ่อม' :
                                            'status-red">🔴 เสียหาย'); ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>