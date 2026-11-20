<?php
// ==========================================
// 1. KONEKSI KE DATABASE
// ==========================================
// Memanggil file koneksi agar terhubung ke database MySQL
require_once('koneksi.php');

// ==========================================
// 2. PROSES HAPUS DATA (JIKA DIPANGGIL VIA GET ?hapus=ID)
// ==========================================
if (isset($_GET['hapus']) && $_GET['hapus'] !== '') {
    // Mengubah nilai hapus menjadi integer agar aman
    $id_hapus = (int) $_GET['hapus'];

    if ($id_hapus > 0) {
        // Menghapus baris data di tabel mengajar berdasarkan id
        $sql_hapus = "DELETE FROM mengajar WHERE id = $id_hapus";
        $res_hapus = mysqli_query($koneksi, $sql_hapus);

        if ($res_hapus) {
            header("Location: daftar_mengajar.php");
            exit();
        } else {
            echo "Gagal menghapus data: " . mysqli_error($koneksi);
            echo "<br><a href='form_tambah_mengajar.php'>Kembali ke Form</a>";
            exit();
        }
    }
}

// ==========================================
// 3. PROSES DATA DARI FORM TAMBAH
// ==========================================
// Mengecek apakah tombol simpan sudah ditekan
if (isset($_POST['simpan'])) {

    // Mencari ID terbesar saat ini lalu ditambah 1 agar kolom 'id' selalu terisi dan unik
    $q_max   = mysqli_query($koneksi, "SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM mengajar");
    $d_max   = mysqli_fetch_assoc($q_max);
    $next_id = (int) ($d_max['next_id'] ?? 1);

    // Menangkap data dari form dengan pengecekan aman (agar tidak Undefined array key)
    // Jika siswa_id tidak dikirim dari form, isi sementara dengan angka 1 (pastikan ID siswa 1 ada di tabel siswa)
    $siswa_id = isset($_POST['siswa_id']) && $_POST['siswa_id'] !== '' ? (int) $_POST['siswa_id'] : 1;

    // Menangkap guru_id dari dropdown form
    $guru_id  = isset($_POST['guru_id']) && $_POST['guru_id'] !== '' ? (int) $_POST['guru_id'] : 0;

    // Jika mapel_id tidak dikirim dari form, isi sementara dengan angka 1 (pastikan ID mapel 1 ada di tabel mapel)
    $mapel_id = isset($_POST['mapel_id']) && $_POST['mapel_id'] !== '' ? (int) $_POST['mapel_id'] : 1;

    // ==========================================
    // 4. SIMPAN DATA KE TABEL MENGAJAR
    // ==========================================
    // Memasukkan id, siswa_id, guru_id, dan mapel_id ke tabel mengajar
    $sql = "INSERT INTO mengajar (id, siswa_id, guru_id, mapel_id) VALUES ('$next_id', '$siswa_id', '$guru_id', '$mapel_id')";

    // Menjalankan query ke database
    $query = mysqli_query($koneksi, $sql);

    // ==========================================
    // 5. CEK HASIL & PINDAH HALAMAN
    // ==========================================
    if ($query) {
        // Jika berhasil, kembali ke halaman daftar mengajar
        header("Location: daftar_mengajar.php");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error MySQL
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
        echo "<br><a href='form_tambah_mengajar.php'>Kembali ke Form</a>";
    }

} else {
    // Jika file dibuka langsung tanpa klik tombol simpan, arahkan ke form
    header("Location: form_tambah_mengajar.php");
    exit();
}
?>