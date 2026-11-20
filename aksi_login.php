<?php
require_once 'koneksi.php' ; 
session_start();

$username = $_POST['username'];
$pass = $_POST['password'];

$sql = "SELECT users.*, COALESCE(siswa.nama_siswa, guru.nama) AS nama, siswa.kelas
FROM users 
left join siswa on users.id = siswa.user_id
left join guru on users.id = guru.user_id
WHERE users.username = '$username'
";
$query = mysqli_query($koneksi, $sql);
$user = mysqli_fetch_assoc($query);

//mengecek apakah hasil quwery ada datanya atau tidak, dan mengecek juga apakah pw yg diinputkan oleh user sama dgn pw yg ada ditabel user
if ($user && password_verify($pass,$user['password'])) {
    // var_dump ($query);
    // exit;
   
    //membuat var session
    $_SESSION['id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['nama_siswa'] = $user['nama_siswa'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['kelas'] = $user['kelas'];
    $_SESSION['login'] = true;

    //beralih kehalaman index
    header("Location: index.php");
    exit;
} else {
    echo "<script>
    alert('Username atau password salah !');
    window.location='login.php';
    </script>";
}