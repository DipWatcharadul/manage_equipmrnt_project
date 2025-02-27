<?php
require 'connect.php'; // เชื่อมต่อฐานข้อมูล

session_start();

// ตรวจสอบว่าผู้ใช้เป็นผู้ดูแลระบบหรือไม่
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['admin_login'];

// ฟังก์ชันสำหรับดึงข้อมูลโปรไฟล์ผู้ใช้จากฐานข้อมูล
function getUserProfile($conn, $userId) {
    $sql = "SELECT u.username, u.fname, u.lname, u.profile_image, u.email, u.phone, 
                   u.job_position, u.company, u.create_at, u.address , u.birth_date , u.create_at
            FROM users u
            WHERE u.id_user = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function calculateAge($birthDate) {
    if (empty($birthDate)) {
        return "ไม่มีข้อมูลวันเกิด";
    }
    
    try {
        $birthDate = new DateTime($birthDate);
        $today = new DateTime('today');
        $age = $today->diff($birthDate);
        return $age->y; // คืนค่าอายุ
    } catch (Exception $e) {
        return "ข้อมูลวันเกิดไม่ถูกต้อง";
    }
}

// ดึงข้อมูลโปรไฟล์ผู้ใช้
$userProfile = getUserProfile($conn, $userId);

// ฟังก์ชันอัปโหลดรูปโปรไฟล์
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $profileImage = $_FILES['profile_image'];
    $imageName = $profileImage['name'];
    $imageTmpName = $profileImage['tmp_name'];
    $imageSize = $profileImage['size'];
    $imageError = $profileImage['error'];

    if ($imageError === 0) {
        if ($imageSize < 5000000) { // ขนาดไฟล์ไม่เกิน 5MB
            $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
            $imageExtensionLower = strtolower($imageExtension);

            if (in_array($imageExtensionLower, ['jpg', 'jpeg', 'png'])) {
                $newImageName = uniqid('', true) . "." . $imageExtensionLower;
                $imageUploadPath = 'uploads/' . $newImageName;

                if (move_uploaded_file($imageTmpName, $imageUploadPath)) {
                    $sql = "UPDATE users SET profile_image = ? WHERE id_user = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $imageUploadPath, $userId);
                    $stmt->execute();

                    // รีไดเร็กต์ไปยังหน้าโปรไฟล์หลังอัปโหลดสำเร็จ
                    header("Location: admin_profile.php");
                    exit();  // หยุดการทำงานหลังจากรีไดเร็กต์
                } else {
                    echo "เกิดข้อผิดพลาดในการย้ายไฟล์";
                }
            } else {
                echo "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพที่รองรับ (.jpg, .jpeg, .png)";
            }
        } else {
            echo "ขนาดไฟล์ใหญ่เกินไป กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 5MB";
        }
    } else {
        echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์: " . $imageError;
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
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
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            padding: 0;
            background-attachment: fixed;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0; /* สีพื้นหลังเมื่อไม่มีรูป */
        }
        .file-input {
            display: none;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
        }
        .card-header h3 {
            margin: 0;
        }
        
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

    <!-- User Profile -->
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>โปรไฟล์</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column align-items-center">
                    <!-- หากไม่มีรูปโปรไฟล์จะแสดงไอคอนแทน -->
                    <label for="profile_image" class="profile-img rounded-circle mb-3">
                        <?php if (empty($userProfile['profile_image'])): ?>
                            <!-- ไอคอนแทนรูปโปรไฟล์ -->
                            <i class="bi bi-person-circle"></i>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($userProfile['profile_image']); ?>" alt="Profile Image" class="profile-img rounded-circle">
                        <?php endif; ?>
                    </label>
                    <form action="admin_profile.php" method="POST" enctype="multipart/form-data" id="profileForm">
                        <input type="file" name="profile_image" id="profile_image" class="file-input" accept="image/*">
                    </form>
                    <h4><?php echo htmlspecialchars($userProfile['username']); ?></h4>
                    <!-- User Info -->
                    <div class="row w-100">
                        <div class="col-md-6">
                            <p><strong>ชื่อจริง:</strong> <?php echo htmlspecialchars($userProfile['fname'] ?? '-'); ?></p>
                            <p><strong>เพศ:</strong> <?php echo htmlspecialchars($userProfile['gender'] ?? '-'); ?></p>
                            <p><strong>เบอร์โทร:</strong> <?php echo htmlspecialchars($userProfile['phone'] ?? '-'); ?></p>
                            <p><strong>บริษัท/สถาบัน:</strong> <?php echo htmlspecialchars($userProfile['company'] ?? '-'); ?></p>
                            <p><strong>อายุ:</strong> <?php echo calculateAge($userProfile['birth_date']); ?> ปี</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>นามสกุล:</strong> <?php echo htmlspecialchars($userProfile['lname'] ?? '-'); ?></p>
                            <p><strong>อีเมล:</strong> <?php echo htmlspecialchars($userProfile['email'] ?? '-'); ?></p>
                            <p><strong>ตำแหน่งงาน:</strong> <?php echo htmlspecialchars($userProfile['job_position'] ?? '-'); ?></p>
                            <p><strong>วันเกิด:</strong> <?php echo htmlspecialchars($userProfile['birth_date'] ?? 'ไม่มีข้อมูลวันเกิด'); ?></p>
                            <p><strong>ที่อยู่:</strong> <?php echo htmlspecialchars($userProfile['address'] ?? '-'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Manage Buttons -->
                <div class="mt-4">
                    <a href="admin_edit_profile.php" class="btn btn-primary btn-custom">แก้ไขโปรไฟล์</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // เมื่อเลือกไฟล์รูปภาพแล้วให้ส่งฟอร์มไปอัปโหลดอัตโนมัติ
        document.getElementById('profile_image').addEventListener('change', function() {
            document.getElementById('profileForm').submit(); // ส่งฟอร์ม
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>