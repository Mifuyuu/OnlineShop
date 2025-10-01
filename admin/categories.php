<?php
require_once '../config.php';
require_once 'auth_admin.php';

// เพิ่มหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if ($category_name) {
        $stmt = $conn->prepare("INSERT INTO categories(category_name) VALUES (?)");
        $stmt->execute([$category_name]);
        header("Location: categories.php");
        exit;
    }
}
// ลบหมวดหมู่ (แบบไมม่ กี ำรตรวจสอบวำ่ ยังมสี นิ คำ้ในหมวดหมนู่ หี้ รอื ไม)่
// if (isset($_GET['delete'])) {
// $category_id = $_GET['delete'];
// $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
// $stmt->execute([$category_id]);
// header("Location: categories.php");
// exit;
// }

// ลบหมวดหมู่
// ตรวจสอบวำ่ หมวดหมนู่ ี้ยังถกู ใชอ้ยหู่ รอื ไม่
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    // ตรวจสอบวำ่ หมวดหมนู่ ยี้ ังถูกใชอ้ยหู่ รอื ไม่
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $productCount = $stmt->fetchColumn();
    if ($productCount > 0) {
        // ถำ้มสี นิ คำ้อยใู่ นหมวดหมนู่ ี้
        $_SESSION['error'] = "ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีส้นค้าที่อยู่ในหมวดหมู่นี้อยู่";
    } else {
        // ถำ้ไมม่ สี นิ คำ้ ใหล้ บได ้
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $_SESSION['success'] = "ลบหมวดหมู่เรียบร้อยแล้ว";
    }
    header("Location: categories.php");
    exit;
}

// แก ้ไขหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = trim($_POST['new_name']);
    if ($category_name) {
        $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
        $stmt->execute([$category_name, $category_id]);
        header("Location: categories.php");
        exit;
    }
}
// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);

// โคด้ นเี้ขยีนตอ่ กันยำวบรรทัดเดยี วไดเ้พรำะ ผลลัพธจ์ ำกเมธอดหนงึ่ สำมำรถสง่ ตอ่ (chaining) ให้เมธอดถัดไปทันที โดยไม่ต ้องแยกตัวแปรเก็บไว้ก่อน
// $pdo->query("...")->fetchAll(...);
// หำกเขียนแยกเป็นหลำยบรรทัดจะเป็นแบบนี้:
// $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_id ASC");
// $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ควรเขยีนแยกบรรทัดเมอื่ จะ ใช ้$stmt ซ ้ำหลำยครัง้ (เชน่ fetch ทีละ row, ตรวจจ ำนวนแถว)
// หรือเขียนแบบ prepare , execute
// $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY category_id ASC");
// $stmt->execute();
// $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมวดหมู่สินค้า - OnlineShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/font/LINESeedSansTH.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <i class="fas fa-tags me-2"></i>จัดการหมวดหมู่สินค้า
            </h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <div class="row mb-4">
                <div class="col-12">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>กลับหน้าผู้ดูแล
                    </a>
                </div>
            </div>

            <!-- ฟอร์มเพิ่มหมวดหมู่ -->
            <div class="admin-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-plus-circle me-2"></i>เพิ่มหมวดหมู่ใหม่
                </h5>
                <form method="post" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="category_name" class="form-control" placeholder="ชื่อหมวดหมู่ใหม่" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" name="add_category" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>เพิ่มหมวดหมู่
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- รายการหมวดหมู่ -->
            <div class="table-container">
                <h5 class="p-3 mb-0 border-bottom">
                    <i class="fas fa-list me-2"></i>รายการหมวดหมู่
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">
                                    <i class="fas fa-tag me-2"></i>ชื่อหมวดหมู่
                                </th>
                                <th scope="col">
                                    <i class="fas fa-edit me-2"></i>แก้ไขชื่อ
                                </th>
                                <th scope="col" class="text-center">
                                    <i class="fas fa-cogs me-2"></i>จัดการ
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="align-middle">
                                        <span class="fw-semibold"><?= htmlspecialchars($cat['category_name']) ?></span>
                                    </td>
                                    <td>
                                        <form method="post" class="d-flex gap-2">
                                            <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                                            <input type="text" name="new_name" class="form-control form-control-sm" 
                                                   placeholder="ชื่อใหม่" required>
                                            <button type="submit" name="update_category" class="btn btn-warning btn-sm">
                                                <i class="fas fa-save me-1"></i>แก้ไข
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm delete-category" 
                                                data-id="<?= $cat['category_id'] ?>" 
                                                data-name="<?= htmlspecialchars($cat['category_name']) ?>">
                                            <i class="fas fa-trash me-1"></i>ลบ
                                        </button>
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
        // SweetAlert for Delete Category
        document.querySelectorAll('.delete-category').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-id');
                const categoryName = this.getAttribute('data-name');
                
                Swal.fire({
                    title: 'ยืนยันการลบหมวดหมู่?',
                    html: `คุณต้องการลบหมวดหมู่ <strong>${categoryName}</strong> หรือไม่?<br><small class="text-muted">หากมีสินค้าในหมวดหมู่นี้จะไม่สามารถลบได้</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `categories.php?delete=${categoryId}`;
                    }
                });
            });
        });
        
        // SweetAlert for Add/Update Success
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?= $_SESSION['success'] ?>',
                showConfirmButton: false,
                timer: 2000
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '<?= $_SESSION['error'] ?>',
                confirmButtonColor: '#667eea'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>

</html>