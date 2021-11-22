<?php

include "koneksi.php";

$json = array();

$nik = $_POST['nik'];

$query ="SELECT periode_awal,periode_akhir,DATEDIFF(periode_akhir,periode_awal) AS jml_hari 
FROM view_periode ";

$result = mysqli_query($KONEKSI,$query);
$data   = mysqli_fetch_assoc($result);

$periode_begin = $data['periode_awal'];
$periode_end   = $data['periode_akhir'];
$jml_hari      = $data['jml_hari'];



function namahari($tanggal){
    $tgl=substr($tanggal,8,2);
    $bln=substr($tanggal,5,2);
    $thn=substr($tanggal,0,4);
    $info=date('w', mktime(0,0,0,$bln,$tgl,$thn));
    switch($info){
        case '0': return "Minggu"; break;
        case '1': return "Senin"; break;
        case '2': return "Selasa"; break;
        case '3': return "Rabu"; break;
        case '4': return "Kamis"; break;
        case '5': return "Jumat"; break;
        case '6': return "Sabtu"; break;
    };
}

$query ="SELECT deskripsi FROM parameter WHERE id ='JAM_MASUK' ";
$result = mysqli_query($KONEKSI,$query);
$data =mysqli_fetch_assoc($result);
$jam_masuk  = $data['deskripsi'];


$query ="SELECT deskripsi FROM parameter WHERE id ='JAM_PULANG' ";
$result = mysqli_query($KONEKSI,$query);
$data =mysqli_fetch_assoc($result);
$jam_pulang  = $data['deskripsi'];


$query ="SELECT deskripsi FROM parameter WHERE id ='hari_kerja' ";
$result = mysqli_query($KONEKSI,$query);
$data =mysqli_fetch_assoc($result);
$nama_hari_array  = $data['deskripsi'];

for ($i=0; $i <= $jml_hari ; $i++) { 
	$tgl_absensi = date('Y-m-d', strtotime(" $i day", strtotime($periode_begin)));

	$query ="SELECT  DATE_FORMAT(waktu_absensi,'%H:%i' ) AS masuk
			FROM nik_import2
			WHERE nik ='$nik' AND DATE(waktu_absensi) = '$tgl_absensi' AND flag_masuk = '1'";
	$result = mysqli_query($KONEKSI,$query);
	$data =mysqli_fetch_assoc($result);
	$in  = isset($data['masuk']) ? $data['masuk']:"";

	$query ="SELECT   DATE_FORMAT(waktu_absensi,'%H:%i' ) AS keluar
			FROM nik_import2
			WHERE nik ='$nik' AND DATE(waktu_absensi) = '$tgl_absensi' AND flag_keluar = '1'";
	$result = mysqli_query($KONEKSI,$query);
	$data =mysqli_fetch_assoc($result);
	$out  = isset($data['keluar'])? $data['keluar']:"";

	$hari = namahari($tgl_absensi);
	$posisi=strpos($nama_hari_array,$hari);

	if ($posisi){
		$query ="SELECT count(*) as jml From hari_libur
			     where  tgl ='$tgl_absensi' ";
		$result = mysqli_query($KONEKSI,$query);
		$data =mysqli_fetch_assoc($result);
		$hari_libur  = $data['jml'];

		if($hari_libur == 0){
			$status_hari_kerja = 1;
			if(empty($in) && empty($out)){
				$flag_hadir ="AL";
			}else if(!empty($in) && empty($out)){
				$query ="SELECT count(*) as jml From permit
						where nik ='$nik' AND  tgl_mulai ='$tgl_absensi' AND tipe_permit ='8' AND `status` ='1' ";
				$result = mysqli_query($KONEKSI,$query);
				$data =mysqli_fetch_assoc($result);
				$ada_lupa_absen = $data['jml'];

				if($ada_lupa_absen > 0){
					$out = $jam_pulang;
					if($in > $jam_masuk){
						$flag_hadir ="T";
					}else if($out < $jam_pulang){
						$flag_hadir ="AN";
					}else{
						$flag_hadir ="HD";
					}	
				}else{
					$flag_hadir ="AN";
				}
			}else if(empty($in) && !empty($out)){
				$query ="SELECT count(*) as jml From permit
						where nik ='$nik' AND  tgl_mulai ='$tgl_absensi' AND tipe_permit ='7' AND `status` ='1' ";
				$result = mysqli_query($KONEKSI,$query);
				$data =mysqli_fetch_assoc($result);
				$ada_lupa_absen = $data['jml'];

				if($ada_lupa_absen > 0){
					$in =$jam_masuk;
					if($in > $jam_masuk){
						$flag_hadir ="T";
					}else if($out < $jam_pulang){
						$flag_hadir ="AN";
					}else{
						$flag_hadir ="HD";
					}	
				}else{
					$flag_hadir ="AN";
				}
				
			}else if(!empty($in) && !empty($out)){
				if($in > $jam_masuk){
					$flag_hadir ="T";
				}else if($out < $jam_pulang){
					$flag_hadir ="AN";
				}else{
					$flag_hadir ="HD";
				}	
			}else{
				$flag_hadir ="HD";
			}
		}else{
			$status_hari_kerja = 0;
			$flag_hadir ="OFF";
		}	
	}
	else {
		$status_hari_kerja = 0;
		$flag_hadir ="OFF";
	}

	$query ="SELECT count(*) as jml From absensi_detail_init_3_final
			 where nik ='$nik' and tgl ='$tgl_absensi' ";
	$result = mysqli_query($KONEKSI,$query);
	$data =mysqli_fetch_assoc($result);
	$jml  = $data['jml'];

	


	
	if($jml == 0){
		$query = "INSERT INTO absensi_detail_init_3_final (nik,shift,is_hari_kerja,tgl,jam_in_std,jam_out_std,`in`,`out`,flg_hadir)
				VALUES ('$nik','A','$status_hari_kerja','$tgl_absensi','$jam_masuk','$jam_pulang','$in','$out','$flag_hadir') ";

		$result = mysqli_query($KONEKSI,$query);
	}else{
		$query ="UPDATE absensi_detail_init_3_final SET `in`='$in' , `out`='$out' ,flg_hadir ='$flag_hadir'
				 where nik ='$nik' and tgl ='$tgl_absensi' ";
		$result = mysqli_query($KONEKSI,$query);

	}  
}

if($result){
	$json = array('value'=> 1,'message'=> 'Proses berhasil');
}else{
	$json = array('value'=> 0,'message'=> 'Proses gagal');
}


echo json_encode($json);
mysqli_close($KONEKSI);