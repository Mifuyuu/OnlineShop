<?php
session_start();
require 'config.php';
require 'function.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือยัง
if (!isset($_SESSION['user_id'])) { // ใส่ session ของ user_id
    header("Location: login.php"); // หน้ำ login
    exit;
}
// เก็บ user_id
$user_id = $_SESSION['user_id']; // ตัวแปรเก็บ user_id
// -----------------------------
// ดึงคำสั่งซื้อของผู้ใช้
// -----------------------------
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC"); // orders table และ order_date
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ - OnlineShop</title>
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
                <i class="fas fa-history me-2"></i>ประวัติการสั่งซื้อ
            </h1>
            <a href="index.php" class="btn btn-secondary mb-4">
                <i class="fas fa-arrow-left me-2"></i>กลับหน้าหลัก
            </a>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success glass-morphism">
                    <i class="fas fa-check-circle me-2"></i>ทำรายการสั่งซื้อเรียบร้อยแล้ว
                </div>
            <?php endif; ?>
            
            <?php if (count($orders) === 0): ?>
                <div class="alert alert-warning glass-morphism text-center">
                    <i class="fas fa-shopping-bag fa-3x mb-3 text-muted"></i>
                    <h5>คุณยังไม่เคยสั่งซื้อสินค้า</h5>
                    <p class="mb-0">เริ่มต้นช้อปปิ้งกับเราได้เลย!</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                    // กำหนดสีสถานะ
                    $statusColor = 'secondary';
                    switch($order['status']) {
                        case 'pending': $statusColor = 'warning'; break;
                        case 'processing': $statusColor = 'info'; break;
                        case 'shipped': $statusColor = 'primary'; break;
                        case 'completed': $statusColor = 'success'; break;
                        case 'cancelled': $statusColor = 'danger'; break;
                    }
                    ?>
                    <div class="glass-morphism mb-4 hover-lift">
                        <div class="card-header" style="background: rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <strong class="gradient-text">
                                        <i class="fas fa-receipt me-2"></i>รหัสคำสั่งซื้อ: #<?= $order['order_id'] ?>
                                    </strong>
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-calendar me-2"></i><?= $order['order_date'] ?>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-<?= $statusColor ?> px-3 py-2">
                                        <i class="fas fa-info-circle me-1"></i><?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="gradient-text mb-3">
                                <i class="fas fa-shopping-cart me-2"></i>รายการสินค้า
                            </h6>
                            <ul class="list-group list-group-flush mb-3">
                                <?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                        <div>
                                            <i class="fas fa-box me-2 text-muted"></i>
                                            <?= htmlspecialchars($item['product_name']) ?>
                                            <span class="badge bg-info ms-2"><?= $item['quantity'] ?> ชิ้น</span>
                                        </div>
                                        <span class="price-tag">฿<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="text-end mb-3">
                                <strong class="price-tag-large">
                                    <i class="fas fa-calculator me-2"></i>รวมทั้งหมด: ฿<?= number_format($order['total_amount'], 2) ?>
                                </strong>
                            </div>
                            
                            <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
                            <?php if ($shipping): ?>
                                <?php
                                $shippingStatusColor = 'secondary';
                                switch($shipping['shipping_status']) {
                                    case 'not_shipped': $shippingStatusColor = 'warning'; break;
                                    case 'shipped': $shippingStatusColor = 'primary'; break;
                                    case 'delivered': $shippingStatusColor = 'success'; break;
                                }
                                ?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="gradient-text">
                                            <i class="fas fa-shipping-fast me-2"></i>ข้อมูลการจัดส่ง
                                        </h6>
                                        <p class="mb-1">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= $shipping['postal_code'] ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-phone me-2"></i><?= htmlspecialchars($shipping['phone']) ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-<?= $shippingStatusColor ?> px-3 py-2">
                                            <i class="fas fa-truck me-1"></i><?= ucfirst($shipping['shipping_status']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>