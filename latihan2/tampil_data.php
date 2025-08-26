<?php
include 'koneksi.php';

$query = "SELECT * FROM siswa";
$result = $koneksi->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1'>
    <tr>
    <th>ID</th>
    <th>Nama</th>
    <th>Email</th>
    <th>Alamat</th>
    </tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>" . $row['id']."</td>
        <td>" . $row['nama']. "</td>
        <td>" . $row['email'] . "</td>
        <td>" . $row['alamat'] . "</td>
    </tr>";
}
echo "</table>";
} else {
echo "Tidak ada data siswa.";
}
?>