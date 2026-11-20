<?php
/** @var mysqli $koneksi */
// Buat sambungin file ini ke database lewat koneksi.php
require_once 'koneksi.php';

// Matikan exception fatal error agar script bisa ditangkap manual
mysqli_report(MYSQLI_REPORT_OFF);

// Buat ngambil data dari form tambah mata pelajaran
$kode_mapel = $_POST['kode_mapel'] ?? '';
$nama_mapel = $_POST['nama_mapel'] ?? '';
$guru_id    = $_POST['guru_id'] ?? '';

// 1. Cek dulu apakah kode_mapel sudah terdaftar di database
$cek_kode = mysqli_query($koneksi, "SELECT kode_mapel FROM mapel WHERE kode_mapel = '$kode_mapel'");

if (mysqli_num_rows($cek_kode) > 0) {
    // Jika kode sudah ada, tampilkan notifikasi dan kembalikan user ke form
    echo "<script>
        alert('Kode mata pelajaran ($kode_mapel) sudah digunakan! Gunakan kode lain.');
        window.history.back();
    </script>";
    exit;
}

// Cek nilai guru_id: kalau kosong/tidak dipilih, diisi NULL
$guru_id_sql = (!empty($guru_id)) ? "'$guru_id'" : "NULL";

// Buat nyimpen data mata pelajaran ke tabel 'mapel'
$sql_mapel = "INSERT INTO mapel (kode_mapel, nama_mapel, guru_id) 
              VALUES ('$kode_mapel', '$nama_mapel', $guru_id_sql)";

$query_mapel = mysqli_query($koneksi, $sql_mapel);

// Cek apakah simpan data ke tabel 'mapel' berhasil
if ($query_mapel) {
    echo "<script>
        alert('Data berhasil disimpan!');
        window.location.href = 'tabel_mapel.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menyimpan data mapel: " . addslashes(mysqli_error($koneksi)) . "');
        window.history.back();
    </script>";
}
?>