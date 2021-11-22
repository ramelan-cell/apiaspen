<?php
include "koneksi.php";

$q1 = "SELECT id,nama FROM master_permit  ";

$exe = mysqli_query($KONEKSI, $q1);
while ($row = mysqli_fetch_assoc($exe)){
    $data_permit[] = $row;
}

echo json_encode($data_permit);
mysqli_free_result($exe);
mysqli_close($KONEKSI);