<?php 
// เริ่มต้นการใช้งาน session
session_start();

if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

require_once 'connect.php';

// ตรวจสอบว่ามีการส่งค่า ID กำหนดการซ่อมมาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ไม่พบข้อมูลที่ต้องการแก้ไข";
    exit();
}

$schedule_id = (int) $_GET['id']; // แปลงเป็นตัวเลขเพื่อความปลอดภัย

// ดึงข้อมูลของกำหนดการซ่อมที่ต้องการแก้ไข
$sql_rh = "SELECT a.*, c.bib, c.name, c.id_equipment 
           FROM repair_schedule a 
           INNER JOIN equipment c ON a.equipment_id = c.id_equipment
           WHERE a.id = ?";
$stmt = $conn->prepare($sql_rh);
if (!$stmt) {
    echo "เกิดข้อผิดพลาด: " . $conn->error;
    exit();
}
$stmt->bind_param('i', $schedule_id);
$stmt->execute();
$result_data = $stmt->get_result();
$data = $result_data->fetch_assoc();

if (!$data) {
    echo "ไม่พบข้อมูลของกำหนดการซ่อมที่ต้องการแก้ไข";
    exit();
}

// ดึงข้อมูลครุภัณฑ์ทั้งหมดสำหรับ dropdown
$sql_equipment = "SELECT id_equipment, bib, name FROM equipment ORDER BY name ASC";
$result_equipment = mysqli_query($conn, $sql_equipment);
if (!$result_equipment) {
    echo "เกิดข้อผิดพลาด: " . $conn->error;
    exit();
}

// ตัวเลือกสถานะ
$status_options = [
    1 => "รอดำเนินการ",
    2 => "กำลังดำเนินการ",
    3 => "ดำเนินเสร็จสิ้น"
];
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
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" style="background-color: #007bff;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="admin.php">
                <i class="bi bi-tools"></i> DIPPEEFASTWORK - ผู้ดูแลระบบ
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="admin.php"><i class="bi bi-house"></i> หน้าแรก</a>
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

<!-- ปุ่มย้อนกลับ -->   
<div class="container mt-3">
    <a href="manage_repair_schedule.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> ย้อนกลับ
    </a>
</div>

<div class="container mt-5">
    <h2>จัดการกำหนดการซ่อมครุภัณฑ์</h2>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">แก้ไขกำหนดการซ่อม</h5>
            <form action="update_schedule.php" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id']); ?>">

                <div class="mb-3">
                    <label for="equipment_id" class="form-label">ครุภัณฑ์</label>
                    <select class="form-control" id="equipment_id" name="equipment_id" required>
                        <option value="" disabled>-- เลือกครุภัณฑ์ --</option>
                        <?php while ($row_equipment = mysqli_fetch_assoc($result_equipment)) { 
                            $selected = $row_equipment['id_equipment'] == $data['id_equipment'] ? 'selected' : ''; ?>
                            <option value="<?php echo htmlspecialchars($row_equipment['id_equipment']); ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($row_equipment['bib'] . " " . $row_equipment['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="repair_date" class="form-label">วันที่ซ่อม</label>
                    <input type="date" class="form-control" id="repair_date" name="repair_date" value="<?php echo htmlspecialchars($data['repair_date']); ?>" required>
                </div> 
                
                <div class="mb-3">
                    <label for="repair_complete_date" class="form-label">วันที่ซ่อมเสร็จ</label>
                    <input type="date" class="form-control" id="repair_complete_date" name="repair_complete_date" value="<?php echo htmlspecialchars($data['repair_complete_date']); ?>" required>
                </div>        
                
                <div class="mb-3">
                    <label for="status_rs" class="form-label">สถานะการซ่อม</label>
                    <select class="form-select" id="status_rs" name="status_rs" required>
                        <?php foreach ($status_options as $key => $label) { ?>
                            <option value="<?php echo $key; ?>" <?php echo $data['status_rs'] == $key ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit" name="update" class="btn btn-primary">บันทึกการแก้ไข</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
