<?php
session_start();

//jika belum login
if(!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

//ambil role user yang akan login
$role = $_SESSION['role'] ?? '';

//cek role user kemudian arahkan ke halaman sesuai dengan role yg login
switch ($role) {
 case 'admin';
    header("Location: tabel.php");
    exit;

 case 'guru';
    header("Location: daftar_guru.php");
    exit;   

 case 'siswa';
    header("Location: daftar_siswa.php");
    exit; 

 default;
    //menghapus sessian yg ada
    session_destroy();
    header("Location: login.php");
    exit;
}