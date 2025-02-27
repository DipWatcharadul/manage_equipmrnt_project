<?php
// เริ่มต้นการใช้งาน session
session_start();
require_once 'connect.php';

// ตรวจสอบว่าผู้ใช้เป็นผู้ดูแลระบบหรือไม่
if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

function getRepairScheduleListByStatus($conn, $status = null)
{
    $query = "
        SELECT rs.id, rs.equipment_id, rs.repair_date, rs.status_rs, rs.updated_at, rs.repair_complete_date, 
               e.bib, e.name, e.brand, e.model, e.image
        FROM repair_schedule rs
        JOIN equipment e ON rs.equipment_id = e.id_equipment
    ";
    if ($status !== null) {
        $query .= " WHERE rs.status_rs = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $status);
    } else {
        $stmt = $conn->prepare($query);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// รับค่าพารามิเตอร์สถานะ
$status = isset($_GET['status']) ? intval($_GET['status']) : null;

// ดึงข้อมูลการซ่อมแซมตามสถานะ
$repair_schedule_list = getRepairScheduleListByStatus($conn, $status);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถานะการซ่อมแซมครุภัณฑ์</title>
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
            margin: 20px; }
        .table { 
            background-color: rgba(95, 72, 230, 0.8); 
            border-radius: 10px; 
            box-shadow: 0 5px 8px rgba(0, 0, 0, 0.1); 
            font-size: 0.8rem; 
        }
        .table th, .table td { 
            padding: 1rem; 
            text-align: center; 
            vertical-align: middle; 
        }
        h2 { 
            color: white; 
        }
        p { 
            color: white; 
        }
        .table th { 
            background-color: #007bff; 
            color: white; 
        }
        .table td { 
            background-color: rgba(245, 247, 248, 0.92); 
        }
        .table tbody tr:hover { 
            background-color: #e9ecef; 
        }
        .table img { 
            max-width: 80px; 
            max-height: 80px; 
            object-fit: cover; 
        }
        .status-dot { 
            width: 12px; 
            height: 12px; 
            border-radius: 50%; 
            display: inline-block; 
            margin-right: 5px; 
            animation: blink 1.5s infinite; 
        }
        .status-pending-dot { 
            background-color: blue; 
        }
        .status-repairing-dot { 
            background-color: orange; 
        }
        .status-completed-dot { 
            background-color: green; 
        }

        @keyframes blink {
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
            <a class="navbar-brand text-white" href="user.php">
                <i class="bi bi-tools"></i> ระบบจัดการครุภัณฑ์  
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link text-white" href="user.php"><i class="bi bi-house"></i> หน้าแรก</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-book"></i> รายงาน</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="equipment_list.php">รายงานครุภัณฑ์</a></li>
                            <li><a class="dropdown-item" href="repair_schedule_list.php">รายงานซ่อมแซม</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link text-white" href="profile.php"><i class="bi-person-circle"></i> โปรไฟล์</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="content">
        <a href="user.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> ย้อนกลับ
        </a>
    </div>
    <!-- Content -->
    <div class="content">
        <h2>สถานะการซ่อมแซมครุภัณฑ์</h2>
        <p>แสดงข้อมูลซ่อมแซมตามสถานะที่เลือก</p>
        <div class="table-container">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>รหัสครุภัณฑ์</th>
                        <th>รูปภาพ</th>
                        <th>ชื่อครุภัณฑ์</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>วันที่ซ่อมแซม</th>
                        <th>วันที่ซ่อมเสร็จ</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($repair_schedule_list->num_rows > 0): ?>
                        <?php while ($row = $repair_schedule_list->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['bib'] ?? '-'); ?></td>
                                <td><?php echo $row['image'] ? "<img src='uploads/{$row['image']}' alt='image'>" : 'ไม่มีรูปภาพ'; ?></td>
                                <td><?php echo htmlspecialchars($row['name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['brand'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['model'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['repair_date'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['repair_complete_date'] ?? '-'); ?></td>
                                <td>
                                    <?php
                                    $status_label = $row['status_rs'] == 1 ? 'รอดำเนินการ' : ($row['status_rs'] == 2 ? 'กำลังดำเนินการ' : 'ดำเนินการเสร็จสิ้น');
                                    $dot_class = $row['status_rs'] == 1 ? 'status-pending-dot' : ($row['status_rs'] == 2 ? 'status-repairing-dot' : 'status-completed-dot');
                                    echo "<span><span class='status-dot $dot_class'></span> $status_label</span>";
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
</body>
</html>
