<?php
Include "koneksi.php";

if ($_SERVER ["REQUEST_METHOD"]=="POST") {
    $nama=$_POST["nama"];
    $email=$_POST['email'];
    $alamat=$_POST['alamat'];

$query = "INSERT INTO siswa (nama, email, alamat) VALUES ('$nama', '$email','$alamat')";

if ($koneksi-> query($query)=== TRUE) {
    echo "Data berhasil ditambahkan.";
    echo "<br><a href='tampil_data.php'>Lihat Data/a>";
} else {
    echo "Error; " . $quary . "<br>" . $koneksi->eror;
}
}
?>