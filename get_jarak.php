<?php
include "koneksi.php";

$q1 = "SELECT deskripsi  FROM parameter WHERE id ='JARAK'   ";

$exe = mysqli_query($KONEKSI, $q1);
$result = mysqli_fetch_assoc($exe);
$jarak = $result['deskripsi'];

echo json_encode($jarak);
mysqli_free_result($exe);
mysqli_close($KONEKSI);