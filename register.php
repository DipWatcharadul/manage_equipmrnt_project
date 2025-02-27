<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียน</title>
    <link rel="icon" href="image/logo.png" type="image/png"> <!-- เพิ่มโลโก้ในแท็บเบราว์เซอร์ -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(145deg, #1E3A8A, #3B82F6);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
        }
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        .link-custom {
            color: yellow;
            text-decoration: none;
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            text-align: center; /* Center align content inside */
        }
        .form-container img {
            display: block;
            margin: 0 auto;
            width: 65%;
            height: auto;
            margin-top: 10rem; /* เพิ่มระยะห่างด้านล่างรูป */
        }
        h2 {
            text-align: center;
            margin-top: 1rem;
            color: white;
        }
        label {
            color: white;
        }
        .btn-primary {
            background-color: #FFD700;
            border-color: #FFD700;
            color: #000;
        }
        .btn-primary:hover {
            background-color: #ffdf33;
            border-color: #ffdf33;
            color: #000;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="form-container">
            <img src="image/56.png" alt="Logo">
            <h2>สมัครสมาชิก</h2>
            <form action="register_process.php" method="post">
                <div class="form-group">
                    <label for="username">ชื่อผู้ใช้:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">รหัสผ่าน:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">ยืนยันรหัสผ่าน:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="email">อีเมล:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="firstname">ชื่อจริง:</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                </div>
                <div class="form-group">
                    <label for="lastname">นามสกุล:</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">ลงทะเบียน</button>
            </form>
            <div class="text-center my-3">
                <label>ถ้าคุณมีสมาชิก?</label>
                <a href="login.php" class="link-custom">เข้าสู่ระบบ</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
