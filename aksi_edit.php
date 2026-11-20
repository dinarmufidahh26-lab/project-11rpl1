<?php
/** @var mysqli $koneksi */
// FUNGSI: Menghubungkan script ke koneksi database
require_once 'koneksi.php';

// FUNGSI: Mengambil data utama yang dikirim dari inputan form edit
$id       = $_POST['id_user'];
$username = $_POST['username'];
$pass     = $_POST['password'];
$role     = $_POST['role'];

// FUNGSI: Memproses pembaruan data akun pada tabel 'users'
// Mengecek apakah kolom password diisi atau sengaja dikosongkan oleh pengguna
if ($pass != "") {
    // FUNGSI: Jika password diisi, enkripsi password baru lalu perbarui username dan password di database
    $passHash = password_hash($pass, PASSWORD_DEFAULT);
    $sql      = "UPDATE users SET username='$username', password='$passHash' WHERE id = '$id'";
    $hasil    = mysqli_query($koneksi, $sql);
} else {
    // FUNGSI: Jika password dikosongkan, hanya perbarui username saja (password lama tetap)
    $sql   = "UPDATE users SET username='$username' WHERE id = '$id'";
    $hasil = mysqli_query($koneksi, $sql);
}

// FUNGSI: Mengambil data umum (Nama & Jenis Kelamin) jika data dikirim dari form Siswa atau Guru
$nama = $_POST['nama'] ?? '';
$jk   = $_POST['jenis_kelamin'] ?? '';

// FUNGSI: Menyiapkan nilai penanda default (bernilai benar) untuk mengecek status eksekusi query
$result = true; 

// FUNGSI: Memilih perintah pembaruan data ke tabel profil berdasarkan parameter 'aksi' di URL
if ($_GET['aksi'] == 'editsiswa') {

    // FUNGSI: Mengambil inputan khusus untuk profil siswa (NIS & Kelas)
    $nis   = $_POST['nis'];
    $kelas = $_POST['kelas'];

    // FUNGSI: Memperbarui data detail siswa di tabel 'siswa' berdasarkan ID user
    $sql    = "UPDATE siswa SET nis='$nis', nama_siswa='$nama', kelas='$kelas', jenis_kelamin='$jk' WHERE user_id = '$id'";
    $result = mysqli_query($koneksi, $sql);

} elseif ($_GET['aksi'] == 'editguru') {

    // FUNGSI: Mengambil inputan khusus untuk profil guru (NIP)
    $nip = $_POST['nip'];

    // FUNGSI: Memperbarui data detail guru di tabel 'guru' berdasarkan ID user
    $sql    = "UPDATE guru SET nip='$nip', nama='$nama', jenis_kelamin='$jk' WHERE user_id = '$id'";
    $result = mysqli_query($koneksi, $sql);

}

// FUNGSI: Memeriksa keberhasilan proses edit pada tabel 'users' dan tabel profil (siswa/guru)
if ($hasil && $result) {
    // FUNGSI: Menampilkan pesan pemberitahuan sukses lalu mengarahkan kembali ke halaman tabel.php
    echo "<script>alert('Data Berhasil diubah'); window.location.href='tabel.php';</script>";
} else {
    // FUNGSI: Menampilkan pesan pemberitahuan gagal lalu mengarahkan kembali ke halaman tabel.php
    echo "<script>alert('Data Gagal diubah'); window.location.href='table.php';</script>";
}
?>