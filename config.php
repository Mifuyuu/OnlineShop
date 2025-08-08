
<?php

$host = "localhost";
$user = "root";
$pass = "";
$database = "online_shop";

$dsn = "mysql:host=$host;dbname=$database";

try {
    $conn = new PDO($dsn, $user, $pass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connection Successful!";
} catch (PDOException $err) {
    echo "Connection Failed: ".$err->getMessage();
}
?>