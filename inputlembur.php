<?php

include "koneksi.php";

date_default_timezone_set('Asia/Jakarta');

$nik  = $_POST['nik'];
$mulai = $_POST['mulai'];
$tgl = $_POST['tgl'];
$selesai = $_POST['selesai'];
$selama = $_POST['selama'];
$keterangan = $_POST['keterangan'];

$query = "SELECT * FROM parameter WHERE id ='JAM_PULANG' ";
$result = mysqli_query($KONEKSI,$query);
$data = mysqli_fetch_assoc($result);
$jam_pulang = $data['deskripsi'];


$query = "SELECT COUNT(*) as jml FROM lembur WHERE tanggal ='$tgl' ";
$result = mysqli_query($KONEKSI,$query);
$data = mysqli_fetch_assoc($result);
$count = $data['jml'];

if($count > 0){
	$json = array('value'=> 0,'message'=> 'Pengajuan lembur di tanggal tersebut sudah ada ');
}else if(date('H:i',strtotime($mulai)) < date('H:i',strtotime($jam_pulang))){

    $json = array('value'=> 0,'message'=> 'Jam mulai lembur masih dalam waktu jam kerja');

}else{
	$query = "INSERT INTO lembur (nik,tanggal,jam_awal,jam_akhir,selama,keterangan,`status`)
          VALUES ('$nik','$tgl','$mulai','$selesai','$selama','$keterangan','0') ";

	$result = mysqli_query($KONEKSI, $query);
	if($result){
		$json = array('value'=> 1,'message'=> 'Proses berhasil' ,'query'=> $query);

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

        $query ="SELECT id FROM lembur  where nik ='$nik' order by id desc limit 1 ";
        $result = mysqli_query($KONEKSI, $query);
        $data   = mysqli_fetch_assoc($result);
        $id_lembur= $data['id'];


        // Notifikasi ke atasan
            $content = array(
                "en" => "$nama Mengajukan Lembur ",
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
                    VALUES ('$id_lembur','$user_id_induk','$user_id',now(),'LEMBUR','PENGAJUAN LEMBUR','$nama Mengajukan Lembur','$fcm_token') ";

		$result = mysqli_query($KONEKSI, $query);
		
	}else{
		$json = array('value'=> 0,'message'=> 'Proses gagal' ,'query'=> $query);
	}
}




echo json_encode($json);
mysqli_close($KONEKSI);