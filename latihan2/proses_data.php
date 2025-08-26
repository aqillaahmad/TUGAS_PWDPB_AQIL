<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];

    $query = "UPDATE siswa SET nama='$nama', email='$email', alamat='$alamat' WHERE id=$id";
    if ($koneksi->query($query) === TRUE) {
        echo "Data berhasil ditambahkan.";
        echo "<br><a href='tampil_data.php'>Lihat Data</a>";
    } else {
    echo "Error: " . $query . "<br>". $koneksi->error;
    }
}
?>