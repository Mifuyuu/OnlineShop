<?php
require_once '../config.php';
require_once 'auth_admin.php';

// ข้อมูลตัวอย่างสำหรับสร้างผู้ใช้
$firstNames = [
    'สมชาย', 'สมหญิง', 'วิชัย', 'วิชิตา', 'ประเสริฐ', 'ประภา', 'สุพจน์', 'สุมาลี',
    'อนุชา', 'อนุสรา', 'เจริญ', 'เจริญลักษณ์', 'บุญมี', 'บุญเรือน', 'สุกิจ', 'สุกัญญา',
    'วันชัย', 'วันเพ็ญ', 'มานะ', 'มาลี', 'ธนพล', 'ธนพร', 'ปิยะ', 'ปิยาดา',
    'ศรีสมร', 'ศรีพิมพ์', 'จิตรา', 'จิรายุ', 'กรณ์', 'กนิษฐา', 'ธีระ', 'ธีรดา',
    'นิรันดร์', 'นิรมล', 'ประยุทธ', 'ประนอม', 'สิริ', 'สิริพร', 'รัตน์', 'รัตนา'
];

$lastNames = [
    'จันทร์เพ็ญ', 'แสงแก้ว', 'ทองดี', 'เพชรกลม', 'สุขสม', 'มีสุข', 'รวยเงิน',
    'บุญมาก', 'ดีใจ', 'ใจดี', 'กล้าหาญ', 'ชาญฤทธิ์', 'วีรกุล', 'อาจหาญ',
    'สง่างาม', 'งามพริ้ง', 'มั่นคง', 'แข็งแรง', 'ยั่งยืน', 'ทนทาน',
    'เรืองแสง', 'สว่างใส', 'ใสใจ', 'ร่าเริง', 'สุกใส', 'บริสุทธิ์',
    'ปราณีต', 'ประณีต', 'สง่า', 'งาม', 'รุ่งเรือง', 'เจริญรุ่ง',
    'พัฒนา', 'ก้าวหน้า', 'มั่งมี', 'ร่ำรวย', 'ศิริมงคล', 'บุญล้น'
];

$domains = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com', 'live.com'];

// ฟังก์ชันสร้างรหัสผ่านแบบสุ่ม
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

// ฟังก์ชันสร้าง username ที่ไม่ซ้ำ
function generateUniqueUsername($conn, $firstName, $lastName) {
    $baseUsername = strtolower(transliterate($firstName . $lastName));
    $username = $baseUsername;
    $counter = 1;
    
    while (true) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetchColumn() == 0) {
            return $username;
        }
        
        $username = $baseUsername . $counter;
        $counter++;
    }
}

// ฟังก์ชันแปลงภาษาไทยเป็นอังกฤษแบบง่าย
function transliterate($text) {
    $thai = ['ก','ข','ค','ง','จ','ฉ','ช','ซ','ญ','ด','ต','ถ','ท','ธ','น','บ','ป','ผ','ฝ','พ','ฟ','ภ','ม','ย','ร','ล','ว','ศ','ษ','ส','ห','ฬ','อ','ฮ','ะ','า','ิ','ี','ึ','ื','ุ','ู','เ','แ','โ','ใ','ไ','ำ','่','้','๊','๋','์'];
    $eng = ['k','k','k','ng','j','ch','ch','s','y','d','t','t','t','t','n','b','p','p','f','p','f','p','m','y','r','l','w','s','s','s','h','l','o','h','a','a','i','i','ue','ue','u','u','e','ae','o','ai','ai','am','','','','',''];
    
    return str_replace($thai, $eng, $text);
}

// ฟังก์ชันสร้าง email ที่ไม่ซ้ำ
function generateUniqueEmail($conn, $username, $domains) {
    $domain = $domains[array_rand($domains)];
    $email = $username . '@' . $domain;
    $counter = 1;
    
    while (true) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetchColumn() == 0) {
            return $email;
        }
        
        $email = $username . $counter . '@' . $domain;
        $counter++;
    }
}

