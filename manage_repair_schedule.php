<?php
// เริ่มต้นการใช้งาน session
session_start();

if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

require_once 'connect.php';

// ตรวจสอบการลบข้อมูล
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql_delete = "DELETE FROM repair_schedule WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id);
    $stmt_delete->execute();
    $stmt_delete->close();
    
    // เช็คว่าลบสำเร็จหรือไม่
    if ($stmt_delete->affected_rows > 0) {
        $_SESSION['message'] = 'ลบข้อมูลสำเร็จ';
        $_SESSION['msg_type'] = 'success';
    } else {
        $_SESSION['message'] = 'ไม่สามารถลบข้อมูลได้';
        $_SESSION['msg_type'] = 'danger';
    }
    header('Location: manage_repair_schedule.php');
    exit();
}

$sql_rh = "SELECT * FROM repair_schedule a 
              INNER JOIN status_repair_schedule b ON a.status_rs = b.id_rs
              INNER JOIN equipment c ON a.equipment_id = c.id_equipment";
$result_data = mysqli_query($conn, $sql_rh);

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
        /* สไตล์สำหรับจุดสถานะ */
        .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .status-blue { background-color: blue; animation: blink-blue 1.5s infinite; }
        .status-orange { background-color: orange; animation: blink-orange 1.5s infinite; }
        .status-green { background-color: green; animation: blink-green 1.5s infinite; }

        @keyframes blink-blue { 50% { opacity: 0.5; } }
        @keyframes blink-orange { 50% { opacity: 0.5; } }
        @keyframes blink-green { 50% { opacity: 0.5; } }
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
                    <li class="nav-item"><a class="nav-link text-white" href="admin.php"><i class="bi bi-house"></i>หน้าแรก</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-book"></i> จัดการข้อมูล</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="manage_users.php">จัดการสมาชิก</a></li>
                            <li><a class="dropdown-item" href="manage_equipment.php">จัดการครุภัณฑ์</a></li>
                            <li><a class="dropdown-item" href="manage_repair_schedule.php">จัดการกำหนดการซ่อมครุภัณฑ์</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link text-white" href="admin_profile.php"><i class="bi-person-circle"></i> โปรไฟล์</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Notification -->
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message']); unset($_SESSION['msg_type']); endif; ?>

    <!-- เนื้อหา -->
    <div class="container mt-5">
        <h2>จัดการกำหนดการซ่อมครุภัณฑ์</h2>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">เพิ่มกำหนดการซ่อม</h5>
                <form action="insert_repair_schedule.php" method="POST">
                    <div class="mb-3">
                        <label for="equipment_id" class="form-label">ID ครุภัณฑ์</label>
                        <select class="form-select" id="equipment_id" name="equipment_id" required>
                            <?php
                            $sql = "SELECT * FROM equipment";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) { ?>
                                <option value="<?php echo htmlspecialchars($row['id_equipment']); ?>">
                                    <?php echo htmlspecialchars($row['bib'] . " " . $row['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="repair_date" class="form-label">วันที่ซ่อมแซม</label>
                        <input type="date" class="form-control" id="repair_date" name="repair_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="repair_complete_date" class="form-label">วันที่ซ่อมเสร็จ</label>
                        <input type="date" class="form-control" id="repair_complete_date" name="repair_complete_date">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">สถานะการซ่อม</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="1">🔵 รอดำเนินการ</option>
                            <option value="2">🟠 กำลังดำเนินการ</option>
                            <option value="3">🟢 ดำเนินการเสร็จสิ้น</option>
                        </select>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">เพิ่มกำหนดการซ่อม</button>
                </form>
            </div>
        </div>

        <div class="container content mt-5">
            <h3 class="text-center mb-4">ข้อมูลกำหนดการซ่อม</h3>
            <table class="table table-striped table-hover table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>รหัสครุภัณฑ์</th>
                        <th>รูปภาพ</th>
                        <th>ชื่อครุภัณฑ์</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>วันที่ซ่อมแซม</th>
                        <th>วันที่ซ่อมเสร็จ</th>
                        <th>สถานะ</th>
                        <th>อัปเดตล่าสุด</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data = $result_data->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($data['bib'] ?? '-'); ?></td>
                    <td><img src="uploads/<?= htmlspecialchars($data['image'] ?? '-'); ?>" alt="Image" width="80"></td>
                    <td><?= htmlspecialchars($data['name'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($data['brand'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($data['model'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($data['repair_date'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($data['repair_complete_date'] ?? '-'); ?></td>
                    <td>
                        <?php if ($data['status_rs'] == 1): ?>
                        <span class="status-dot status-blue"></span> รอดำเนินการ
                        <?php elseif ($data['status_rs'] == 2): ?>
                        <span class="status-dot status-orange"></span> กำลังดำเนินการ
                        <?php elseif ($data['status_rs'] == 3): ?>
                        <span class="status-dot status-green"></span> ดำเนินการเสร็จสิ้น
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($data['updated_at']); ?></td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="edit_repair_schedule.php?id=<?= $data['id']; ?>" class="btn btn-warning btn-sm me-2">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </a>
                                    <a href="?delete=<?= $data['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณต้องการลบข้อมูลนี้ใช่ไหม?');">
                                        <i class="bi bi-trash"></i> ลบ
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
