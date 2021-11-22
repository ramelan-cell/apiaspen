<?php

include "koneksi.php";

$user_id = $_POST['user_id'];
$lat     = $_POST['lat'];
$lng 	 = $_POST['lng'];


$json = array();

$query ="SELECT COUNT(*) as jml FROM maps_area_user WHERE user_id ='$user_id' ";
$result = mysqli_query($KONEKSI,$query);
$data  = mysqli_fetch_assoc($result);
$jml = $data['jml'];

if($jml == 0){
	$query = "INSERT INTO maps_area_user (user_id,lat,`long`) VALUES ('$user_id','$lat','$lng') ";
	$result = mysqli_query($KONEKSI,$query);

	if($result){
		$json = array('value'=> 1,'message'=> 'simpan berhasil');
	}else{
		$json = array('value'=> 0,'message'=> 'simpan gagal');
	}
}else{
	$query = "UPDATE maps_area_user SET lat ='$lat' , `long` ='$lng'  WHERE user_id ='$user_id' ";

	$result = mysqli_query($KONEKSI,$query);

	if($result){
		$json = array('value'=> 1,'message'=> 'update berhasil');
	}else{
		$json = array('value'=> 0,'message'=> 'update gagal' ,'query'=>$query);
	}
}

echo json_encode($json);
mysqli_close($KONEKSI);