<?php
session_start();
require 'config.php';
// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือยัง
if (!isset($_SESSION['user_id'])) { // TODO: ใส่ชื่อ ตัวแปร session เก็บ user id
    header("Location: login.php"); // TODO: ใส่ หน้าที่ ใช้login
    exit;
}
$user_id = $_SESSION['user_id']; // TODO: กำหนดตัวแปร user_id จาก session
$errors = [];

// ดึงรายการสินค้ในตะกร้า
$stmt = $conn->prepare("SELECT cart.cart_id, cart.quantity, cart.product_id, products.product_name, products.price
FROM cart
JOIN products ON cart.product_id = products.product_id
WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// คำนวณราคารวม
$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price'];
}

// เมื่อลูกค้ากดยืนยันคำสั่งซื้อ (method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']); // TODO: ช่องกรอกที่อยู่
    $city = trim($_POST['city']); // TODO: ช่องกรอกจังหวัด
    $postal_code = trim($_POST['postal_code']); // TODO: ช่องกรอกรหัสไปรษณีย์
    $phone = trim($_POST['phone']); // TODO: ช่องกรอกเบอร์โทรศัพท์
    // ตรวจสอบกำรกรอกข้อมูล
    if (empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
        $errors[] = "กรุณากรอกข้อมูลให้ครบถ้วน"; // TODO: ข้อความแจ้งเตือนกรอกไม่ครบ
    }
    if (empty($errors)) {
        // เริ่ม transaction
        $conn->beginTransaction();
        try {
            // บันทึกข้อมูลการสั่งซื้อ
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$user_id, $total]);
            $order_id = $conn->lastInsertId();
            // บันทึกข้อมูลรายการสินค้าใน order_items
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmtItem->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                // TODO: product_id, quantity, price
            }
            // บันทึกข้อมูลการจัดส่ง
            $stmt = $conn->prepare("INSERT INTO shipping (order_id, address, city, postal_code, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $address, $city, $postal_code, $phone]);
            // ล้างตะกร้าสินค้า
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            // ยืนยันการบันทึก
            $conn->commit();
            header("Location: orders.php?success=1"); // TODO: หน้าสำหรับแสดงผลคำสั่งซื้อ
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $errors[] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งซื้อสินค้า - OnlineShop</title>
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
                            <span class="welcome-text me-3">
                                สวัสดี, <?= htmlspecialchars($_SESSION['fullname']) ?> (<?= $_SESSION['role'] ?>)
                            </span>
                        </li>
                        <li class="nav-item d-flex">
                            <a class="btn btn-outline-primary btn-sm border border-0" href="profile.php">
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
                <i class="fas fa-credit-card me-2"></i>ยืนยันการสั่งซื้อ
            </h1>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger glass-morphism">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- แสดงรายการสินค้าในตะกร้า -->
            <div class="glass-morphism p-4 mb-4">
                <h5 class="gradient-text mb-3">
                    <i class="fas fa-shopping-cart me-2"></i>รายการสินค้าในตะกร้า
                </h5>
                <ul class="list-group list-group-flush">
                    <?php foreach ($items as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-bottom">
                            <div>
                                <i class="fas fa-box me-2 text-muted"></i>
                                <?= htmlspecialchars($item['product_name']) ?>
                                <span class="badge bg-info ms-2"><?= $item['quantity'] ?> ชิ้น</span>
                            </div>
                            <span class="price-tag">฿<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 pt-3">
                        <strong class="gradient-text">
                            <i class="fas fa-calculator me-2"></i>รวมทั้งหมด:
                        </strong>
                        <strong class="price-tag-large">฿<?= number_format($total, 2) ?></strong>
                    </li>
                </ul>
            </div>
            <!-- ฟอร์มกรอกข้อมูลการจัดส่ง -->
            <div class="glass-morphism p-4">
                <h5 class="gradient-text mb-4">
                    <i class="fas fa-shipping-fast me-2"></i>ข้อมูลการจัดส่ง
                </h5>
                <form method="post" class="row g-3">
                    <div class="col-md-12">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>ที่อยู่
                        </label>
                        <input type="text" name="address" id="address" class="form-control" required 
                               placeholder="เช่น 123 หมู่ 4 ตำบลบางพลี">
                    </div>
                    <div class="col-md-6">
                        <label for="city" class="form-label">
                            <i class="fas fa-city me-2"></i>จังหวัด
                        </label>
                        <input type="text" name="city" id="city" class="form-control" required 
                               placeholder="เช่น สมุทรปราการ">
                    </div>
                    <div class="col-md-3">
                        <label for="postal_code" class="form-label">
                            <i class="fas fa-mail-bulk me-2"></i>รหัสไปรษณีย์
                        </label>
                        <input type="text" name="postal_code" id="postal_code" class="form-control" required 
                               placeholder="เช่น 10540">
                    </div>
                    <div class="col-md-3">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone me-2"></i>เบอร์โทรศัพท์
                        </label>
                        <input type="text" name="phone" id="phone" class="form-control" required 
                               placeholder="เช่น 0812345678">
                    </div>
                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg me-3">
                            <i class="fas fa-check-circle me-2"></i>ยืนยันการสั่งซื้อ
                        </button>
                        <a href="cart.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>กลับตะกร้า
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>