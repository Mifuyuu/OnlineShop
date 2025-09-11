<?php

require_once '../config.php';
require_once 'auth_admin.php';

// ตรวจสอบกำรสง่ ขอ้ มลู จำกฟอรม์
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['u_id'])) {
    $user_id = $_POST['u_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
    $stmt->execute([$user_id]);

    // สง่ ผลลัพธก์ ลับไปยังหนำ้ users.php
    header("Location: users.php");
    exit;
}
