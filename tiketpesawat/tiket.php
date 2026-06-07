<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ===============================
// DATA HARGA TIKET
// ===============================
$harga = [
    'GRD' => [
        'nama' => 'Garuda',
        'Eksekutif' => 1500000,
        'Bisnis' => 900000,
        'Ekonomi' => 500000
    ],
    'MPT' => [
        'nama' => 'Merpati',
        'Eksekutif' => 1200000,
        'Bisnis' => 800000,
        'Ekonomi' => 400000
    ],
    'BTV' => [
        'nama' => 'Batavia',
        'Eksekutif' => 1000000,
        'Bisnis' => 700000,
        'Ekonomi' => 300000
    ]
];

$result = null;

if (isset($_POST['simpan'])) {
    $nama   = $_POST['nama'];
    $kode   = $_POST['kode'];
    $kelas  = $_POST['kelas'];
    $jumlah = (int)$_POST['jumlah'];

    $namaPesawat = $harga[$kode]['nama'];
    $hargaTiket  = $harga[$kode][$kelas];
    $totalBayar  = $hargaTiket * $jumlah;

    $result = [
        'nama' => $nama,
        'namaPesawat' => $namaPesawat,
        'kelas' => $kelas,
        'harga' => $hargaTiket,
        'jumlah' => $jumlah,
        'total' => $totalBayar
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tiket Online Jakarta - Malaysia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .box {
            width: 400px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            padding: 20px;
            margin: 20px auto;
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
        }

        td {
            padding: 6px;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 6px;
            box-sizing: border-box;
        }

        button {
            padding: 6px 15px;
            margin: 5px;
        }

        .result {
            font-family: "Courier New", monospace;
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .logout-btn {
            text-align: right;
            margin-bottom: 10px;
        }

        .logout-btn a {
            color: #ff4444;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-btn a:hover {
            text-decoration: underline;
        }

        .user-info {
            text-align: right;
            margin-bottom: 10px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="box">
    <div class="user-info">
        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 
        <a href="logout.php" style="color: #ff4444;">Logout</a>
    </div>
    <h3>TIKET ONLINE<br>JAKARTA - MALAYSIA</h3>
    <form method="post">
        <table>
            <tr>
                <td>Nama</td>
                <td><input type="text" name="nama" required></td>
            </tr>
            <tr>
                <td>Kode Pesawat</td>
                <td>
                    <select name="kode">
                        <option value="GRD">GRD</option>
                        <option value="MPT">MPT</option>
                        <option value="BTV">BTV</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>
                    <input type="radio" name="kelas" value="Eksekutif" checked> Eksekutif<br>
                    <input type="radio" name="kelas" value="Bisnis"> Bisnis<br>
                    <input type="radio" name="kelas" value="Ekonomi"> Ekonomi
                </td>
            </tr>
            <tr>
                <td>Jumlah Tiket</td>
                <td>
                    <select name="jumlah">
                        <?php for ($i = 1; $i <= 10; $i++) echo "<option>$i</option>"; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button type="submit" name="simpan">SIMPAN</button>
                    <button type="reset">BATAL</button>
                </td>
            </tr>
        </table>
    </form>
</div>

<!-- HASIL OUTPUT -->
<?php if ($result): ?>
<div class="box">
    <h3>DETAIL PEMESANAN</h3>
    <div class="result">
        <?php
        function line($label, $value) {
            echo $label . " : " . $value . "<br>";
        }
        line('Nama', $result['nama']);
        line('Pesawat', $result['namaPesawat']);
        line('Kelas', $result['kelas']);
        line('Harga', 'Rp ' . number_format($result['harga'],0,',','.'));
        line('Jumlah', $result['jumlah']);
        line('Total Bayar', 'Rp ' . number_format($result['total'],0,',','.'));
        ?>
    </div>
</div>
<?php endif; ?>

</body>
</html>