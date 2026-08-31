<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once __DIR__ . '/../../config/database.php';

$countBarang = $pdo->query("SELECT COUNT(*) FROM barang")->fetchColumn();
$countAnggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
$countPinjam = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn();

$sql = "SELECT p.*, b.nama_barang, u.nama_lengkap FROM peminjaman p 
        JOIN barang b ON p.id_barang = b.id_barang 
        JOIN anggota a ON p.id_anggota = a.id_anggota 
        JOIN users u ON a.id_user = u.id 
        ORDER BY p.id_peminjaman DESC LIMIT 5";
$recentLoans = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - E-Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">E-Peminjaman Admin</a>
    <div class="navbar-nav me-auto">
        <a class="nav-link active" href="dashboard.php">Dashboard</a>
        <a class="nav-link" href="barang.php">CRUD Data Utama (Barang)</a>
        <a class="nav-link" href="anggota.php">CRUD Data Pendukung (Anggota)</a>
        <a class="nav-link" href="transaksi.php">Transaksi & Denda</a>
    </div>
    <div class="d-flex align-items-center">
      <span class="navbar-text me-3 text-white">Halo, <b><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></b></span>
      <a href="../../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white p-3 shadow-sm rounded-3">
                <h5>Total Barang / Buku</h5>
                <h2 class="fw-bold m-0"><?= $countBarang ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white p-3 shadow-sm rounded-3">
                <h5>Total Anggota</h5>
                <h2 class="fw-bold m-0"><?= $countAnggota ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white p-3 shadow-sm rounded-3">
                <h5>Transaksi Aktif</h5>
                <h2 class="fw-bold m-0"><?= $countPinjam ?></h2>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="m-0 fw-bold">Ringkasan Peminjaman Terbaru</h5>
            <a href="transaksi.php" class="btn btn-sm btn-primary">Kelola Transaksi</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Peminjam</th>
                        <th>Barang / Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentLoans as $loan): ?>
                    <tr>
                        <td><?= $loan['id_peminjaman'] ?></td>
                        <td><?= htmlspecialchars($loan['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($loan['nama_barang']) ?></td>
                        <td><?= $loan['tgl_pinjam'] ?></td>
                        <td>
                            <span class="badge bg-<?= $loan['status'] == 'dipinjam' ? 'warning text-dark' : 'success' ?>">
                                <?= $loan['status'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
