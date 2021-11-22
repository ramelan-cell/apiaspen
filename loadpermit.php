

<?php
include "koneksi.php";

$nik = $_GET['nik'];
$limit = $_GET['limit'];
$page = $_GET['page']*$limit;


$q1 = "SELECT permit.*,master_permit.nama AS tipe_permit,user.nama
FROM permit LEFT JOIN master_permit 
ON permit.tipe_permit = master_permit.id
LEFT JOIN `user` ON permit.nik = `user`.nik 
WHERE  `status` = '0' AND (permit.nik ='$nik'  OR 
permit.nik IN (SELECT nik FROM `user` 
             WHERE user_id_induk IN (SELECT user_id FROM `user` WHERE nik ='$nik') ))   ORDER BY id Desc LIMIT $limit OFFSET $page   ";

$exe = mysqli_query($KONEKSI, $q1);
$count = mysqli_num_rows($exe);

if($count > 0){
    while($row=mysqli_fetch_assoc($exe)){
        $json['results'][] = $row;
    }
}else{
    $json['results'] = array();
}

echo json_encode($json);
mysqli_close($KONEKSI);
