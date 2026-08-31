<?php
$host = "localhost";
$db   = "db_peminjaman_lengkap";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div style='color:red; font-family:sans-serif; padding:20px; border:1px solid red; margin:20px;'>
        <h3>Koneksi Database Gagal!</h3>
        <p>Pastikan MySQL di XAMPP sudah aktif dan database <b>db_peminjaman_lengkap</b> sudah dibuat di phpMyAdmin.</p>
        <small>Error: " . htmlspecialchars($e->getMessage()) . "</small>
    </div>");
}
?>
