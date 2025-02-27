<?php
// เริ่มต้นการใช้งาน session
session_start();

if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

require_once 'connect.php';

// ดึงข้อมูลจากตาราง equipment และ users โดยใช้ user_id
$sql = "SELECT a.*, b.username, c.name_e AS status_name 
        FROM equipment a
        INNER JOIN users b ON a.user_id = b.id_user
        INNER JOIN status_equipment c ON a.status_e = c.id_e";
$result = mysqli_query($conn, $sql);

// ดึงข้อมูลผู้ใช้งานที่ไม่ใช่ admin
$sql_users = "SELECT id_user, username FROM users WHERE username NOT IN ('admin', 'Admin2')";
$result_users = mysqli_query($conn, $sql_users);
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
        .navbar {
            z-index: 2500;
            background-color: rgba(0, 123, 255, 0.8);
        }
        .table {
            border-radius: 10px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
            font-size: 0.9rem;
        }
        .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
            animation: blink 1.5s infinite;
        }

        .status-dot.active {
            background-color: green;
        }

        .status-dot.repairing {
            background-color: orange;
        }

        .status-dot.decommissioned {
            background-color: red;
        }

        @keyframes blink {
            50% {
                opacity: 0.5;
            }
        }
    </style>
</head>
<body>
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

    <div class="container mt-5">
        <h2>จัดการครุภัณฑ์</h2>

        <!-- ฟอร์มเพิ่มครุภัณฑ์ -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">เพิ่มครุภัณฑ์</h5>
                <form action="insert_equipment.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="bib" class="form-label">รหัสครุภัณฑ์</label>
                        <input type="text" class="form-control" id="bib" name="bib" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อครุภัณฑ์</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="brand" class="form-label">ยี่ห้อ</label>
                        <input type="text" class="form-control" id="brand" name="brand" required>
                    </div>
                    <div class="mb-3">
                        <label for="model" class="form-label">รุ่น</label>
                        <input type="text" class="form-control" id="model" name="model" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">หมวดหมู่</label>
                        <input type="text" class="form-control" id="category" name="category" required>
                    </div>
                    <div class="mb-3">
                        <label for="purchase" class="form-label">วันที่ซื้อครุภัณฑ์</label>
                        <input type="date" class="form-control" id="purchase" name="purchase" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">รายละเอียดครุภัณฑ์</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">สถานที่จัดเก็บ</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">ปีงบประมาณ</label>
                        <input type="number" class="form-control" id="year" name="year" required>
                    </div>
                    <div class="mb-3">
                        <label for="user" class="form-label">ผู้ใช้งาน</label>
                        <select class="form-select" id="user" name="user" required>
                            <?php while ($row_user = mysqli_fetch_assoc($result_users)): ?>
                                <option value="<?php echo $row_user['id_user']; ?>"><?php echo $row_user['username']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">สถานะครุภัณฑ์</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="1" data-color="green">🟢 พร้อมใช้งาน</option>
                            <option value="2" data-color="orange">🟠 ส่งซ่อม</option>
                            <option value="3" data-color="red">🔴 ยกเลิกใช้งาน</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">รูปครุภัณฑ์</label>
                        <input type="file" class="form-control" id="image" name="image" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">เพิ่มครุภัณฑ์</button>
                </form>
            </div>
        </div>

        <div class="container content mt-5">
            <h3 class="text-center mb-4">ข้อมูลครุภัณฑ์</h3>
            <table class="table table-striped table-hover table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>รหัสครุภัณฑ์</th>
                        <th>รูปภาพ</th>
                        <th>ชื่อครุภัณฑ์</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>หมวดหมู่</th>
                        <th>รายละเอียด</th>
                        <th>สถานที่จัดเก็บ</th>
                        <th>หมายเหตุ</th>
                        <th>ผู้ใช้งาน</th>
                        <th>สถานะ</th>
                        <th>ปีงบประมาณ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // ตรวจสอบว่า query มีผลลัพธ์หรือไม่
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $status_class = '';
                            $status_text = '';
                            if ($row['status_e'] == 1) {
                                $status_class = 'active';
                                $status_text = 'พร้อมใช้งาน';
                            } elseif ($row['status_e'] == 2) {
                                $status_class = 'repairing';
                                $status_text = 'ส่งซ่อม';
                            } else {
                                $status_class = 'decommissioned';
                                $status_text = 'ยกเลิกใช้งาน';
                            }
                            ?>
                            <tr>
                                <td><?php echo $row['bib']; ?></td>
                                <td><img src="uploads/<?php echo $row['image']; ?>" alt="image" class="img-fluid" style="width: 100px;"></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['brand']; ?></td>
                                <td><?php echo $row['model']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td><?php echo $row['address']; ?></td>
                                <td><?php echo $row['remarks']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><span class="status-dot <?php echo $status_class; ?>"></span><?php echo $status_text; ?></td>
                                <td><?php echo $row['byear']; ?></td>
                        <td>
                            <a href="edit_equipment.php?id=<?php echo $row['id_equipment']; ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> แก้ไข
                            </a>
                            <a href="delete_equipment.php?id=<?php echo $row['id_equipment']; ?>" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> ลบ
                            </a>
                        </td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
