<?php
include "koneksi.php";

$user_id = $_POST['user_id'];

$query="SELECT COUNT(*) as jml FROM maps_area_user WHERE user_id ='$user_id' ";
$result = mysqli_query($KONEKSI,$query);
$data = mysqli_fetch_assoc($result);
$jml  = $data['jml'];

if($jml == 0){
	$isValid = 0;
	$isPesan = "Setting lokasi kantor anda terlebih dahulu !!!";
}else{
	$isValid = 1;
	$isPesan = "Lanjut proses berikutnya";
}


$json = array('value'=> $isValid,'message'=> $isPesan);


echo json_encode($json);
mysqli_close($KONEKSI);

?>