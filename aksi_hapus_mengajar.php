<?php
// Nyalain session dulu kalau belum aktif biar $_SESSION bisa dipake
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/** @var mysqli $koneksi */
// Panggil file koneksi biar nyambung ke database di folder tugasphp
require_once('koneksi.php');

// Ambil id data mengajar dari URL (?id=...), pastikan bentuknya angka bulat
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Cek dulu id-nya valid gak, kalau kosong atau 0 mending balikin ke daftar_mengajar.php
if ($id <= 0) {
    echo "<script>
            alert('ID data mengajar tidak valid!');
            window.location = 'daftar_mengajar.php';
          </script>";
    exit;
}

// Cek dulu datanya beneran ada gak di tabel mengajar sebelum dihapus
$cek_data = mysqli_query($koneksi, "SELECT id FROM mengajar WHERE id = $id LIMIT 1");
if (!$cek_data) {
    die("Gagal ngecek data: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($cek_data) === 0) {
    echo "<script>
            alert('Data mengajar tidak ditemukan di database!');
            window.location = 'daftar_mengajar.php';
          </script>";
    exit;
}

// Eksekusi hapus baris di tabel mengajar sesuai id-nya aja (pake LIMIT 1 biar aman)
$hapus = mysqli_query($koneksi, "DELETE FROM mengajar WHERE id = $id LIMIT 1");

// Kasih notif sesuai hasil hapus terus balikin ke daftar_mengajar.php
if ($hapus) {
    echo "<script>
            alert('Data mengajar berhasil dihapus!');
            window.location = 'daftar_mengajar.php';
          </script>";
    exit;
} else {
    echo "<script>
            alert('Gagal menghapus data: " . addslashes(mysqli_error($koneksi)) . "');
            window.location = 'daftar_mengajar.php';
          </script>";
    exit;
}
?>