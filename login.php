<?php
session_start();
require_once 'config.php';
$error=[];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //รับค่าจาก form
    $username_or_email = trim($_POST['user_or_email']);
    $password = trim($_POST['password']);

    //เอาค่าที่รับมาจาก form ไปตรวจสอบว่ามีข้อมูลตรงกันใน db หรือไม่
    $sql = 'SELECT * FROM users WHERE (username = ? OR email = ?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username_or_email, $username_or_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') header('Location: index.php');
        else header('Location: index.php');
        exit();
    } else {
        $error[] = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnlineShop - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                    <h2 class="register-title">เข้าสู่ระบบ</h2>
                    <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
                        <div class="alert alert-success">สมัครสมาชิกสำเร็จ กรณุาเข้าสู่ระบบ</div>
                    <?php endif; ?>
                    <?php foreach ($error as $e): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="user_or_email" id="user_or_email" placeholder="กรอกชื่อผู้ใช้ หรือ อีเมล" class="form-control" value="<?= isset($_POST['user_or_email']) ? htmlspecialchars($_POST['user_or_email']) : '' ?>">
                                    <label for="user">ชื่อผู้ใช้</label>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="password" name="password" id="password" placeholder="กรอกรหัสผ่าน" class="form-control">
                                    <label for="password">รหัสผ่าน</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg me-md-2 flex-fill flex-md-grow-0">
                                <i class="fa-solid fa-right-to-bracket me-2"></i>เข้าสู่ระบบ
                            </button>
                        </div>
                        <div class="text-center mt-3">
                            <span class="text-muted">ยังไม่มีบัญชี? </span>
                            <a href="register.php" class="btn-link">สมัครสมาชิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>