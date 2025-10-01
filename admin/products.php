<?php

require_once '../config.php';
require_once 'auth_admin.php';

// เพิ่มสินค้าใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']); // floatval() ใช้แปลงเป็น float
    $stock = intval($_POST['stock']); // intval() ใช้แปลงเป็น integer
    $category_id = intval($_POST['category_id']);
    // ค่าที่ได้จากฟอร์มเป็น string เสมอ
    if ($name && $price > 0) { // ตรวจสอบชื่อ และราคาสินค้า
        $imageName = null;

        if (!empty($_FILES['product_image']['name'])) {
            $file = $_FILES['product_image'];
            $allowed = ['image/jpeg', 'image/png'];

            if (in_array($file['type'], $allowed)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $imageName = 'product_' . time() . '.' . $ext;
                $path = __DIR__ . '/../assets/img/products_imgs/' . $imageName;
                move_uploaded_file($file['tmp_name'], $path);
            }
        }
        $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $category_id, $imageName]);
        header("Location: products.php");
        exit;
    }
}


// ลบสนิ คำ้ (ลบไฟลร์ปู ดว้ย)
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete']; // แคสต์เป็น int
    // 1) ดงึชอื่ ไฟลร์ปู จำก DB ก่อน
    $stmt = $conn->prepare("SELECT image FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $imageName = $stmt->fetchColumn(); // null ถ ้ำไม่มีรูป
    // 2) ลบใน DB ด ้วย Transaction
    try {
        $conn->beginTransaction();
        $del = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $del->execute([$product_id]);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        // ใส่ flash message หรือ log ได ้ตำมต ้องกำร
        header("Location: products.php");
        exit;
    }
    // 3) ลบไฟล์รูปหลัง DB ลบส ำเร็จ
    if ($imageName) {
        $baseDir = realpath(__DIR__ . '/../assets/img/products_imgs'); // โฟลเดอร์เก็บรูป
        $filePath = realpath($baseDir . '/' . $imageName);
        // กัน path traversal: ต ้องอยู่ใต้ $baseDir จริง ๆ
        if ($filePath && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
            @unlink($filePath); // ใช ้@ กัน warning ถำ้ลบไมส่ ำเร็จ
        }
    }
    header("Location: products.php");
    exit;
}

// ลบสินค้า
// if (isset($_GET['delete'])) {
//     $product_id = $_GET['delete'];
//     $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
//     $stmt->execute([$product_id]);
//     header("Location: products.php");
//     exit;
// }

// ดึงรายการสินค้า
$stmt = $conn->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงหมวดหมู่
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า - OnlineShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/font/LINESeedSansTH.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-select {
            height: calc(2.375rem + 10px);
            padding: 0.375rem 0.75rem;
        }
    </style>
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
                <i class="fas fa-box me-2"></i>จัดการสินค้า
            </h1>

            <div class="row mb-4">
                <div class="col-12">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>กลับหน้าผู้ดูแล
                    </a>
                </div>
            </div>

            <!-- ฟอร์มเพิ่มสินค้าใหม่ -->
            <div class="admin-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-plus-circle me-2"></i>เพิ่มสินค้าใหม่
                </h5>
                <form method="post" class="row g-3" enctype="multipart/form-data">
                    <div class="col-md-4">
                        <label class="form-label">ชื่อสินค้า</label>
                        <input type="text" name="product_name" class="form-control" placeholder="ชื่อสินค้า" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ราคา (บาท)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="ราคา" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">จำนวนสต๊อก</label>
                        <input type="number" name="stock" class="form-control" placeholder="จำนวน" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">หมวดหมู่</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">เลือกหมวดหมู่</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="add_product" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>เพิ่ม
                        </button>
                    </div>
                    <div class="col-12">
                        <label class="form-label">รายละเอียดสินค้า</label>
                        <textarea name="description" class="form-control" placeholder="รายละเอียดสินค้า" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">รูปสินค้า (jpg, png)</label>
                        <input type="file" name="product_image" class="form-control" rows="3"></input>
                    </div>
                </form>
            </div>

            <!-- รายการสินค้า -->
            <div class="table-container">
                <h5 class="p-3 mb-0 border-bottom">
                    <i class="fas fa-list me-2"></i>รายการสินค้า
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">
                                    <i class="fas fa-box me-2"></i>ชื่อสินค้า
                                </th>
                                <th scope="col">
                                    <i class="fas fa-tags me-2"></i>หมวดหมู่
                                </th>
                                <th scope="col">
                                    <i class="fas fa-dollar-sign me-2"></i>ราคา
                                </th>
                                <th scope="col">
                                    <i class="fas fa-warehouse me-2"></i>คงเหลือ
                                </th>
                                <th scope="col">
                                    <i class="fas fa-image me-2"></i>ภาพสินค้า
                                </th>
                                <th scope="col" class="text-center">
                                    <i class="fas fa-cogs me-2"></i>จัดการ
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td class="align-middle">
                                        <span class="fw-semibold"><?= htmlspecialchars($p['product_name']) ?></span>
                                        <?php if (!empty($p['description'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($p['description']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-primary"><?= htmlspecialchars($p['category_name']) ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="fw-bold text-success"><?= number_format($p['price'], 2) ?> บาท</span>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($p['stock'] > 10): ?>
                                            <span class="badge bg-success"><?= $p['stock'] ?></span>
                                        <?php elseif ($p['stock'] > 0): ?>
                                            <span class="badge bg-warning"><?= $p['stock'] ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">หมด</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($p['image']): ?>
                                            <img src="../assets/img/products_imgs/<?= htmlspecialchars($p['image']) ?>" width="60" height="60"
                                                class="rounded object-fit-cover">
                                        <?php else: ?>
                                            <img src="../assets/img/no_images.png" width="60" height="60">
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="edit_products.php?id=<?= $p['product_id'] ?>"
                                                class="btn btn-warning btn-sm d-flex justify-content-center align-items-center">
                                                <i class="fas fa-edit me-1"></i>แก้ไข
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center delete-product"
                                                    data-id="<?= $p['product_id'] ?>" 
                                                    data-name="<?= htmlspecialchars($p['product_name']) ?>">
                                                <i class="fas fa-trash me-1"></i>ลบ
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // SweetAlert for Delete Product
        document.querySelectorAll('.delete-product').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-name');
                
                Swal.fire({
                    title: 'ยืนยันการลบสินค้า?',
                    html: `คุณต้องการลบสินค้า <strong>${productName}</strong> หรือไม่?<br><small class="text-danger">การลบจะไม่สามารถกู้คืนได้!</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'กำลังลบสินค้า...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        window.location.href = `products.php?delete=${productId}`;
                    }
                });
            });
        });
    </script>
</body>

</html>