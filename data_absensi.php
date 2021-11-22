<?php
include "koneksi.php";

$json = array();

$nik = $_POST['nik'];

$query ="SELECT (SELECT CONCAT (YEAR(CURDATE()),'-',(CASE WHEN MONTH(CURDATE()) < 10 THEN CONCAT('0',MONTH(CURDATE()) - 1) ELSE MONTH(CURDATE()) -1 END),'-','21')) as periode_begin , 
				(SELECT CONCAT (YEAR(CURDATE()),'-',(CASE WHEN MONTH(CURDATE()) < 10 THEN CONCAT('0',MONTH(CURDATE())) ELSE MONTH(CURDATE()) END),'-','20')) as periode_end ";
				


$result = mysqli_query($KONEKSI,$query);
$data   = mysqli_fetch_assoc($result);

$periode_begin = $data['periode_begin'];
$periode_end   = $data['periode_end'];

$query = "SELECT 
		        CASE
			    WHEN DATE_FORMAT(tgl,'%w') = 0 THEN 'Minggu'
			    WHEN DATE_FORMAT(tgl,'%w') = 1 THEN 'Senin'
			    WHEN DATE_FORMAT(tgl,'%w') = 2 THEN 'Selasa'
			    WHEN DATE_FORMAT(tgl,'%w') = 3 THEN 'Rabu'
			    WHEN DATE_FORMAT(tgl,'%w') = 4 THEN 'Kamis'
			    WHEN DATE_FORMAT(tgl,'%w') = 5 THEN 'Jumat'
			    WHEN DATE_FORMAT(tgl,'%w') = 6 THEN 'Sabtu'
			END AS hari,
		        nik,
		       DATE_FORMAT(tgl,'%d-%m-%Y') AS tgl,
		       (CASE WHEN is_hari_kerja ='1' OR flg_hadir ='HD' THEN `in`
			WHEN is_hari_kerja ='0' THEN ''
			ELSE '' END) AS `in`,
		       (CASE WHEN is_hari_kerja ='1' OR flg_hadir ='HD' THEN `out`
			WHEN is_hari_kerja ='0' THEN ''
			ELSE ''  END) AS `out`,
			flg_hadir
		FROM `absensi_detail_init_3_final`
		WHERE nik ='$nik' AND tgl >='$periode_begin' AND tgl <= '$periode_end'   ";	


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