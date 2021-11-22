<?php
include "koneksi.php";

$nik = $_POST['nik'];

$tgl_mulai = $_POST['tgl_mulai'];
$tgl_akhir = $_POST['tgl_akhir'];
$tipe = $_POST['tipe_permit'];
$keterangan = $_POST['keterangan'];
$today = date('Y-m-d');

$query ="SELECT nama FROM master_permit WHERE id ='$tipe' ";
$result = mysqli_query($KONEKSI, $query);
$data = mysqli_fetch_assoc($result);
$permit_nama = $data['nama'];


if(!empty($_FILES)) {

	$fileSize    = $_FILES["gambar"]["size"];

    if ($fileSize > 1024 * 1000 * 10){
    	$isValid = 0;
		$isPesan = "File size shoud be less than 5 MB !!!!";
		$json = array('value'=> $isValid,'message'=> $isPesan);
		echo json_encode($json);
		die();
    } 


    $dir_base = "permit/";
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
    $newfilename = $today.'-'.$nik.$file_ext;


    if (in_array($fileActualExt, $allowed)) {

        if(file_exists($dir_file.$newfilename)){
            unlink($dir_file.$newfilename);
        }

        if(move_uploaded_file($fileTmpName,$dir_file.$newfilename)) {

            $query ="INSERT INTO permit (nik,tanggal,tipe_permit,tgl_mulai,tgl_akhir,keterangan,`file`,`status`)
                     VALUES ('$nik','$today','$tipe','$tgl_mulai','$tgl_akhir','$keterangan','$newfilename','0') ";

			$result = mysqli_query($KONEKSI, $query);

			if(!$result){
				$isValid = 0;
            	$isPesan = "Data gagal tersimpan, error : ".mysqli_error($KONEKSI);
			}else{
				$isValid = 1;
                $isPesan = " Proses input Sukses !!!";
                
                $query ="SELECT user_id_induk ,nama ,`user_id` FROM user  where nik ='$nik' ";
                $result = mysqli_query($KONEKSI, $query);
                $data   = mysqli_fetch_assoc($result);
                $user_id_induk = $data['user_id_induk'];
                $nama = $data['nama'];
                $user_id = $data['user_id'];

                $query ="SELECT fcm_token  FROM user  where user_id ='$user_id_induk' ";
                $result = mysqli_query($KONEKSI, $query);
                $data   = mysqli_fetch_assoc($result);
                $fcm_token = $data['fcm_token'];

                $query ="SELECT id FROM permit  where nik ='$nik' order by id desc limit 1 ";
                $result = mysqli_query($KONEKSI, $query);
                $data   = mysqli_fetch_assoc($result);
                $id_klaim = $data['id'];

                $query ="SELECT nama FROM master_permit  where id ='$tipe' ";
                $result = mysqli_query($KONEKSI, $query);
                $data   = mysqli_fetch_assoc($result);
                $nama_tipe = $data['nama'];



                // Notifikasi ke atasan
                    $content = array(
                        "en" => "$nama Mengajukan Permit $nama_tipe ",
                    );
                
                    $fields = array(
                        'app_id' => "9c1a698f-3f93-439f-b2ed-890624451631",
                        'include_player_ids' => array("$fcm_token"),
                        'data' => array("foo" => "bar"),
                        'large_icon' =>"ic_launcher_round.png",
                        'contents' => $content
                    );
                
                    $fields = json_encode($fields);
                
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                               'Authorization: Basic NzliZWQ4YWUtNTM0NS00NGMzLThjMDctYTFhNGE4NDBhNTg2'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    
                
                    $response = curl_exec($ch);
                    curl_close($ch);
                // }

                $query ="INSERT INTO sys_pesan (id_flg_otorisasi,`user_id`,user_id_request,waktu,`subject`,header,pesan,fcm_token)
                         VALUES ('$id_klaim','$user_id_induk','$user_id',now(),'PERMIT','$permit_nama','$nama Mengajukan Permit $nama_tipe','$fcm_token') ";

                $result = mysqli_query($KONEKSI, $query);
                
			}
		}else{
            $isValid = 0;
            $isPesan = 'File gagal terkirim, Silahkan coba kembali !!!';
        }
                
    }

   
}else{

    $query = "INSERT INTO permit (nik,tanggal,tipe_permit,tgl_mulai,tgl_akhir,keterangan,`status`) 
     VALUES ('$nik','$today','$tipe','$tgl_mulai','$tgl_akhir','$keterangan','0') ";

    $result = mysqli_query($KONEKSI, $query);

    if(!$result){
        $isValid = 0;
        $isPesan = "Data gagal tersimpan, error : ".mysqli_error($KONEKSI);
    }else{
        $isValid = 1;
        $isPesan = " Proses input Sukses !!!";
        
        $query ="SELECT user_id_induk ,nama ,`user_id` FROM user  where nik ='$nik' ";
        $result = mysqli_query($KONEKSI, $query);
        $data   = mysqli_fetch_assoc($result);
        $user_id_induk = $data['user_id_induk'];
        $nama = $data['nama'];
        $user_id = $data['user_id'];

        $query ="SELECT fcm_token  FROM user  where user_id ='$user_id_induk' ";
        $result = mysqli_query($KONEKSI, $query);
        $data   = mysqli_fetch_assoc($result);
        $fcm_token = $data['fcm_token'];

        $query ="SELECT id FROM permit  where nik ='$nik' order by id desc limit 1 ";
        $result = mysqli_query($KONEKSI, $query);
        $data   = mysqli_fetch_assoc($result);
        $id_klaim = $data['id'];

        $query ="SELECT nama FROM master_permit  where id ='$tipe' ";
        $result = mysqli_query($KONEKSI, $query);
        $data   = mysqli_fetch_assoc($result);
        $nama_tipe = $data['nama'];


        // Notifikasi ke atasan
            $content = array(
                "en" => "$nama Mengajukan Permit $nama_tipe ",
            );
        
            $fields = array(
                'app_id' => "9c1a698f-3f93-439f-b2ed-890624451631",
                'include_player_ids' => array("$fcm_token"),
                'data' => array("foo" => "bar"),
                'large_icon' =>"ic_launcher_round.png",
                'contents' => $content
            );
        
            $fields = json_encode($fields);
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                        'Authorization: Basic NzliZWQ4YWUtNTM0NS00NGMzLThjMDctYTFhNGE4NDBhNTg2'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    
        
            $response = curl_exec($ch);
            curl_close($ch);
        // }

        $query ="INSERT INTO sys_pesan (id_flg_otorisasi,`user_id`,user_id_request,waktu,`subject`,header,pesan,fcm_token)
                    VALUES ('$id_klaim','$user_id_induk','$user_id',now(),'PERMIT','$permit_nama','$nama Mengajukan permit $nama_tipe','$fcm_token') ";

        $result = mysqli_query($KONEKSI, $query);
        
    }
}


$json = array('value'=> $isValid,'message'=> $isPesan,'fcm_token'=>$fcm_token,'query'=>$query);


echo json_encode($json);
mysqli_close($KONEKSI);


?>