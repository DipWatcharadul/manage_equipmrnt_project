<?php
// เริ่มต้นการใช้งาน session
session_start();

if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

require_once 'connect.php';

// เช็คว่าได้รับ ID หรือไม่
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // ดึงข้อมูลครุภัณฑ์จากฐานข้อมูล
    $sql = "SELECT * FROM equipment WHERE id_equipment = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipment = $result->fetch_assoc();
}

// ถ้ามีการส่งข้อมูลมาจากฟอร์ม (เมื่อกด submit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $name = $_POST['name'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $status = $_POST['status_e'];
    $remarks = $_POST['remarks']; // รับค่าหมายเหตุ
    $category = $_POST['category']; // รับค่าหมวดหมู่
    $brand = $_POST['brand']; // รับค่ายี่ห้อ
    $model = $_POST['model']; // รับค่ารุ่น
    $year = $_POST['year'];  // รับค่าปีงบประมาณจากฟอร์ม
    $user = $_POST['user']; // รับค่าผู้ใช้งาน

    // ตรวจสอบการอัปโหลดไฟล์ภาพ
    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        $image = $equipment['image']; // ใช้ภาพเดิมหากไม่มีการอัปโหลด
    }
    // ตรวจสอบค่าของ 'year' ก่อน
if (empty($year)) {
    echo "กรุณากรอกปีงบประมาณ.";
    exit();  // หยุดการทำงานหากค่าของ 'year' เป็นค่าว่าง
}

    // อัปเดตข้อมูลครุภัณฑ์
$update_sql = "UPDATE equipment 
                SET name = ?, description = ?, address = ?, status_e = ?, remarks = ?, category = ?, brand = ?, model = ?, image = ?, user_id = ?, byear = ? 
                WHERE id_equipment = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ssssssssssii", $name, $description, $address, $status, $remarks, $category, $brand, $model, $image, $user, $year, $id);


    if ($update_stmt->execute()) {
        // Redirect ไปยังหน้า manage_equipment.php เมื่อทำการอัปเดตเสร็จ
        header('Location: manage_equipment.php');
        exit();
    } else {
        echo "ไม่สามารถอัปเดตข้อมูลได้.";
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
</head>
<!-- ปุ่มย้อนกลับ -->
<div class="container mt-3">
    <a href="manage_equipment.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> ย้อนกลับ
    </a>
</div>
<body>
    <!-- ฟอร์มแก้ไขครุภัณฑ์ -->
    <div class="container mt-5">
        <h2>แก้ไขครุภัณฑ์</h2>
        <form action="edit_equipment.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อครุภัณฑ์</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $equipment['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="brand" class="form-label">ยี่ห้อ</label>
                <input type="text" class="form-control" id="brand" name="brand" value="<?php echo $equipment['brand']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="model" class="form-label">รุ่น</label>
                <input type="text" class="form-control" id="model" name="model" value="<?php echo $equipment['model']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">หมวดหมู่</label>
                <input type="text" class="form-control" id="category" name="category" value="<?php echo $equipment['category']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="purchase_date" class="form-label">วันที่ซื้อครุภัณฑ์</label>
                <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?php echo $equipment['purchase_date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">รายละเอียดครุภัณฑ์</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $equipment['description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="remarks" class="form-label">หมายเหตุ</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo $equipment['remarks']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่ครุภัณฑ์</label>
                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $equipment['address']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">สถานะครุภัณฑ์</label>
                <select class="form-select" id="status" name="status_e" required>
                    <option value="1" <?php echo $equipment['status_e'] == 1 ? 'selected' : ''; ?>>พร้อมใช้งาน</option>
                    <option value="2" <?php echo $equipment['status_e'] == 2 ? 'selected' : ''; ?>>ส่งซ่อม</option>
                    <option value="3" <?php echo $equipment['status_e'] == 3 ? 'selected' : ''; ?>>เสียหาย</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="year" class="form-label">ปีงบประมาณ</label>
                <input type="number" class="form-control" id="year" name="year" value="<?php echo isset($equipment['year']) ? $equipment['year'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">รูปครุภัณฑ์</label>
                <input type="file" class="form-control" id="image" name="image">
                <img src="uploads/<?php echo $equipment['image']; ?>" style="width: 100px; height: auto;" class="mt-2">
            </div>
            <div class="mb-3">
                <label for="user" class="form-label">ชื่อผู้ใช้งาน</label>
                <select class="form-select" id="user" name="user" required>
                    <?php
                    // ดึงรายชื่อผู้ใช้งานที่ไม่ใช่ "admin" และ "Admin2"
                    $sql_users = "SELECT id_user, username FROM users WHERE username NOT IN ('admin', 'Admin2')";
                    $result_users = mysqli_query($conn, $sql_users);
                    while ($row_user = mysqli_fetch_assoc($result_users)) {
                        echo "<option value='" . $row_user['id_user'] . "' " . ($equipment['user_id'] == $row_user['id_user'] ? 'selected' : '') . ">" . $row_user['username'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
        </form>
    </div>

    <!-- เรียกใช้ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
