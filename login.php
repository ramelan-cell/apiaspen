<?php

include "koneksi.php";

$username=trim($_POST['username']);
$password=$_POST['password'];

$myusername=mysqli_real_escape_string($KONEKSI, $username);
$mypassword= mysqli_real_escape_string($KONEKSI, $password);

$myusername = str_replace(" ","",$myusername);

$json = array();

$query="SELECT a.* , b.nama AS jabatan 
        FROM USER a LEFT JOIN jabatan b
        ON a.jabatan_id = b.jabatan_id
        where a.username ='$myusername' and a.password=md5('$mypassword')  ";


$result = mysqli_query($KONEKSI,$query);
$data =mysqli_fetch_assoc($result);
$row = mysqli_num_rows($result);

if($row > 0){
        $user_id = $data['user_id'];
        $nama = $data['nama'];
        $username = $data['username'];
        $nik = $data['nik'];
        $jabatan = $data['jabatan'];
        $foto = $data['foto'];

        
        // $urlfoto = 'https://mediasaranainovasi.co.id/api/absensi/profile/'.$foto;
        
        // if(file_exists($urlfoto)){
        //     $fotocek = 'fileada';
        //     $foto =  'https://mediasaranainovasi.co.id/api/absensi/profile/'.$foto;
        // }else{
        //     $fotocek = 'tidak fileada';
        //     $foto =  'https://kreditmandiri.co.id/api_test/absensi/profile/photo.jpg';
        // }


        // function URLIsValid($URL)
        // {
        // $exists = true;
        // $file_headers = @get_headers($URL);
        // $InvalidHeaders = array('404', '403', '500');
        // foreach($InvalidHeaders as $HeaderVal)
        // {
        //         if(strstr($file_headers[0], $HeaderVal))
        //         {
        //                 $exists = false;
        //                 break;
        //         }
        // }
        // return $exists;
        // }

        // $hasil = URLIsValid($urlfoto);

        if(empty($foto)){
                $foto =  'https://kreditmandiri.co.id/api_test/absensi/profile/photo.jpg';
        }else{
                $foto =  'https://mediasaranainovasi.co.id/api/absensi/profile/'.$foto;
        }

        $json = array('value'=> 1,'message'=> 'login berhasil','nama'=>$nama,
		         'user_id'=>$user_id,'user'=>$username,'jabatan'=>$jabatan,'nik'=>$nik,'foto'=>$foto);
}else{
        $json = array('value'=> 0,'message'=> 'login gagal','query'=>$query);
}


echo json_encode($json);
mysqli_close($KONEKSI);