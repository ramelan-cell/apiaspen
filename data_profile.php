<?php
include "koneksi.php";

$json = array();

$nik = $_POST['nik'];

$query = "SELECT  * from karyawan WHERE nik ='$nik'   ";

$execute = mysqli_query($KONEKSI, $query);
$count = mysqli_num_rows($execute);

if($count > 0){
    while($row=mysqli_fetch_assoc($execute)){
        $json[] = $row;
    }
}else{
    $json = array();
}

echo json_encode($json);
mysqli_close($KONEKSI);

?>