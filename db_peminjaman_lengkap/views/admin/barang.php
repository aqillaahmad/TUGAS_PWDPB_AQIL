<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once __DIR__ . '/../../config/database.php';

$msg = '';
$err = '';

// TAMBAH DATA
if (isset($_POST['tambah'])) {
    $kode = trim($_POST['kode_barang']);
    $nama = trim($_POST['nama_barang']);
    $kategori = trim($_POST['kategori']);
    $stok = (int)$_POST['stok'];
    $lokasi = trim($_POST['lokasi']);

    try {
        $stmt = $pdo->prepare("INSERT INTO barang (kode_barang, nama_barang, kategori, stok, lokasi) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$kode, $nama, $kategori, $stok, $lokasi]);
        $msg = "Data barang berhasil ditambahkan!";
    } catch (Exception $e) {
        $err = "Gagal menambah data: " . $e->getMessage();
    }
}

// EDIT DATA
if (isset($_POST['edit'])) {
    $id = $_POST['id_barang'];
    $nama = trim($_POST['nama_barang']);
    $kategori = trim($_POST['kategori']);
    $stok = (int)$_POST['stok'];
    $lokasi = trim($_POST['lokasi']);

    try {
        $stmt = $pdo->prepare("UPDATE barang SET nama_barang = ?, kategori = ?, stok = ?, lokasi = ? WHERE id_barang = ?");
        $stmt->execute([$nama, $kategori, $stok, $lokasi, $id]);
        $msg = "Data barang berhasil diperbarui!";
    } catch (Exception $e) {
        $err = "Gagal memperbarui data: " . $e->getMessage();
    }
}

// HAPUS DATA
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    try {
        $stmt = $pdo->prepare("DELETE FROM barang WHERE id_barang = ?");
        $stmt->execute([$id]);
        $msg = "Data barang berhasil dihapus!";
    } catch (Exception $e) {
        $err = "Gagal menghapus data: " . $e->getMessage();
    }
}

// SEARCH & FILTER (Fitur Tambahan)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM barang WHERE nama_barang LIKE ? OR kode_barang LIKE ? OR kategori LIKE ?");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    $barangList = $stmt->fetchAll();
} else {
    $barangList = $pdo->query("SELECT * FROM barang ORDER BY id_barang DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Data Utama (Barang) - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">E-Peminjaman Admin</a>
    <div class="navbar-nav me-auto">
        <a class="nav-link" href="dashboard.php">Dashboard</a>
        <a class="nav-link active" href="barang.php">CRUD Data Utama (Barang)</a>
        <a class="nav-link" href="anggota.php">CRUD Data Pendukung (Anggota)</a>
        <a class="nav-link" href="transaksi.php">Transaksi & Denda</a>
    </div>
  </div>
</nav>

<div class="container py-2">
    <h3 class="fw-bold mb-3">CRUD Data Utama (Barang / Buku)</h3>

    <?php if ($msg): ?><div class="alert alert-success py-2"><?= $msg ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger py-2"><?= $err ?></div><?php endif; ?>

    <!-- Form Tambah -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white fw-bold">Tambah Data Barang Baru</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control" required placeholder="BRG005">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Nama Barang/Buku</label>
                    <input type="text" name="nama_barang" class="form-control" required placeholder="Nama Barang">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Kategori</label>
                    <input type="text" name="kategori" class="form-control" required placeholder="Buku/Elektronik">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Stok</label>
                    <input type="number" name="stok" class="form-control" required min="0" value="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Lokasi Penyimpanan</label>
                    <input type="text" name="lokasi" class="form-control" required placeholder="Rak A-1 / Gudang">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" name="tambah" class="btn btn-success fw-bold px-4">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pencarian & Tabel -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="fw-bold m-0">Daftar Barang / Buku</h5>
            <form method="GET" class="d-flex w-50">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari nama/kode/kategori..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-secondary">Cari</button>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barangList as $b): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($b['kode_barang']) ?></b></td>
                        <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($b['kategori']) ?></span></td>
                        <td><b><?= $b['stok'] ?></b></td>
                        <td><?= htmlspecialchars($b['lokasi']) ?></td>
                        <td>
                            <!-- Modal Button Edit -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $b['id_barang'] ?>">Edit</button>
                            <a href="?hapus=<?= $b['id_barang'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus barang ini?')">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $b['id_barang'] ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form method="POST">
                              <div class="modal-header">
                                <h5 class="modal-title">Edit Barang: <?= htmlspecialchars($b['kode_barang']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <input type="hidden" name="id_barang" value="<?= $b['id_barang'] ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($b['nama_barang']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($b['kategori']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stok</label>
                                    <input type="number" name="stok" class="form-control" value="<?= $b['stok'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Lokasi</label>
                                    <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($b['lokasi']) ?>" required>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                              </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
