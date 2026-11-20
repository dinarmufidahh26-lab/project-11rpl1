<?php
// Panggil koneksi database
require_once "koneksi.php";

// Tangkap parameter dari URL
$id       = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$id_siswa = isset($_GET['id_siswa']) ? (int) $_GET['id_siswa'] : 0;
$mapel_id = isset($_GET['mapel_id']) ? (int) $_GET['mapel_id'] : 0;

// 1. Paling aman: hapus berdasarkan primary key 'id' milik baris nilai_rapor
if ($id > 0) {
    $hapus = mysqli_query($koneksi, "DELETE FROM nilai_rapor WHERE id = $id LIMIT 1");

    if (!$hapus) {
        die("Query hapus gagal: " . mysqli_error($koneksi));
    }

    header("Location: nilai_siswa_guru.php");
    exit();

// 2. Jika masih memakai id_siswa dan mapel_id, batasi LIMIT 1 agar tidak menghapus banyak baris
} elseif ($id_siswa > 0 && $mapel_id > 0) {
    $hapus = mysqli_query(
        $koneksi,
        "DELETE FROM nilai_rapor WHERE siswa_id = $id_siswa AND mapel_id = $mapel_id LIMIT 1"
    );

    if (!$hapus) {
        die("Query hapus gagal: " . mysqli_error($koneksi));
    }

    header("Location: nilai_siswa_guru.php");
    exit();

} else {
    die("Parameter hapus tidak valid.");
}
?>