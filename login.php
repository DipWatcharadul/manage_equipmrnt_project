<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Hugo 0.122.0">
  <title>เข้าสู่ระบบ</title>
  <link rel="icon" href="image/logo.png" type="image/png"> <!-- เพิ่มโลโก้ในแท็บเบราว์เซอร์ -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      background: linear-gradient(145deg, #1E3A8A, #3B82F6);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      /* Add a reflective shine effect */
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
    .container {
      text-align: center;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .container img {
      max-width: 100%;
      height: auto;
      border-radius: 10px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    h1 {
      font-size: 3rem;
      margin-bottom: 20px;
    }

    p {
      font-size: 1.2rem;
    }
    .link-custom {
    color: yellow; /* กำหนดสีลิงก์เป็นสีเหลือง */
    text-decoration: none; /* ลบขีดเส้นใต้ */
  }

  .link-custom:hover {
    color: #f3f3f3; /* เปลี่ยนสีลิงก์เมื่อวางเมาส์ */
    text-decoration: underline; /* เพิ่มเส้นใต้เมื่อวางเมาส์ */
  }
  .text-center {
        text-align: center; /* จัดข้อความให้อยู่ตรงกลาง */
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


  <!-- Custom styles for this template -->
  <link href="sign-in.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
  <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="check2" viewBox="0 0 16 16">
      <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
    </symbol>
    <symbol id="circle-half" viewBox="0 0 16 16">
      <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z" />
    </symbol>
    <symbol id="moon-stars-fill" viewBox="0 0 16 16">
      <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z" />
      <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z" />
    </symbol>
    <symbol id="sun-fill" viewBox="0 0 16 16">
      <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z" />
    </symbol>
  </svg>

  <main class="form-signin w-100 m-auto">
  <form action="sign-in.php" method="post">
    <img class="mb-4" src="image/56.png" alt="" width="300" height="">
    <h1 class="h3 mb-3 fw-normal text-center">เข้าสู่ระบบ</h1>

    <div class="form-floating mb-3">
        <input type="text" class="form-control" id="floatingInput" name="username" placeholder="Username" required>
        <label for="floatingInput">ชื่อผู้ใช้</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
        <label for="floatingPassword">รหัสผ่าน</label>
    </div>

    <div class="form-check text-start my-3">
    <label class="form-check-label" for="flexCheckDefault">
        ยังไม่เป็นสมาชิกใช่ไหม ? 
        <a href="register.php" class="link-custom">ลงทะเบียน</a>
    </label>
</div>
    <button class="btn btn-primary w-100 py-2" name="signin" type="submit">เข้าสู่ระบบ</button>
</form>
  </main>

</body>

</html>