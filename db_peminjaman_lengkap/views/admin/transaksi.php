<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once __DIR__ . '/../../config/database.php';

$message = '';
$error = '';

// PROCESS RETURN (Fitur 5: Penyelesaian Transaksi + Denda Otomatis)
if (isset($_GET['action']) && $_GET['action'] == 'kembali' && isset($_GET['id'])) {
    $id_peminjaman = $_GET['id'];
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("SELECT * FROM peminjaman WHERE id_peminjaman = ? AND status = 'dipinjam'");
        $stmt->execute([$id_peminjaman]);
        $pinjam = $stmt->fetch();
        
        if ($pinjam) {
            $tgl_kembali = date('Y-m-d');
            
            // Perhitungan Denda Otomatis (Rp 2.000 per hari keterlambatan)
            $denda = 0;
            if (strtotime($tgl_kembali) > strtotime($pinjam['tgl_tenggat'])) {
                $selisih = (strtotime($tgl_kembali) - strtotime($pinjam['tgl_tenggat'])) / (60 * 60 * 24);
                $denda = floor($selisih) * 2000;
            }
            
            $up = $pdo->prepare("UPDATE peminjaman SET status = 'dikembalikan', tgl_kembali = ?, denda = ? WHERE id_peminjaman = ?");
            $up->execute([$tgl_kembali, $denda, $id_peminjaman]);
            
            // Otomatisasi Stok Bertambah Kembali
            $stokUp = $pdo->prepare("UPDATE barang SET stok = stok + 1 WHERE id_barang = ?");
            $stokUp->execute([$pinjam['id_barang']]);
            
            $pdo->commit();
            $message = "Transaksi Berhasil Diselesaikan! Denda Keterlambatan: Rp " . number_format($denda, 0, ',', '.');
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Gagal memproses pengembalian: " . $e->getMessage();
    }
}

// PROCESS NEW TRANSACTION (Fitur 4: Transaksi Utama + Stok Berkurang)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pinjam'])) {
    $id_anggota = $_POST['id_anggota'];
    $id_barang = $_POST['id_barang'];
    $tgl_pinjam = date('Y-m-d');
    $tgl_tenggat = date('Y-m-d', strtotime('+7 days'));
    
    try {
        $pdo->beginTransaction();
        
        $stk = $pdo->prepare("SELECT stok FROM barang WHERE id_barang = ?");
        $stk->execute([$id_barang]);
        $barang = $stk->fetch();
        
        if ($barang && $barang['stok'] > 0) {
            $ins = $pdo->prepare("INSERT INTO peminjaman (id_anggota, id_barang, tgl_pinjam, tgl_tenggat, status) VALUES (?, ?, ?, ?, 'dipinjam')");
            $ins->execute([$id_anggota, $id_barang, $tgl_pinjam, $tgl_tenggat]);
            
            // Otomatisasi Stok Berkurang
            $red = $pdo->prepare("UPDATE barang SET stok = stok - 1 WHERE id_barang = ?");
            $red->execute([$id_barang]);
            
            $pdo->commit();
            $message = "Transaksi Peminjaman Berhasil Dicatat!";
        } else {
            $error = "Stok barang ini sedang habis!";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Gagal menambahkan transaksi: " . $e->getMessage();
    }
}

$barangList = $pdo->query("SELECT * FROM barang WHERE stok > 0")->fetchAll();
$anggotaList = $pdo->query("SELECT a.id_anggota, u.nama_lengkap FROM anggota a JOIN users u ON a.id_user = u.id")->fetchAll();
$transaksiList = $pdo->query("SELECT p.*, b.nama_barang, u.nama_lengkap FROM peminjaman p 
                               JOIN barang b ON p.id_barang = b.id_barang 
                               JOIN anggota a ON p.id_anggota = a.id_anggota 
                               JOIN users u ON a.id_user = u.id 
                               ORDER BY p.id_peminjaman DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi & Denda - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 no-print">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">E-Peminjaman Admin</a>
    <div class="navbar-nav me-auto">
        <a class="nav-link" href="dashboard.php">Dashboard</a>
        <a class="nav-link" href="barang.php">CRUD Data Utama (Barang)</a>
        <a class="nav-link" href="anggota.php">CRUD Data Pendukung (Anggota)</a>
        <a class="nav-link active" href="transaksi.php">Transaksi & Denda</a>
    </div>
  </div>
</nav>

<div class="container py-2">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold m-0">Kelola Transaksi & Denda</h3>
        <button onclick="window.print()" class="btn btn-outline-dark no-print">Cetak Laporan (PDF)</button>
    </div>

    <?php if ($message): ?><div class="alert alert-success py-2 no-print"><?= $message ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2 no-print"><?= $error ?></div><?php endif; ?>

    <!-- Form Tambah Transaksi -->
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-header bg-primary text-white fw-bold">Catat Transaksi Peminjaman Baru</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Pilih Anggota</label>
                    <select name="id_anggota" class="form-select" required>
                        <option value="">-- Pilih Anggota --</option>
                        <?php foreach($anggotaList as $a): ?>
                            <option value="<?= $a['id_anggota'] ?>"><?= htmlspecialchars($a['nama_lengkap']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Pilih Barang / Buku</label>
                    <select name="id_barang" class="form-select" required>
                        <option value="">-- Pilih Barang (Tersedia) --</option>
                        <?php foreach($barangList as $b): ?>
                            <option value="<?= $b['id_barang'] ?>"><?= htmlspecialchars($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="tambah_pinjam" class="btn btn-success w-100 fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Transaksi -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">Daftar Semua Transaksi Peminjaman</div>
        <div class="card-body p-0">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Anggota</th>
                        <th>Barang / Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tenggat</th>
                        <th>Status</th>
                        <th>Denda Keterlambatan</th>
                        <th class="no-print">Aksi (Penyelesaian)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transaksiList as $t): ?>
                    <tr>
                        <td><?= $t['id_peminjaman'] ?></td>
                        <td><?= htmlspecialchars($t['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($t['nama_barang']) ?></td>
                        <td><?= $t['tgl_pinjam'] ?></td>
                        <td><?= $t['tgl_tenggat'] ?></td>
                        <td>
                            <span class="badge bg-<?= $t['status'] == 'dipinjam' ? 'warning text-dark' : 'success' ?>">
                                <?= $t['status'] ?>
                            </span>
                        </td>
                        <td><b>Rp <?= number_format($t['denda'], 0, ',', '.') ?></b></td>
                        <td class="no-print">
                            <?php if ($t['status'] == 'dipinjam'): ?>
                                <a href="?action=kembali&id=<?= $t['id_peminjaman'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Proses pengembalian barang dan selesaikan transaksi ini?')">Selesaikan & Kembalikan</a>
                            <?php else: ?>
                                <span class="text-muted small">Selesai</span>
                            <?php endif; ?>
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
