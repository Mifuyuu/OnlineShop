<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //รับค่าจาก form
    $username = trim($_POST['user']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_pass']);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    // นำข้อมูลบันทึกลงฐานข้อมูล
    $sql = "INSERT INTO users(username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'admin');";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $fullname, $email, $hashedPassword]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnlineShop - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/font/LINESeedSansTH.css">
    <style>
        body {
            background-image: url('assets/img/background.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            font-family: 'LINE Seed Sans TH', 'Segoe UI', Verdana, sans-serif;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            transform: translateY(-2px);
        }

        .btn-primary {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 400;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-link {
            color: #8b77fcff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-link:hover {
            color: #9251faff;
            text-decoration: underline;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
            font-family: 'LINE Seed Sans TH', sans-serif;
        }

        .register-title {
            color: #2c3e50;
            font-weight: 500;
            margin-bottom: 30px;
            text-align: center;
            font-family: 'LINE Seed Sans TH', sans-serif;
        }

        .form-floating .form-control {
            padding: 1rem 0.75rem;
        }

        @media (max-width: 768px) {
            .register-container {
                margin: 20px;
                padding: 20px !important;
            }

            .register-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="register-container p-4 p-md-5">
                    <h2 class="register-title">สมัครสมาชิก</h2>
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="user" id="user" placeholder="กรอกชื่อผู้ใช้" class="form-control" required>
                                    <label for="user">ชื่อผู้ใช้</label>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="fullname" id="fullname" placeholder="กรอกชื่อ-นามสกุล" class="form-control" required>
                                    <label for="fullname">ชื่อ-นามสกุล</label>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="email" name="email" id="email" placeholder="กรอกอีเมล" class="form-control" required>
                                    <label for="email">อีเมล</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="password" name="password" id="password" placeholder="กรอกรหัสผ่าน" class="form-control" required minlength="6">
                                    <label for="password">รหัสผ่าน</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="password" name="confirm_pass" id="confirm_pass" placeholder="ยืนยันรหัส" class="form-control" required minlength="6">
                                    <label for="confirm_pass">ยืนยันรหัสผ่าน</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg me-md-2 flex-fill flex-md-grow-0">
                                <i class="fas fa-user-plus me-2"></i>สมัครสมาชิก
                            </button>
                        </div>
                        <div class="text-center mt-3">
                            <span class="text-muted">มีบัญชีอยู่แล้ว? </span>
                            <a href="login.php" class="btn-link">เข้าสู่ระบบ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>