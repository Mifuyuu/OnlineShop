<?php
require_once 'connect.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$key = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM tb_664230033 WHERE `key` = ?");
$stmt->execute([$key]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// ========== เมอื่ ผใู้ชก้ด Submit ฟอร์ม ==========

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $std_id = $_POST['std_id'];
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $age = $_POST['age'];
    $mail = $_POST['mail'];
    $tel = $_POST['tel'];

    // TODO-8: ตรวจควำมครบถ ้วน และตรวจรูปแบบ email
    if ($std_id === '' || $mail === '') {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    }
    
    if (!$error) {
        $check = $conn->prepare("SELECT 1 FROM tb_664230033 WHERE mail = ? AND std_id = ?");
        $check->execute([$std_id, $mail]);
        if ($check->fetch()) {
            $error = "รหัสนักศึกษาหรืออีเมล";
        }
    }

    if (!$error) {
        $upd = $conn->prepare("UPDATE tb_664230033 SET std_id = ?, f_name = ?, l_name = ?, age = ?, mail = ?, tel = ? WHERE `key` = ?");
        $upd->execute([$std_id, $f_name, $l_name, $age, $mail, $tel, $key]);
        header("Location: index.php");
        exit;
    }

    $student['std_id'] = $std_id;
    $student['f_name'] = $f_name;
    $student['l_name'] = $l_name;
    $student['age'] = $age;
    $student['mail'] = $mail;
    $student['tel'] = $tel;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลนักศึกษา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Main Content -->
    <div class="container">
        <div class="main-container p-4 p-md-5">
            <h1 class="page-title">
                <i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลนักศึกษา
            </h1>

            <div class="row mb-4">
                <div class="col-12">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>กลับหน้ารายชื่อสมาชิก
                    </a>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- ฟอร์มแก้ไขข้อมูลสมาชิก -->
            <div class="admin-card">
                <h5 class="mb-4">
                    <i class="fas fa-edit me-2"></i>แก้ไขข้อมูล:
                </h5>
                <form method="post" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-user me-2"></i>รหัสนักศึกษา
                        </label>
                        <input type="text" name="std_id" class="form-control" value="<?= htmlspecialchars($student['std_id']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-user me-2"></i>ชื่อ
                        </label>
                        <input type="text" name="f_name" class="form-control" required
                            value="<?= htmlspecialchars($student['f_name']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-id-card me-2"></i>นามสกุล
                        </label>
                        <input type="text" name="l_name" class="form-control"
                            value="<?= htmlspecialchars($student['l_name']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-person me-2"></i>อายุ
                        </label>
                        <input type="number" name="age" class="form-control"
                            value="<?= htmlspecialchars($student['age']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fas fa-envelope me-2"></i>อีเมล
                        </label>
                        <input type="email" name="mail" class="form-control" required
                            value="<?= htmlspecialchars($student['mail']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fas fa-phone me-2"></i>เบอร์โทรศัพท์
                        </label>
                        <input type="email" name="tel" class="form-control" required
                            value="<?= htmlspecialchars($student['tel']) ?>">
                    </div>
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="saveUserBtn">
                                <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>ยกเลิก
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // SweetAlert for Save User Confirmation
        document.getElementById('saveUserBtn').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'ยืนยันการบันทึก?',
                text: 'คุณต้องการบันทึกการแก้ไขข้อมูลสมาชิกนี้หรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form
                    e.target.closest('form').submit();
                }
            });
        });
    </script>
</body>

</html>