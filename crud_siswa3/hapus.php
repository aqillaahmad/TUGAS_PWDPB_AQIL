<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

if (isset($_GET['id'])) {
    $id  = $_GET['id'];
    $sql = "DELETE FROM siswa WHERE id = '$id'";
    $q   = mysqli_query($koneksi, $sql);

    if ($q) {
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location='index.php';</script>";
    }
}
?>
