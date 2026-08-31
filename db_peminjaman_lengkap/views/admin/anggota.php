<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once __DIR__ . '/../../config/database.php';

$msg = '';
$err = '';

// TAMBAH ANGGOTA
if (isset($_POST['tambah'])) {
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama_lengkap']);
    $no_induk = trim($_POST['nomor_induk']);
    $telepon = trim($_POST['telepon']);
    $alamat = trim($_POST['alamat']);
    $pass = password_hash('admin123', PASSWORD_BCRYPT);

    try {
        $pdo->beginTransaction();
        $stmt1 = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'anggota')");
        $stmt1->execute([$username, $pass, $nama]);
        $id_user = $pdo->lastInsertId();

        $stmt2 = $pdo->prepare("INSERT INTO anggota (id_user, nomor_induk, telepon, alamat) VALUES (?, ?, ?, ?)");
        $stmt2->execute([$id_user, $no_induk, $telepon, $alamat]);

        $pdo->commit();
        $msg = "Anggota baru berhasil ditambahkan! (Password default: admin123)";
    } catch (Exception $e) {
        $pdo->rollBack();
        $err = "Gagal menambah anggota: " . $e->getMessage();
    }
}

// HAPUS ANGGOTA
if (isset($_GET['hapus'])) {
    $id_user = $_GET['hapus'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id_user]);
        $msg = "Data anggota berhasil dihapus!";
    } catch (Exception $e) {
        $err = "Gagal menghapus data: " . $e->getMessage();
    }
}

$anggotaList = $pdo->query("SELECT a.*, u.username, u.nama_lengkap FROM anggota a JOIN users u ON a.id_user = u.id ORDER BY a.id_anggota DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Data Pendukung (Anggota) - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">E-Peminjaman Admin</a>
    <div class="navbar-nav me-auto">
        <a class="nav-link" href="dashboard.php">Dashboard</a>
        <a class="nav-link" href="barang.php">CRUD Data Utama (Barang)</a>
        <a class="nav-link active" href="anggota.php">CRUD Data Pendukung (Anggota)</a>
        <a class="nav-link" href="transaksi.php">Transaksi & Denda</a>
    </div>
  </div>
</nav>

<div class="container py-2">
    <h3 class="fw-bold mb-3">CRUD Data Pendukung (Kelola Anggota)</h3>

    <?php if ($msg): ?><div class="alert alert-success py-2"><?= $msg ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger py-2"><?= $err ?></div><?php endif; ?>

    <!-- Form Tambah -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-success text-white fw-bold">Tambah Anggota Baru</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Username Login</label>
                    <input type="text" name="username" class="form-control" required placeholder="username">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required placeholder="Nama Lengkap">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Nomor Induk/NIM</label>
                    <input type="text" name="nomor_induk" class="form-control" required placeholder="ANG002">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">No. Telepon</label>
                    <input type="text" name="telepon" class="form-control" required placeholder="0812...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Alamat</label>
                    <input type="text" name="alamat" class="form-control" required placeholder="Alamat">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" name="tambah" class="btn btn-success fw-bold px-4">Simpan Anggota</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Anggota -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 fw-bold">Daftar Anggota Terdaftar</div>
        <div class="card-body p-0">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>No Induk</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($anggotaList as $a): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($a['nomor_induk']) ?></b></td>
                        <td><?= htmlspecialchars($a['username']) ?></td>
                        <td><?= htmlspecialchars($a['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($a['telepon']) ?></td>
                        <td><?= htmlspecialchars($a['alamat']) ?></td>
                        <td>
                            <a href="?hapus=<?= $a['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus anggota ini beserta akunnya?')">Hapus</a>
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
