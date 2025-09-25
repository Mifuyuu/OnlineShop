<?php
session_start();
require 'config.php';
// ตรวจสอบกำรล็อกอิน
if (!isset($_SESSION['user_id'])) { // TO DO: ใส่ session ของ user
    header("Location: login.php"); // TO DO: หน้ำ login
    exit;
}
$user_id = $_SESSION['user_id']; // TO DO: กำหนด user_id

// -----------------------------
// ดึงรายการสินค้าในตะกร้า
// -----------------------------
$stmt = $conn->prepare("SELECT cart.cart_id, cart.quantity, products.product_name, products.price
FROM cart
JOIN products ON cart.product_id = products.product_id
WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -----------------------------
// เพิ่มสินค้าเข้าตะกร้า
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) { // TO DO: product_id
    $product_id = $_POST['product_id']; // TO DO: product_id
    $quantity = max(1, intval($_POST['quantity'] ?? 1));
    // ตรวจสอบว่าสินค้าอยู่ในตะกร้าแล้วหรือยัง
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    // TO DO: ใส่ชื่อตารางตะกร้า
    $stmt->execute([$user_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($item) {
        // ถ้ามีแล้ว ให้เพิ่มจำนวน
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE cart_id = ?");
        // TO DO: ชื่อ ตำราง, primary key ของตะกร้า
        $stmt->execute([$quantity, $item['cart_id']]);
    } else {
        // ถ ้ำยังไม่มี ให้เพิ่มใหม่
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
    header("Location: cart.php"); // TO DO: กลับมำที่ cart
    exit;
}

// -----------------------------
// ลบสนิ คำ้ออกจำกตะกรำ้
// -----------------------------
if (isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    // TODO: ชอื่ ตำรำงตะกรำ้, primary key
    $stmt->execute([$cart_id, $user_id]);
    header("Location: cart.php"); // TODO: กลับมำที่ cart
    exit;
}

// -----------------------------
// คำนวณราคารวม
// -----------------------------
$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price']; // TODO: quantity * price
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า - OnlineShop</title>
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
                            <a class="btn btn-outline-primary btn-sm border border-0 active" href="cart.php">
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
                <i class="fas fa-shopping-cart me-2"></i>ตะกร้าสินค้า
            </h1>
            <a href="index.php" class="btn btn-secondary mb-4">
                <i class="fas fa-arrow-left me-2"></i>กลับไปเลือกสินค้า
            </a>
            <?php if (count($items) === 0): ?>
                <div class="alert alert-warning glass-morphism">
                    <i class="fas fa-shopping-cart me-2"></i>ยังไม่มีสินค้าในตะกร้า
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ชื่อสินค้า</th>
                                <th>จำนวน</th>
                                <th>ราคาต่อหน่วย</th>
                                <th>ราคารวม</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-box me-2 text-muted"></i>
                                        <?= htmlspecialchars($item['product_name']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $item['quantity'] ?> ชิ้น
                                        </span>
                                    </td>
                                    <td class="price-tag">฿<?= number_format($item['price'], 2) ?></td>
                                    <td class="price-tag">฿<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                    <td>
                                        <a href="cart.php?remove=<?= $item['cart_id'] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('คุณต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?')">
                                            <i class="fas fa-trash me-1"></i>ลบ
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="3" class="text-end fw-bold">
                                    <i class="fas fa-calculator me-2"></i>รวมทั้งหมด:
                                </td>
                                <td colspan="2" class="fw-bold price-tag-large">
                                    ฿<?= number_format($total, 2) ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="text-center mt-4">
                    <a href="checkout.php" class="btn btn-success btn-lg">
                        <i class="fas fa-credit-card me-2"></i>สั่งซื้อสินค้า
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>