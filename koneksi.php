
<?php

$KONEKSI = mysqli_connect('localhost:3306','root','');
$DATABASE= mysqli_select_db($KONEKSI,'hc');

if(!$KONEKSI){
    die("Koneksi Gagal : ". mysqli_connect_error());
}

?>