<?php 
$username ="root";
$password ="";
$hostname ="localhost";
$database ="db_tugas";

$koneksi = mysqli_connect($hostname, $username, $password, $database);

if (!$koneksi) {
    die("koneksi gagal:". mysql_connect_error());
}

?>