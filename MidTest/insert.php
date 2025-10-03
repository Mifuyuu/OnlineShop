<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $std_id = trim($_POST['std_id']);
    $f_name = trim($_POST['f_name']);
    $l_name = trim($_POST['l_name']);
    $age = trim($_POST['age']);
    $mail = trim($_POST['mail']);
    $tel = trim($_POST['tel']);

    if (empty($std_id) || empty($f_name) || empty($l_name) || empty($age) || empty($mail) || empty($tel)) $error[] = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) $error[] = 'กรุณากรอกอีเมลให้ถูกต้อง';
    else {

        $sql = "SELECT * FROM tb_664230033 WHERE std_id = ? OR mail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$std_id, $mail]);

        if ($stmt->rowCount() > 0) {
            $error[] = 'มีรหัสนักศึกษานี้อยู่แล้ว หรือ อีเมลนี้ถูกใช้ไปแล้ว';
        }
    }

    if (empty($error)) {

        $sql = "INSERT INTO tb_664230033(std_id, f_name, l_name, age, mail, tel) VALUES (?, ?, ?, ?, ?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$std_id, $f_name, $l_name, $age, $mail, $tel]);

        header('Location: index.php?insert=success');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MidTest - Insert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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

        /* .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            transform: translateY(-2px);
        } */

        a:hover,
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        /* 
        .btn-link {
            color: #8b77fcff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-link:hover {
            color: #9251faff;
            text-decoration: underline;
        } */

        /* .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
            font-family: 'LINE Seed Sans TH', sans-serif;
        } */

        .register-title {
            color: #2c3e50;
            font-weight: 500;
            margin-bottom: 30px;
            text-align: center;
            font-family: 'LINE Seed Sans TH', sans-serif;
        }

        /* .form-floating .form-control {
            padding: 1rem 0.75rem;
        } */

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
                    <h2 class="register-title"><i class="fas fa-user-plus me-2"></i>เพิ่มนักศึกษา</h2>
                    <?php if (!empty($error)):
                    ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 list-unstyled">
                                <?php foreach ($error as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                    <?php
                                    ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="std_id" class=" form-label">รหัสนักศึกษา</label>
                                <input type="text" name="std_id" id="student_id" placeholder="เช่น 66423XXXX" class="form-control" value="<?= isset($_POST['std_id']) ? htmlspecialchars($_POST['std_id']) : '' ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="f_name" class=" form-label">ชื่อ</label>
                                <input type="text" name="f_name" id="first_name" placeholder="กรอกชื่อ" class="form-control" value="<?= isset($_POST['f_name']) ? htmlspecialchars($_POST['f_name']) : '' ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="l_name" class=" form-label">นามสกุล</label>
                                <input type="text" name="l_name" id="last_name" placeholder="กรอกนามสกุล" class="form-control" value="<?= isset($_POST['l_name']) ? htmlspecialchars($_POST['l_name']) : '' ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="age" class=" form-label">อายุ</label>
                                <input min="0" type="number" name="age" id="age" placeholder="กรอกอายุ" class="form-control" value="<?= isset($_POST['age']) ? htmlspecialchars($_POST['age']) : '' ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="mail" class=" form-label">อีเมล</label>
                                <input type="text" name="mail" id="email" placeholder="example@gmail.com" class="form-control" value="<?= isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : '' ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="phone" class=" form-label">โทรศัพท์</label>
                                <input type="text" name="tel" id="phone" placeholder="092XXXXXXX" class="form-control" value="<?= isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '' ?>">
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary btn-lg flex-fill">ดูรายการ
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">เพิ่มข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // SweetAlert for Add/Update Success
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?= $_SESSION['success'] ?>',
                showConfirmButton: false,
                timer: 2000
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '<?= $_SESSION['error'] ?>',
                confirmButtonColor: '#667eea'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>

</html>