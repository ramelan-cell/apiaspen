<?php
include "koneksi.php";

$q1 = "SELECT latitude , longitude FROM app_kode_kantor  ";

$exe = mysqli_query($KONEKSI, $q1);
while ($row = mysqli_fetch_assoc($exe)){
    $data_lokasi[] = $row;
}

echo json_encode($data_lokasi);
mysqli_free_result($exe);
mysqli_close($KONEKSI);