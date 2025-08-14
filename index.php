<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h3>หน้าหลัก Member</h3>
    <p>Welcome <?= htmlspecialchars($_SESSION['username']) ?>(<?= $_SESSION['role']?>)</p>
    <button><a href="logout.php">Logout</a></button>
</body>
</html>