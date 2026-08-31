<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anggota') {
    header("Location: ../../index.php");
    exit;
}
require_once __DIR__ . '/../../config/database.php';

$stmt = $pdo->prepare("SELECT p.*, b.nama_barang FROM peminjaman p 
                       JOIN barang b ON p.id_barang = b.id_barang 
                       JOIN anggota a ON p.id_anggota = a.id_anggota 
                       WHERE a.id_user = ? ORDER BY p.id_peminjaman DESC");
$stmt->execute([$_SESSION['user_id']]);
$myLoans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Anggota - E-Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">E-Peminjaman Anggota</a>
    <div class="d-flex align-items-center">
      <span class="navbar-text me-3 text-white">Selamat Datang, <b><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></b></span>
      <a href="../../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 fw-bold">Riwayat Peminjaman Saya</div>
        <div class="card-body p-0">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID Peminjaman</th>
                        <th>Nama Barang / Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tenggat Pengembalian</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($myLoans)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Anda belum memiliki riwayat peminjaman.</td></tr>
                    <?php else: ?>
                        <?php foreach($myLoans as $l): ?>
                        <tr>
                            <td><?= $l['id_peminjaman'] ?></td>
                            <td><?= htmlspecialchars($l['nama_barang']) ?></td>
                            <td><?= $l['tgl_pinjam'] ?></td>
                            <td><?= $l['tgl_tenggat'] ?></td>
                            <td>
                                <span class="badge bg-<?= $l['status'] == 'dipinjam' ? 'warning text-dark' : 'success' ?>">
                                    <?= $l['status'] ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($l['denda'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
