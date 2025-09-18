<?php
session_start();

require_once 'config.php'; //เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลสินค้าทั้งหมดพร้อมชื่อหมวดหมู่
// $sql = "SELECT p.*, c.category_name 
//         FROM products p 
//         LEFT JOIN categories c ON p.category_id = c.category_id 
//         WHERE p.stock > 0 
//         ORDER BY p.created_at DESC";
// $stmt = $conn->prepare($sql);
// $stmt->execute();
// $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("SELECT p.*, c.category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลหมวดหมู่สำหรับ filter
// $sql_categories = "SELECT * FROM categories ORDER BY category_name";
// $stmt_categories = $conn->prepare($sql_categories);
// $stmt_categories->execute();
// $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

$sql_categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
$categories = $sql_categories->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnlineShop - ร้านค้าออนไลน์</title>
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
                            <span class=" welcome-text me-3 ">
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
            <h1 class="page-title">
                <i class="fas fa-shopping-bag me-2"></i>ร้านค้าออนไลน์
            </h1>

            <!-- Category Filter -->
            <div class="category-filter">
                <h5 class="mb-3"><i class="fas fa-filter me-2"></i>กรองตามหมวดหมู่</h5>
                <div class="row">
                    <div class="col-auto mb-2">
                        <button class="btn btn-outline-primary btn-sm active" onclick="filterProducts('all')">
                            ทั้งหมด
                        </button>
                    </div>
                    <?php foreach ($categories as $category): ?>
                        <div class="col-auto mb-2">
                            <button class="btn btn-outline-primary btn-sm"
                                onclick="filterProducts('<?= $category['category_id'] ?>')">
                                <?= htmlspecialchars($category['category_name']) ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row" id="products-container">
                <?php if (empty($products)): ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>ขณะนี้ยังไม่มีสินค้าในร้าน
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>

                        <!-- TODO==== เตรียมรูป / ตกแต่ง badge / ดำวรีวิว ==== -->
                        <?php
                        // เตรียมรูป
                        $img = !empty($product['image'])
                            ? 'assets/img/products_imgs/' . rawurlencode($product['image'])
                            : 'assets/img/products_imgs/no_images.png';
                        // ตกแต่ง badge: NEW ภำยใน 7 วัน / HOT ถ ้ำสต็อกน้อยกว่ำ 5
                        $isNew = isset($product['created_at']) && (time() - strtotime($product['created_at']) <= 7 * 24 * 3600);
                        $isHot = (int)$product['stock'] > 0 && (int)$product['stock'] < 5;
                        // ดำวรีวิว (ถ ้ำไม่มีใน DB จะโชว์ 4.5 จ ำลอง; ถ ้ำมี $p['rating'] ให้แทน)
                        $rating = isset($product['rating']) ? (float)$product['rating'] : 4.5;
                        $full = floor($rating); // จ ำนวนดำวเต็ม (เต็ม 1 ดวง) , floor ปัดลง
                        $half = ($rating - $full) >= 0.5 ? 1 : 0; // มีดำวครึ่งดวงหรือไม่
                        ?>

                        <div class="col-lg-4 col-md-6 col-sm-6 mb-4" data-category="<?= $product['category_id'] ?>">
                            <div class="card product-card">
                                <!-- Product Image with Badges -->
                                <div class="position-relative">
                                    <?php if (!empty($product['image'])): ?>
                                        <div class="product-has-image overflow-hidden">
                                            <img src="<?= htmlspecialchars($img) ?>" class=" w-100">
                                        </div>
                                    <?php else: ?>
                                        <div class="product-image">
                                            <i class="fas fa-box"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Badges -->
                                    <div class="position-absolute top-0 start-0 p-2">
                                        <?php if ($isNew): ?>
                                            <span class="badge bg-success me-1">
                                                <i class="fas fa-star me-1"></i>NEW
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($isHot): ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-fire me-1"></i>HOT
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h6>
                                    <p class="card-text text-muted small">
                                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="price-tag">฿<?= number_format($product['price'], 2) ?></span>
                                        <span class="stock-badge">
                                            <i class="fas fa-cubes me-1"></i><?= $product['stock'] ?> ชิ้น
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i>
                                            <?= htmlspecialchars($product['category_name']) ?>
                                        </small>
                                    </div>

                                    <!-- Star Rating -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?php if ($i <= $full): ?>
                                                        <i class="fas fa-star text-warning"></i>
                                                    <?php elseif ($i == $full + 1 && $half): ?>
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star text-muted"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                            <small class="text-muted">(<?= number_format($rating, 1) ?>)</small>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-grid gap-2">
                                        <a href="product_detail.php?id=<?= $product['product_id'] ?>"
                                            class="btn btn-outline-primary">
                                            <i class="fas fa-eye me-2"></i>ดูรายละเอียด
                                        </a>

                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <form action="cart.php" method="post">
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-cart-plus me-2"></i>เพิ่มในตะกร้า
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-success">
                                                <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบเพื่อซื้อ
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function filterProducts(categoryId) {
            const products = document.querySelectorAll('[data-category]');
            const buttons = document.querySelectorAll('.category-filter .btn-outline-primary');

            // Reset button states
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Filter products
            products.forEach(product => {
                if (categoryId === 'all' || product.dataset.category === categoryId) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>