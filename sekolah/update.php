<?php
include 'config.php';
$id = $_GET['id'];
$sql = "SELECT * FROM siswa WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $nama = $_POST['nama'];
 $sekolah = $_POST['sekolah'];
 $jurusan = $_POST['jurusan'];
 $no_hp = $_POST['no_hp'];
 $alamat = $_POST['alamat'];
 $sql = "UPDATE siswa SET nama='$nama', 
sekolah='$sekolah', jurusan='$jurusan', no_hp='$no_hp', 
alamat='$alamat' WHERE id=$id";
 if ($conn->query($sql) === TRUE) {
 header('Location: index.php');
 exit();
 } else {
 echo "Error: " . $sql . "<br>" . $conn->error;
 }
}
?>