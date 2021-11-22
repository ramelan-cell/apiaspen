<?php

include "koneksi.php";

date_default_timezone_set('Asia/Jakarta');
$waktu= date('Y-m-d H:i:s');
$flag = $_POST['flag'];
$nik  = $_POST['nik'];

$size = 240;

$waktu_absen = date('Y-m-d',strtotime($waktu));

if($flag =="MASUK"){
	$flag_absen = "flag_masuk";
}elseif($flag =="MULAI ISTIRAHAT"){
	$flag_absen = "flag_mulai_break";
}elseif($flag =="SELESAI ISTIRAHAT"){
	$flag_absen = "flag_selesai_break";
}else{
    $flag_absen = "flag_keluar";
}




if(!empty($_FILES)) {

	$fileSize    = $_FILES["gambar"]["size"];

    // if ($fileSize > 1024 * 1000){
    // 	$isValid = 0;
	// 	$isPesan = "File size shoud be less than 5 MB !!!!";
	// 	$json = array('value'=> $isValid,'message'=> $isPesan);
	// 	echo json_encode($json);
	// 	die();
    // } 


    $dir_base = "upload_absensi/";
    $dir_file = $dir_base;
    if(!file_exists($dir_file)){
        mkdir("$dir_file");
        chmod("$dir_file", 0777);
    }

    $fileName = isset($_FILES['gambar']['name']) ? $_FILES['gambar']['name']:"";

    $fileTmpName = isset($_FILES['gambar']['tmp_name']) ? $_FILES['gambar']['tmp_name']:"";
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $allowed = array('jpg','jpeg');
    $file_ext = substr($fileName, strripos($fileName, '.'));
    $file_basename= substr($fileName, 0, strripos($fileName, '.'));
    $newfilename = $nik.$waktu_absen.$flag_absen.$file_ext;


    if (in_array($fileActualExt, $allowed)) {

        if(file_exists($dir_file.$newfilename)){
            unlink($dir_file.$newfilename);
        }

        if(move_uploaded_file($fileTmpName,$dir_file.$newfilename)) {

            $query ="INSERT INTO absensi_gambar (nik,gambar,waktu_absensi,$flag_absen)
                     VALUES ('$nik','$newfilename','$waktu','1') ";

			$result = mysqli_query($KONEKSI, $query);

			if(!$result){
				$isValid = '0';
            	$isPesan = "Data gagal tersimpan, error : ".mysqli_error($KONEKSI);
			}else{
				$isValid = '1';
            	$isPesan = " Proses input Sukses !!!";
			}
		}else{
            $isValid = '0';
            $isPesan = 'File gagal terkirim, Silahkan coba kembali !!!';
            $json = array('value'=> $isValid,'message'=> $isPesan);
            echo json_encode($json);
            die();
        }
                
    }

    $query ="SELECT COUNT(*) as jml FROM nik_import2
         WHERE $flag_absen ='1' AND date(waktu_absensi) = '$waktu_absen' AND nik ='$nik'  ";

    $result = mysqli_query($KONEKSI,$query);
    $data   = mysqli_fetch_assoc($result);
    $jml    = $data['jml'];

    if($jml > 0){
        $json = array('value'=> '0','message'=> 'Anda sudah melakukan absensi sebelumnya !!!');
        echo json_encode($json);
        die();
    }


    $json = array();

    $query="INSERT INTO nik_import2 (nik,waktu_absensi,log_ke,$flag_absen,waktu_kirim)
            VALUES ('$nik','$waktu','1','1','$waktu') ";

    $result = mysqli_query($KONEKSI,$query);

    if($result){
        $json = array('value'=> '1','message'=> 'Proses berhasil' ,'query'=> $query,'time'=>$waktu);
    }else{
        $json = array('value'=> '0','message'=> 'Proses gagal' ,'query'=> $query);
    }


   
}

echo json_encode($json);
mysqli_close($KONEKSI);