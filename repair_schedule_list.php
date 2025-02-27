<?php
include 'connect.php';

// ดึงข้อมูลตารางการซ่อมแซมจากฐานข้อมูล
$sql = "SELECT rs.id, rs.repair_date,rs.repair_complete_date, s.name_rs AS status_rs, e.bib, e.name AS equipment_name, e.brand, e.model, e.image , e.remarks
        FROM repair_schedule rs
        LEFT JOIN status_repair_schedule s ON rs.status_rs = s.id_rs
        LEFT JOIN equipment e ON rs.equipment_id = e.id_equipment
        ORDER BY rs.repair_date DESC"; 
$result = $conn->query($sql);
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
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 2px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            font-size: 1rem;
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
            background-color:rgba(172, 190, 207, 0.83);
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
        
        /* CSS สำหรับจุดสถานะที่กระพริบ */
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-left: 10px;
            display: inline-block;
            animation: blink 1s infinite;
        }
        .status-waiting {
            background-color:rgb(9, 178, 235); /* สีฟ้าอ่อน */
            animation-duration: 1.5s;
        }
        .status-in-progress {
            background-color: orange;
            animation-duration: 1.2s;
        }
        .status-complete {
            background-color: green;
            animation-duration: 1s;
        }
        
        /* กำหนดการกระพริบ */
        @keyframes blink {
            0% { opacity: 0.2; }
            50% { opacity: 1; }
            100% { opacity: 0.2; }
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
    <!-- ตารางรายการตารางซ่อมแซม -->
    <div class="container mt-5">
        <h2>รายการซ่อมแซม</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>รหัสครุภัณฑ์</th>
                    <th>รูปภาพ</th>
                    <th>ชื่อครุภัณฑ์</th>
                    <th>ยี่ห้อ</th>
                    <th>รุ่น</th>
                    <th>วันที่ซ่อมแซม</th>
                    <th>วันที่ซ่อมเสร็จ</th>
                    <th>สถานะการซ่อมแซม</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // กำหนดสถานะการซ่อมแซม
                        $status_class = '';
                        switch ($row['status_rs']) {
                            case 'รอดำเนินการ':
                                $status_class = 'status-waiting';
                                break;
                            case 'กำลังดำเนินการ':
                                $status_class = 'status-in-progress';
                                break;
                            case 'ดำเนินการเสร็จสิ้น':
                                $status_class = 'status-complete';
                                break;
                            default:
                                $status_class = 'status-waiting';
                        }
                        
                        echo "<tr>";
                        echo "<td><a href='repair_schedule_detail.php?id=" . $row['id'] . "' class='text-decoration-none'>" . htmlspecialchars($row['bib'] ?? 'ไม่มีข้อมูล') . "</a></td>";
                        echo "<td><img src='uploads/" . $row['image'] . "' class='img-thumbnail' alt='รูปภาพครุภัณฑ์'></td>";
                        echo "<td>" . (!empty($row['equipment_name']) ? $row['equipment_name'] : '-') . "</td>";
                        echo "<td>" . (!empty($row['brand']) ? $row['brand'] : '-') . "</td>";
                        echo "<td>" . (!empty($row['model']) ? $row['model'] : '-') . "</td>";
                        echo "<td>" . (!empty($row['repair_date']) ? $row['repair_date'] : '-') . "</td>";
                        echo "<td>" . (!empty($row['repair_complete_date']) ? $row['repair_complete_date'] : '-') . "</td>";
                        echo "<td>" . (!empty($row['status_rs']) ? $row['status_rs'] : '-') . "<span class='status-dot " . $status_class . "'></span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>ไม่มีข้อมูล</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
