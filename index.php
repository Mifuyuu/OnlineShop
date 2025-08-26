<?php
session_start();
require_once 'config.php';

// ดึงข้อมูลสินค้าทั้งหมดพร้อมชื่อหมวดหมู่
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.stock > 0 
        ORDER BY p.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลหมวดหมู่สำหรับ filter
$sql_categories = "SELECT * FROM categories ORDER BY category_name";
$stmt_categories = $conn->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
        body {
            background-image: url('assets/img/background.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            font-family: 'LINE Seed Sans TH', 'Segoe UI', Verdana, sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .navbar-brand {
            font-weight: 700;
            color: #2c3e50 !important;
            font-family: 'LINE Seed Sans TH', sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .btn-primary {
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-primary {
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: 2px solid #667eea;
            color: #667eea;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .btn-outline-primary.active {
            background: linear-gradient(135deg, #5c4fd6ff 0%, rgba(137, 89, 182, 1) 100%);
            border-color: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(68, 84, 173, 0.4);
        }

        .category-filter {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .price-tag {
            font-size: 1.2rem;
            font-weight: 600;
            color: #e74c3c;
        }

        .stock-badge {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
        }

        .page-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            font-family: 'LINE Seed Sans TH', sans-serif;
        }

        .welcome-text {
            color: #495057;
            font-weight: 500;
            font-family: 'LINE Seed Sans TH', sans-serif;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 15px !important;
            }
            
            .product-image {
                height: 150px;
                font-size: 2rem;
            }
        }
    </style>
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
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item d-flex">
                            <span class="navbar-text welcome-text me-3">
                                สวัสดี, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)
                            </span>
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
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-category="<?= $product['category_id'] ?>">
                            <div class="card product-card">
                                <div class="product-image">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h6>
                                    <p class="card-text text-muted small">
                                        <?= htmlspecialchars($product['description']) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="price-tag">฿<?= number_format($product['price'], 2) ?></span>
                                        <span class="stock-badge">
                                            <i class="fas fa-cubes me-1"></i><?= $product['stock'] ?> ชิ้น
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i>
                                            <?= htmlspecialchars($product['category_name']) ?>
                                        </small>
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <button class="btn btn-primary btn-sm">
                                                <i class="fas fa-cart-plus me-1"></i>เพิ่ม
                                            </button>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-sign-in-alt me-1"></i>เข้าสู่ระบบ
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
