<?php

require_once '../config.php';
require_once 'auth_admin.php';

// ตรวจสอบว่าได้ส่ง ID สินค้ามาหรือไม่
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}
$product_id = $_GET['id'];

// ดึงข้อมูลสินค้า
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo "<h3>ไม่พบข้อมูลสินค้า</h3>";
    exit;
}

// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// เมื่อมีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];
    // ค่ารูปเดิม จากฟอร์ม
    $oldImage = $_POST['old_image'] ?? null;
    $removeImage = isset($_POST['remove_image']); // true/false
    if ($name && $price > 0) {
        // เตรียมตัวแปรรูปที่จะบันทึก
        $newImageName = $oldImage; // default: คงรูปเดิมไว้
        // 1) ถ ้ำมีติ๊ก "ลบรูปเดิม" → ตั้งให้เป็น null
        if ($removeImage) {
            $newImageName = null;
        }
        // 2) ถ ้ำมีอัปโหลดไฟล์ใหม่ → ตรวจแลว้เซฟไฟลแ์ ละตัง้ชอื่ ใหมท่ ับคำ่
        if (!empty($_FILES['product_image']['name'])) {
            $file = $_FILES['product_image'];
            // ตรวจชนิดไฟล์แบบง่ำย (แนะน ำ: ตรวจ MIME จริงด ้วย finfo)
            $allowed = ['image/jpeg', 'image/png'];
            if (in_array($file['type'], $allowed, true) && $file['error'] === UPLOAD_ERR_OK) {
                // สรำ้งชอื่ ไฟลใ์หม่
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $newImageName = 'product_' . time() . '.' . $ext;
                $uploadDir = realpath(__DIR__ . '/../assets/img/products_imgs');
                $destPath = $uploadDir . DIRECTORY_SEPARATOR . $newImageName;
                // ย้ำยไฟล์อัปโหลด
                if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                    // ถ ้ำย้ำยไม่ได ้ อำจตั้ง flash message แลว้คงใชรู้ปเดมิ ไว ้
                    $newImageName = $oldImage;
                }
            }
        }
        // อัปเดต DB
        $sql = "UPDATE products SET product_name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ? WHERE product_id = ?";
        $args = [$name, $description, $price, $stock, $category_id, $newImageName, $product_id];
        $stmt = $conn->prepare($sql);
        $stmt->execute($args);
        // ลบไฟล์เก่ำในดิสก์ ถ ้ำ:
        // - มีรูปเดิม ($oldImage) และ
        // - เกดิ กำรเปลยี่ นรปู (อัปโหลดใหมห่ รอื สั่งลบรปู เดมิ)
        if (!empty($oldImage) && $oldImage !== $newImageName) {
            $baseDir = realpath(__DIR__ . '/../assets/img/products_imgs');
            $filePath = realpath($baseDir . DIRECTORY_SEPARATOR . $oldImage);
            if ($filePath && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
                @unlink($filePath);
            }
        }
        header("Location: products.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า - OnlineShop Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/font/LINESeedSansTH.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>

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
                <i class="fas fa-edit me-2"></i>แก้ไขสินค้า
            </h1>
            <div class="mb-4">
                <a href="products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>กลับไปยังรายการสินค้า
                </a>
            </div>
            <div class="form-container">
                <form method="post" class="row g-4" enctype="multipart/form-data">
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fas fa-tag me-2"></i>ชื่อสินค้า
                        </label>
                        <input type="text" name="product_name" class="form-control" 
                               value="<?= htmlspecialchars($product['product_name']) ?>" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fas fa-baht-sign me-2"></i>ราคา
                        </label>
                        <input type="number" step="0.01" name="price" class="form-control" 
                               value="<?= $product['price'] ?>" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fas fa-boxes me-2"></i>จำนวนในคลัง
                        </label>
                        <input type="number" name="stock" class="form-control" 
                               value="<?= $product['stock'] ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fas fa-list me-2"></i>หมวดหมู่
                        </label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>" <?= $product['category_id'] === $cat['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">
                            <i class="fas fa-align-left me-2"></i>รายละเอียดสินค้า
                        </label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label d-block">
                            <i class="fas fa-image me-2"></i>รูปปัจจุบัน
                        </label>
                        <div class="current-image-container mb-3">
                            <?php if (!empty($product['image'])): ?>
                                <img src="../assets/img/products_imgs/<?= htmlspecialchars($product['image']) ?>"
                                     class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="no-image-placeholder">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">ไม่มีรูป</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']) ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fas fa-upload me-2"></i>อัปโหลดรูปใหม่
                        </label>
                        <input type="file" name="product_image" class="form-control" accept="image/jpeg,image/png">
                        <div class="form-text">รองรับไฟล์ JPG และ PNG เท่านั้น</div>
                        
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                            <label class="form-check-label" for="remove_image">
                                <i class="fas fa-trash me-2"></i>ลบรูปเดิม
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
                            </button>
                            <a href="products.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>ยกเลิก
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
        .no-image-placeholder {
            width: 150px;
            height: 150px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .current-image-container img {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn {
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
        }
    </style>
</body>