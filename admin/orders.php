<?php
require '../config.php';
require_once 'auth_admin.php'; // ตรวจสอบสิทธิ์ admin

// ดึงคำสั่งซื้อทั้งหมด
$stmt = $conn->query("
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ฟังก์ชันดึงรายการสินค้าในคำสั่งซื้อ
// function getOrderItems($conn, $order_id) {
    //     $stmt = $conn->prepare("
    //         SELECT oi.quantity, oi.price, p.product_name
//         FROM order_items oi
//         JOIN products p ON oi.product_id = p.product_id
//         WHERE oi.order_id = ?
//     ");
//     $stmt->execute([$order_id]);
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }

require '../function.php';   // ดึงฟังก์ชันที่เก็บไว้

// ฟังก์ชันดึงข้อมูลการจัดส่ง
// function getShippingInfo($conn, $order_id) {
//     $stmt = $conn->prepare("SELECT * FROM shipping WHERE order_id = ?");
//     $stmt->execute([$order_id]);
//     return $stmt->fetch(PDO::FETCH_ASSOC);
// }

// อัปเดตสถานะคำสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
        header("Location: orders.php");
        exit;
    }
    if (isset($_POST['update_shipping'])) {
        $stmt = $conn->prepare("UPDATE shipping SET shipping_status = ? WHERE shipping_id = ?");
        $stmt->execute([$_POST['shipping_status'], $_POST['shipping_id']]);
        header("Location: orders.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคำสั่งซื้อ - OnlineShop Admin</title>
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
                <span class="badge bg-warning text-dark ms-2">Admin</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-2">
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="index.php">
                            <i class="fas fa-tachometer-alt me-1"></i>แดชบอร์ด
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>ออกจากระบบ
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="main-container p-4 p-md-5">
            <h1 class="page-title admin-title">
                <i class="fas fa-clipboard-list me-2"></i>จัดการคำสั่งซื้อทั้งหมด
            </h1>
            <a href="index.php" class="btn btn-secondary mb-4">
                <i class="fas fa-arrow-left me-2"></i>กลับหน้าผู้ดูแล
            </a>

            <div class="accordion accordion-flush" id="ordersAccordion">
                <?php foreach ($orders as $index => $order): ?>
                    <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
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

                    <div class="accordion-item glass-morphism mb-3">
                        <h2 class="accordion-header" id="heading<?= $index ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>" style="background: rgba(255, 255, 255, 0.8); border: none;">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <div>
                                        <i class="fas fa-receipt me-2"></i>
                                        <strong>คำสั่งซื้อ #<?= $order['order_id'] ?></strong>
                                        <span class="text-muted mx-2">|</span>
                                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($order['username']) ?>
                                        <span class="text-muted mx-2">|</span>
                                        <i class="fas fa-calendar me-1"></i><?= $order['order_date'] ?>
                                    </div>
                                    <span class="badge bg-<?= $statusColor ?> px-3 py-2">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#ordersAccordion">
                            <div class="accordion-body" style="background: rgba(255, 255, 255, 0.9);"
                                <!-- รายการสินค้า -->
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
                                            <span class="price-tag">฿<?= number_format($item['quantity'] * $item['price'], 2) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="text-end mb-3">
                                    <strong class="price-tag-large">
                                        <i class="fas fa-calculator me-2"></i>ยอดรวม: ฿<?= number_format($order['total_amount'], 2) ?>
                                    </strong>
                                </div>

                                <!-- อัปเดตสถานะคำสั่งซื้อ -->
                                <div class="glass-morphism p-3 mb-3">
                                    <h6 class="gradient-text mb-3">
                                        <i class="fas fa-edit me-2"></i>อัปเดตสถานะคำสั่งซื้อ
                                    </h6>
                                    <form method="post" class="row g-2" id="orderForm<?= $index ?>">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <div class="col-md-8">
                                            <select name="status" class="form-select">
                                                <?php
                                                $statuses = [
                                                    'pending' => 'รอดำเนินการ',
                                                    'processing' => 'กำลังประมวลผล',
                                                    'shipped' => 'จัดส่งแล้ว',
                                                    'completed' => 'เสร็จสิ้น',
                                                    'cancelled' => 'ยกเลิก'
                                                ];
                                                foreach ($statuses as $status => $statusText) {
                                                    $selected = ($order['status'] === $status) ? 'selected' : '';
                                                    echo "<option value=\"$status\" $selected>$statusText</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-primary w-100 update-order-status" data-form="orderForm<?= $index ?>">
                                                <i class="fas fa-save me-1"></i>อัปเดตสถานะ
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- ข้อมูลการจัดส่ง -->
                                <?php if ($shipping): ?>
                                    <?php
                                    $shippingStatusColor = 'secondary';
                                    switch($shipping['shipping_status']) {
                                        case 'not_shipped': $shippingStatusColor = 'warning'; break;
                                        case 'shipped': $shippingStatusColor = 'primary'; break;
                                        case 'delivered': $shippingStatusColor = 'success'; break;
                                    }
                                    ?>
                                    <div class="glass-morphism p-3">
                                        <h6 class="gradient-text mb-3">
                                            <i class="fas fa-shipping-fast me-2"></i>ข้อมูลการจัดส่ง
                                        </h6>
                                        <div class="row mb-3">
                                            <div class="col-md-8">
                                                <p class="mb-1">
                                                    <i class="fas fa-map-marker-alt me-2"></i>
                                                    <strong>ที่อยู่:</strong> <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= $shipping['postal_code'] ?>
                                                </p>
                                                <p class="mb-0">
                                                    <i class="fas fa-phone me-2"></i>
                                                    <strong>เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?>
                                                </p>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <span class="badge bg-<?= $shippingStatusColor ?> px-3 py-2">
                                                    <i class="fas fa-truck me-1"></i><?= ucfirst($shipping['shipping_status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <form method="post" class="row g-2" id="shippingForm<?= $index ?>">
                                            <input type="hidden" name="shipping_id" value="<?= $shipping['shipping_id'] ?>">
                                            <div class="col-md-8">
                                                <select name="shipping_status" class="form-select">
                                                    <?php
                                                    $s_statuses = [
                                                        'not_shipped' => 'ยังไม่ได้จัดส่ง',
                                                        'shipped' => 'จัดส่งแล้ว',
                                                        'delivered' => 'ส่งถึงแล้ว'
                                                    ];
                                                    foreach ($s_statuses as $s => $statusText) {
                                                        $selected = ($shipping['shipping_status'] === $s) ? 'selected' : '';
                                                        echo "<option value=\"$s\" $selected>$statusText</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-success w-100 update-shipping-status" data-form="shippingForm<?= $index ?>">
                                                    <i class="fas fa-truck me-1"></i>อัปเดตการจัดส่ง
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script>
        // SweetAlert for Update Order Status
        document.querySelectorAll('.update-order-status').forEach(button => {
            button.addEventListener('click', function() {
                const formId = this.getAttribute('data-form');
                const form = document.getElementById(formId);
                const status = form.querySelector('select[name="status"]').value;
                
                Swal.fire({
                    title: 'ยืนยันการอัปเดตสถานะ?',
                    html: `คุณต้องการเปลี่ยนสถานะคำสั่งซื้อเป็น <strong>${status}</strong> หรือไม่?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
        
        // SweetAlert for Update Shipping Status
        document.querySelectorAll('.update-shipping-status').forEach(button => {
            button.addEventListener('click', function() {
                const formId = this.getAttribute('data-form');
                const form = document.getElementById(formId);
                const status = form.querySelector('select[name="shipping_status"]').value;
                
                Swal.fire({
                    title: 'ยืนยันการอัปเดตการจัดส่ง?',
                    html: `คุณต้องการเปลี่ยนสถานะการจัดส่งเป็น <strong>${status}</strong> หรือไม่?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>