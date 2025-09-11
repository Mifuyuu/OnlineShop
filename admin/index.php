<?php
require_once '../config.php';
require_once 'auth_admin.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แผงควบคุมผู้ดูแลระบบ - OnlineShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/font/LINESeedSansTH.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <style>
        /* Admin panel specific styles */
        .admin-card {
            padding: 30px;
            text-align: center;
        }
        
        .welcome-text {
            text-align: center;
            margin-bottom: 40px;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-store me-2"></i>OnlineShop
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-danger fw-bold">
                    <i class="fas fa-crown me-2"></i>Admin Panel
                </span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="main-container p-4 p-md-5">
            <h1 class="page-title">
                <i class="fas fa-cogs me-2"></i>ระบบผู้ดูแลระบบ
            </h1>
            
            <p class="welcome-text">
                <i class="fas fa-user-shield me-2"></i>ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?>
            </p>

            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="admin-card">
                        <div class="admin-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="mb-3">จัดการสมาชิก</h5>
                        <p class="text-muted mb-3">ดูข้อมูลและจัดการสมาชิกของระบบ</p>
                        <a href="users.php" class="btn btn-warning w-100">
                            <i class="fas fa-user-cog me-2"></i>จัดการสมาชิก
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="admin-card">
                        <div class="admin-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h5 class="mb-3">จัดการหมวดหมู่</h5>
                        <p class="text-muted mb-3">เพิ่ม แก้ไข และลบหมวดหมู่สินค้า</p>
                        <a href="categories.php" class="btn btn-dark w-100">
                            <i class="fas fa-tags me-2"></i>จัดการหมวดหมู่
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="admin-card">
                        <div class="admin-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <h5 class="mb-3">จัดการสินค้า</h5>
                        <p class="text-muted mb-3">เพิ่ม แก้ไข และลบสินค้าในระบบ</p>
                        <a href="products.php" class="btn btn-primary w-100">
                            <i class="fas fa-cube me-2"></i>จัดการสินค้า
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="admin-card">
                        <div class="admin-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h5 class="mb-3">จัดการคำสั่งซื้อ</h5>
                        <p class="text-muted mb-3">ติดตามและจัดการคำสั่งซื้อ</p>
                        <a href="orders.php" class="btn btn-success w-100">
                            <i class="fas fa-receipt me-2"></i>จัดการคำสั่งซื้อ
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="../logout.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                </a>
            </div>
        </div>
    </div>
</body>

</html>