// สร้างผู้ใช้ใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_users'])) {
    $count = (int)$_POST['user_count'];
    $count = min(max($count, 1), 100); // จำกัดระหว่าง 1-100
    
    $createdUsers = [];
    $errors = [];
    
    try {
        $conn->beginTransaction();
        
        for ($i = 0; $i < $count; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = $firstName . ' ' . $lastName;
            
            $username = generateUniqueUsername($conn, $firstName, $lastName);
            $email = generateUniqueEmail($conn, $username, $domains);
            $password = generatePassword();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, 'member')");
            
            if ($stmt->execute([$username, $hashedPassword, $email, $fullName])) {
                $createdUsers[] = [
                    'username' => $username,
                    'password' => $password,
                    'email' => $email,
                    'full_name' => $fullName
                ];
            } else {
                $errors[] = "ไม่สามารถสร้างผู้ใช้ $fullName ได้";
            }
        }
        
        $conn->commit();
        $success = "สร้างผู้ใช้สำเร็จ " . count($createdUsers) . " คน";
        
    } catch (Exception $e) {
        $conn->rollBack();
        $errors[] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

// ลบผู้ใช้ทั้งหมด (ยกเว้น admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all_members'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE role = 'member'");
        $stmt->execute();
        $deletedCount = $stmt->rowCount();
        $deleteSuccess = "ลบสมาชิกทั้งหมด $deletedCount คน เรียบร้อยแล้ว";
    } catch (Exception $e) {
        $deleteError = "เกิดข้อผิดพลาดในการลบ: " . $e->getMessage();
    }
}

// นับจำนวนผู้ใช้ปัจจุบัน
$stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'member'");
$currentMemberCount = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างผู้ใช้สำหรับทดสอบ - OnlineShop Admin</title>
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
                <i class="fas fa-user-plus me-2"></i>สร้างผู้ใช้สำหรับทดสอบ
            </h1>

            <div class="mb-4">
                <a href="users.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>กลับไปจัดการสมาชิก
                </a>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>หน้าผู้ดูแล
                </a>
            </div>

            <!-- แสดงข้อมูลสถิติ -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="admin-card text-center">
                        <h3 class="text-primary"><?= $currentMemberCount ?></h3>
                        <p class="mb-0">จำนวนสมาชิกปัจจุบัน</p>
                    </div>
                </div>
            </div>

            <!-- แสดงข้อความแจ้งเตือน -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($deleteSuccess)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= $deleteSuccess ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>เกิดข้อผิดพลาด:
                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($deleteError)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= $deleteError ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- ฟอร์มสร้างผู้ใช้ -->
            <div class="admin-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-users me-2"></i>สร้างสมาชิกสำหรับทดสอบ
                </h5>
                <form method="post" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">จำนวนที่ต้องการสร้าง</label>
                        <input type="number" name="user_count" class="form-control" value="10" min="1" max="100" required>
                        <div class="form-text">สามารถสร้างได้สูงสุด 100 คนต่อครั้ง</div>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <button type="submit" name="generate_users" class="btn btn-primary" onclick="return confirm('ยืนยันการสร้างผู้ใช้สำหรับทดสอบ?')">
                            <i class="fas fa-user-plus me-2"></i>สร้างสมาชิก
                        </button>
                    </div>
                </form>
            </div>

            <!-- ฟอร์มลบผู้ใช้ทั้งหมด -->
            <div class="admin-card mb-4">
                <h5 class="mb-3 text-danger">
                    <i class="fas fa-trash me-2"></i>ลบสมาชิกทั้งหมด
                </h5>
                <p class="text-muted mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    คำเตือน: การกระทำนี้จะลบสมาชิกทั้งหมด (ยกเว้นบัญชี Admin) และไม่สามารถกู้คืนได้
                </p>
                <form method="post">
                    <button type="submit" name="delete_all_members" class="btn btn-danger" 
                            onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบสมาชิกทั้งหมด? การกระทำนี้ไม่สามารถกู้คืนได้!')">
                        <i class="fas fa-trash me-2"></i>ลบสมาชิกทั้งหมด
                    </button>
                </form>
            </div>

            <!-- แสดงรายการผู้ใช้ที่สร้างใหม่ -->
            <?php if (!empty($createdUsers)): ?>
                <div class="admin-card">
                    <h5 class="mb-3">
                        <i class="fas fa-list me-2"></i>รายการผู้ใช้ที่สร้างใหม่
                    </h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        กรุณาบันทึกข้อมูลเหล่านี้ไว้สำหรับการทดสอบ
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>ชื่อผู้ใช้</th>
                                    <th>รหัสผ่าน</th>
                                    <th>อีเมล</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($createdUsers as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                                        <td><code><?= htmlspecialchars($user['username']) ?></code></td>
                                        <td><code><?= htmlspecialchars($user['password']) ?></code></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>