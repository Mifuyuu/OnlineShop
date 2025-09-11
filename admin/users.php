<?php
require_once '../config.php';
require_once 'auth_admin.php';

// if (isset($_GET['delete'])) {
//     $user_id = $_GET['delete'];
//     // ป้องกันลบตัวเอง
//     if ($user_id != $_SESSION['user_id']) {
//         $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
//         $stmt->execute([$user_id]);
//     }
//     header("Location: users.php");
//     exit;
// }

// ดึงข้อมูลสมาชิก
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจัดการสมาชิก - OnlineShop Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/font/LINESeedSansTH.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Sweetalert CDN -->
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
                    <i class="fas fa-users me-2"></i>จัดการสมาชิก
                </h1>
                <div class="mb-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>กลับหน้าผู้ดูแล
                    </a>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= $_SESSION['error_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <?php if (count($users) === 0): ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-info-circle me-2"></i>ยังไม่มีสมาชิกในระบบ
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-user me-2"></i>ชื่อผู้ใช้</th>
                                        <th><i class="fas fa-id-card me-2"></i>ชื่อ - นามสกุล</th>
                                        <th><i class="fas fa-envelope me-2"></i>อีเมล</th>
                                        <th><i class="fas fa-calendar me-2"></i>วันที่สมัคร</th>
                                        <th><i class="fas fa-cogs me-2"></i>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($user['username']) ?></strong>
                                                <br>
                                                <span class="user-badge">
                                                    <i class="fas fa-user me-1"></i>Member
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                                            <td>
                                                <i class="fas fa-at me-1 text-muted"></i>
                                                <?= htmlspecialchars($user['email']) ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-clock me-1 text-muted"></i>
                                                <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="edit_users.php?id=<?= $user['user_id'] ?>"
                                                        class="btn btn-warning btn-sm"
                                                        title="แก้ไขข้อมูล">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="del_users.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="u_id" value="<?php echo $user['user_id']; ?>">
                                                        <button type="button" class="delete-button btn btn-danger btn-sm" data-user-id="<?php echo $user['user_id']; ?>"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                    <!-- <a href="users.php?delete=< ?= $user['user_id'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('คุณต้องการลบสมาชิกนี้หรือไม่?')"
                                                        title="ลบสมาชิก">
                                                        <i class="fas fa-trash"></i>
                                                    </a> -->
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <div class="alert alert-info d-inline-block">
                            <i class="fas fa-info-circle me-2"></i>
                            พบสมาชิกทั้งหมด <strong><?= count($users) ?></strong> คน
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <script>
            // ฟังกช์ นั ส ำหรับแสดงกลอ่ งยนื ยัน SweetAlert2
            function showDeleteConfirmation(userId) {
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: 'คุณจะไม่สามารถเรียกคืนข้อมูลกลับได้!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // หำกผใู้ชย้นื ยัน ใหส้ ง่ คำ่ ฟอรม์ ไปยัง delete.php เพื่อลบข ้อมูล
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'del_users.php';
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'u_id';
                        input.value = userId;
                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
            // แนบตัวตรวจจับเหตุกำรณ์คลิกกับองค์ปุ ่่มลบทั ่ ้งหมดที่มีคลำส delete-button
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const userId = button.getAttribute('data-user-id');
                    showDeleteConfirmation(userId);
                });
            });
        </script>
    </body>

</html>