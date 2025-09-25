<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";
// ดงึขอ้ มลู สมำชกิ
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// เมอื่ มกี ำรสง่ ฟอรม์
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    // ตรวจสอบชอื่ และอเีมลไมว่ ำ่ ง
    if (empty($full_name) || empty($email)) {
        $errors[] = "กรณุ ำกรอกชอื่ -นำมสกุลและอีเมล";
    }
    // ตรวจสอบอเีมลซ ้ำ
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "อเีมลนถี้ กู ใชง้ำนแลว้";
    }
    // ตรวจสอบกำรเปลี่ยนรหัสผ่ำน (ถ ้ำมี)
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "รหัสผ่ำนเดิมไม่ถูกต ้อง";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "รหัสผ่ำนใหม่ต ้องมีอย่ำงน้อย 6 ตัวอักษร";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "รหัสผ่ำนใหม่และกำรยืนยันไม่ตรงกัน";
        } else {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }
    // อัปเดตข ้อมูลหำกไม่มี error
    if (empty($errors)) {
        if (!empty($new_hashed)) {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, password = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $new_hashed, $user_id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $user_id]);
        }
        $success = "บันทึกข ้อมูลเรียบร ้อยแล้ว";
        // อัปเดต session หำกจ ำเป็น
        $_SESSION['username'] = $user['username'];
        $user['full_name'] = $full_name;
        $user['email'] = $email;
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์สมาชิก - OnlineShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/font/LINESeedSansTH.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-store me-2"></i>OnlineShop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-2">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item d-flex justify-content-center align-items-center">
                            <span class="welcome-text me-3"> สวัสดี, <?= htmlspecialchars($_SESSION['fullname']) ?> (<?= $_SESSION['role'] ?>)
                            </span>
                        </li>
                        <li class="nav-item d-flex">
                            <a class="btn btn-outline-primary btn-sm border border-0 active" href="profile.php">
                                <i class="fa-solid fa-user fa-2x"></i>
                            </a>
                        </li>
                        <li class="nav-item d-flex">
                            <a class="btn btn-outline-primary btn-sm border border-0" href="cart.php">
                                <i class="fa-solid fa-cart-shopping fa-2x"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-primary btn-sm" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>ออกจากระบบ
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-primary btn-sm" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>เข้าสู่ระบบ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>สมัครสมาชิก
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="main-container p-4 p-md-5">
            <h1 class="page-title">
                <i class="fas fa-user-circle me-2"></i>โปรไฟล์ของคุณ
            </h1>
            <a href="index.php" class="btn btn-secondary mb-4">
                <i class="fas fa-arrow-left me-2"></i>กลับหน้าหลัก
            </a>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger glass-morphism">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif (!empty($success)): ?>
                <div class="alert alert-success glass-morphism">
                    <i class="fas fa-check-circle me-2"></i><?= $success ?>
                </div>
            <?php endif; ?>
            
            <div class="glass-morphism p-4">
                <form method="post" class="row g-3">
                    <div class="col-12 mb-3">
                        <h5 class="gradient-text">
                            <i class="fas fa-user-edit me-2"></i>ข้อมูลส่วนตัว
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label for="full_name" class="form-label">
                            <i class="fas fa-user me-2"></i>ชื่อ-นามสกุล
                        </label>
                        <input type="text" name="full_name" class="form-control" required 
                               value="<?= htmlspecialchars($user['full_name']) ?>"
                               placeholder="กรอกชื่อ-นามสกุลของคุณ">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>อีเมล
                        </label>
                        <input type="email" name="email" class="form-control" required 
                               value="<?= htmlspecialchars($user['email']) ?>"
                               placeholder="กรอกอีเมลของคุณ">
                    </div>
                    
                    <div class="col-12 mt-4">
                        <hr class="my-4">
                        <h5 class="gradient-text">
                            <i class="fas fa-lock me-2"></i>เปลี่ยนรหัสผ่าน (ไม่จำเป็น)
                        </h5>
                        <p class="text-muted small">หากคุณต้องการเปลี่ยนรหัสผ่าน กรุณากรอกข้อมูลด้านล่าง</p>
                    </div>
                    <div class="col-md-4">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-key me-2"></i>รหัสผ่านเดิม
                        </label>
                        <input type="password" name="current_password" id="current_password" class="form-control"
                               placeholder="กรอกรหัสผ่านเดิม">
                    </div>
                    <div class="col-md-4">
                        <label for="new_password" class="form-label">
                            <i class="fas fa-unlock-alt me-2"></i>รหัสผ่านใหม่ (≥ 6 ตัวอักษร)
                        </label>
                        <input type="password" name="new_password" id="new_password" class="form-control"
                               placeholder="กรอกรหัสผ่านใหม่">
                    </div>
                    <div class="col-md-4">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-check-double me-2"></i>ยืนยันรหัสผ่านใหม่
                        </label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                               placeholder="ยืนยันรหัสผ่านใหม่">
                    </div>
                    
                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>