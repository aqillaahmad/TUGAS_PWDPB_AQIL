<?php
$host = "localhost";
$user ="root";
$pass = "";
$dbname = "db_siswa";

// Membuat koneksi
$koneksi = new mysqli($host, $user, $pass, $dbname);

// Menegecek koneksi
if ($koneksi->connect_error) {
    die("koneksi gagal: " . $koneksii->connect_error);
}
?>