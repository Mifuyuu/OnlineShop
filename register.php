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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>สมัครสมาชิก</h2>
        <form action="" method="post">
            <div>
                <label for="user" class="form-label">ชื่อผู้ใช้</label>
                <input type="text" name="user" id="user" placeholder="กรอกชื่อผู้ใช้" class="form-control">
            </div>
            <div>
                <label for="fullname" class="form-label">ชื่อ-นามสกุล</label>
                <input type="text" name="fullname" id="fullname" placeholder="กรอกชื่อ-นามสกุล" class="form-control">
            </div>
            <div>
                <label for="email" class="form-label">อีเมล</label>
                <input type="text" name="email" id="email" placeholder="กรอกอีเมล" class="form-control">
            </div>
            <div>
                <label for="pass" class="form-label">รหัสผ่าน</label>
                <input type="password" name="password" id="password" placeholder="กรอกรหัสผ่าน" class="form-control">
            </div>
            <div>
                <label for="confirm_pass" class="form-label">ยืนยันรหัสผ่าน</label>
                <input type="password" name="confirm_pass" id="confirm_pass" placeholder="ยืนยันรหัส" class="form-control">
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
                <a href="login.php" class="btn btn-link">เข้าสู่ระบบ</a>
            </div>
        </form>
    </div>
</body>

</html>