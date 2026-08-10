<?php
session_start();

// Proteksi halaman: Cek apakah user sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

$id    = "";
$nisn  = "";
$nama  = "";
$kelas = "";

// Ambil data jika tombol EDIT diklik
if (isset($_GET['op']) && $_GET['op'] == 'edit') {
    $id    = $_GET['id'];
    $sql   = "SELECT * FROM siswa WHERE id = '$id'";
    $q     = mysqli_query($koneksi, $sql);
    $r     = mysqli_fetch_array($q);
    if ($r) {
        $nisn  = $r['nisn'];
        $nama  = $r['nama'];
        $kelas = $r['kelas'];
    }
}

// Proses SIMPAN atau UPDATE data
if (isset($_POST['simpan'])) {
    $nisn  = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);

    if (isset($_GET['op']) && $_GET['op'] == 'edit' && !empty($id)) {
        $sql = "UPDATE siswa SET nisn='$nisn', nama='$nama', kelas='$kelas' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO siswa (nisn, nama, kelas) VALUES ('$nisn', '$nama', '$kelas')";
    }

    $q = mysqli_query($koneksi, $sql);
    if ($q) {
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Data Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 80px; }
        input[type="text"] { padding: 5px; width: 250px; }
        button { padding: 6px 12px; cursor: pointer; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-edit { background-color: #ffc107; border: none; padding: 4px 8px; color: black; text-decoration: none; }
        .btn-delete { background-color: #dc3545; border: none; padding: 4px 8px; color: white; text-decoration: none; }
        .btn-logout { background-color: #6c757d; padding: 6px 12px; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Form Input Siswa</h2>
        <a href="logout.php" class="btn-logout">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
    </div>

    <form action="" method="POST">
        <div class="form-group">
            <label>NISN</label>
            <input type="text" name="nisn" value="<?php echo htmlspecialchars($nisn); ?>" required>
        </div>
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($nama); ?>" required>
        </div>
        <div class="form-group">
            <label>Kelas</label>
            <input type="text" name="kelas" value="<?php echo htmlspecialchars($kelas); ?>" required>
        </div>
        <button type="submit" name="simpan">Simpan Data</button>
    </form>

    <hr>

    <h2>Data Siswa</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql2   = "SELECT * FROM siswa ORDER BY id DESC";
            $q2     = mysqli_query($koneksi, $sql2);
            $nomor  = 1;

            while ($r2 = mysqli_fetch_array($q2)) {
            ?>
                <tr>
                    <td><?php echo $nomor++; ?></td>
                    <td><?php echo htmlspecialchars($r2['nisn']); ?></td>
                    <td><?php echo htmlspecialchars($r2['nama']); ?></td>
                    <td><?php echo htmlspecialchars($r2['kelas']); ?></td>
                    <td>
                        <a href="index.php?op=edit&id=<?php echo $r2['id']; ?>" class="btn-edit">Edit</a>
                        <a href="hapus.php?id=<?php echo $r2['id']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus data?')">Hapus</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</body>
</html>
