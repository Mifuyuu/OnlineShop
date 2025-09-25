<?php

require_once 'config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = $_GET['id'];
$stmt = $conn->prepare("SELECT p.*, c.category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo "<h3>ไม่พบสินค้าที่คุณต้องการ</h3>";
    exit;
}

// เตรียมรูปภาพ
$img = !empty($product['image'])
    ? 'assets/img/products_imgs/' . rawurlencode($product['image'])
    : 'assets/img/products_imgs/no_images.png';

// ตกแต่ง badge: NEW ภายใน 7 วัน / HOT ถ้าสต็อกน้อยกว่า 5
$isNew = isset($product['created_at']) && (time() - strtotime($product['created_at']) <= 7 * 24 * 3600);
$isHot = (int)$product['stock'] > 0 && (int)$product['stock'] < 5;
// ดาวรีวิว (ถ้าไม่มีใน DB จะโชว์ 4.5 จำลอง; ถ้ามี $product['rating'] ให้แทน)
$rating = isset($product['rating']) ? (float)$product['rating'] : 4.5;
$full = floor($rating); // จำนวนดาวเต็ม (เต็ม 1 ดวง) , floor ปัดลง
$half = ($rating - $full) >= 0.5 ? 1 : 0; // มีดาวครึ่งดวงหรือไม่
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสินค้า - OnlineShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/font/LINESeedSansTH.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        /* Product detail specific styles */
        .product-image-large {
            height: 300px;
        }
        
        .product-has-image {
            height: 100%;
            /* max-height: 400px; */
        }
        
        .price-tag {
            font-size: 1.5rem;
        }
        
        .stock-badge {
            padding: 8px 16px;
            font-size: 1rem;
        }
        .quantity-section {
            background: rgba(108, 117, 125, 0.1);
            padding: 20px;
            border-radius: 12px;
            border: 2px dashed #6c757d;
        }
    </style>
</head>

<body>
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
                                สวัสดี, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)
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
            <div class="mb-4">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>กลับหน้ารายการสินค้า
                </a>
            </div>

            <div class="product-detail-card p-4">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <!-- Product Image with Badges -->
                        <div class="position-relative">
                            <?php if (!empty($product['image'])): ?>
                                <div class="product-has-image overflow-hidden">
                                    <img src="<?= htmlspecialchars($img) ?>" class="w-100 h-100 object-fit-cover rounded" alt="<?= htmlspecialchars($product['product_name']) ?>">
                                </div>
                            <?php else: ?>
                                <div class="product-image-large d-flex align-items-center justify-content-center bg-light rounded">
                                    <i class="fas fa-box fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badges -->
                            <div class="position-absolute top-0 start-0 p-3">
                                <?php if ($isNew): ?>
                                    <span class="badge bg-success me-2 mb-1" style="font-size: 0.9rem;">
                                        <i class="fas fa-star me-1"></i>NEW
                                    </span>
                                <?php endif; ?>
                                <?php if ($isHot): ?>
                                    <span class="badge bg-danger" style="font-size: 0.9rem;">
                                        <i class="fas fa-fire me-1"></i>HOT
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h1 class="page-title mb-3">
                            <i class="fas fa-tag me-2"></i><?= htmlspecialchars($product['product_name'])?>
                        </h1>
                        
                        <div class="mb-3">
                            <span class="category-badge">
                                <i class="fas fa-folder me-1"></i><?= htmlspecialchars($product['category_name'])?>
                            </span>
                        </div>

                        <!-- Star Rating -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $full): ?>
                                            <i class="fas fa-star text-warning fs-5"></i>
                                        <?php elseif ($i == $full + 1 && $half): ?>
                                            <i class="fas fa-star-half-alt text-warning fs-5"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-muted fs-5"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-muted h6 mb-0">(<?= number_format($rating, 1) ?> ดาว)</span>
                            </div>
                        </div>

                        <div class="mb-4 quantity-section">
                            <p class="text-muted"><?= nl2br(htmlspecialchars($product['description']))?></p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <!-- <i class="fas fa-baht-sign me-2 text-danger"></i> -->
                                    <span class="price-tag-large">฿<?= number_format($product['price'], 2)?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <span class="stock-badge-large">
                                        <i class="fas fa-cubes me-1"></i><?= $product['stock']?> ชิ้น
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form action="cart.php" method="post" class="mt-4">
                                <input type="hidden" name="product_id" value="<?= $product['product_id']?>">
                                <div class="row align-items-end">
                                    <div class="col-md-4 mb-3">
                                        <label for="quantity" class="form-label fw-bold">
                                            <i class="fas fa-sort-numeric-up me-1"></i>จำนวน:
                                        </label>
                                        <input type="number" name="quantity" id="quantity" class="form-control" 
                                               value="1" min="1" max="<?= $product['stock']?>" required>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <button type="submit" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-cart-plus me-2"></i>เพิ่มในตะกร้า
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i>กรุณาเข้าสู่ระบบเพื่อซื้อสินค้า
                                <div class="mt-3">
                                    <a href="login.php" class="btn btn-primary me-2">
                                        <i class="fas fa-sign-in-alt me-1"></i>เข้าสู่ระบบ
                                    </a>
                                    <a href="register.php" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus me-1"></i>สมัครสมาชิก
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>