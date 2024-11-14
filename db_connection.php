<?php
// Database connection settings
$host = 'localhost';
$db = 'webApp';
$user = 'root'; // Default user for XAMPP
$pass = 'mysql@13'; // Default password is empty for XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
