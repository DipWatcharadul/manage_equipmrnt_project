<?php
require 'connect.php'; // เชื่อมต่อฐานข้อมูล

$message = ''; // สำหรับข้อความสถานะ
$categories = []; // อาเรย์สำหรับเก็บหมวดหมู่
$equipments = []; // อาเรย์สำหรับเก็บข้อมูลครุภัณฑ์

// ฟังก์ชันสำหรับเช็คหมวดหมู่ครุภัณฑ์
function checkCategories($conn) {
    $sql = "SELECT DISTINCT category FROM equipment";
    return $conn->query($sql);
}

// ฟังก์ชันสำหรับดึงข้อมูลครุภัณฑ์ในหมวดหมู่ที่เลือก
function getEquipmentsByCategory($conn, $category) {
    $sql = "SELECT * FROM equipment WHERE category = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $category);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    return null; // ถ้าเตรียมคำสั่งไม่สำเร็จ
}

// เช็คหมวดหมู่ทั้งหมด
$result = checkCategories($conn);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category'])) {
    $selectedCategory = htmlspecialchars(trim($_POST['category']));
    $equipments = getEquipmentsByCategory($conn, $selectedCategory);
}

// ปิดการเชื่อมต่อหลังจากใช้งานเสร็จสิ้น
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช็คหมวดหมู่ครุภัณฑ์</title>
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
        h2, h3 {
            color: white; /* เปลี่ยนสีตัวอักษรสำหรับ h2 */
        }
        table {
            background-color: white; /* สีพื้นหลังของตาราง */
        }
    </style>
</head>
<body>
 <!-- Navbar -->
 <nav class="navbar navbar-expand-lg" style="background-color: #007bff;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="user.php">
                <i class="bi bi-tools"></i> ระบบส่งซ่อมครุภัณฑ์
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="user.php"><i class="bi bi-house"></i> หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="search_equipment.php">
                            <i class="bi bi-search"></i> ค้นหาครุภัณฑ์
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="category_equipment.php">
                            <i class="bi bi-box"></i> หมวดหมู่ครุภัณฑ์
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="notifications.php">
                            <i class="bi-bell"></i> แจ้งเตือน
                            <span class="badge bg-danger">
                                <?php
                                // นับการแจ้งเตือนที่ยังไม่ได้อ่าน
                                $sql_count = "SELECT COUNT(*) as count FROM notifications WHERE user_id = 1 AND is_read = 0";
                                $result_count = $conn->query($sql_count);
                                if ($result_count && $unread_count = $result_count->fetch_assoc()) {
                                    echo $unread_count['count'];
                                } else {
                                    echo 0; // หากไม่มีการแจ้งเตือน
                                }
                                ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="profile.php">
                            <i class="bi-person-circle"></i> โปรไฟล์
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<div class="container mt-5">
    <h2 class="mb-4">เช็คหมวดหมู่ครุภัณฑ์</h2>

    <form action="" method="post">
        <div class="form-group">
            <select class="form-control" id="category" name="category" required>
                <option value="">-- เลือกหมวดหมู่ --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">แสดงข้อมูลครุภัณฑ์</button>
    </form>

    <?php if (!empty($equipments)): ?>
    <h3 class="mt-4">ข้อมูลครุภัณฑ์ในหมวดหมู่ "<?php echo htmlspecialchars($selectedCategory); ?>"</h3>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>รหัสครุภัณฑ์</th>
                <th>ชื่อ</th>
                <th>หมวดหมู่</th>
                <th>จำนวน</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($equipment = $equipments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($equipment['bib']); ?></td>
                    <td><?php echo htmlspecialchars($equipment['name']); ?></td>
                    <td><?php echo isset($equipment['category']) ? htmlspecialchars($equipment['category']) : 'ไม่มีข้อมูล'; ?></td>
                    <td><?php echo isset($equipment['quantity']) ? htmlspecialchars($equipment['quantity']) : 'ไม่มีข้อมูล'; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-warning">ไม่พบข้อมูลครุภัณฑ์ในหมวดหมู่นี้</div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// ปิดการเชื่อมต่อ
$conn->close();
?>
