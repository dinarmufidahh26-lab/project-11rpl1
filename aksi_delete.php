<?php
// FUNGSI: Menghubungkan script ke database
require_once "koneksi.php";

// FUNGSI: Memeriksa apakah ada pengiriman ID melalui URL dan memastikan nilainya berupa angka
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    // FUNGSI: Mengubah/memastikan data ID menjadi tipe angka bulat (integer) demi keamanan
    $id = intval($_GET['id']);

    // FUNGSI: Menyiapkan query aman (prepared statement) untuk mengecek peran/role dari ID user tersebut
    $stmt_user = mysqli_prepare($koneksi, "SELECT role FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt_user, "i", $id);
    mysqli_stmt_execute($stmt_user);
    
    // FUNGSI: Mengambil hasil pencarian role dari database
    $result = mysqli_stmt_get_result($stmt_user);
    $data = mysqli_fetch_assoc($result);

    // FUNGSI: Memeriksa apakah data user ditemukan di database
    if ($data) {
        // FUNGSI: Mengubah teks role menjadi huruf kecil semua agar mudah dicocokkan
        $role = strtolower($data['role']);

        // FUNGSI: Menentukan query hapus berdasarkan peran (Siswa, Guru, atau Admin)
        if ($role === 'siswa') {
            // FUNGSI: Menyiapkan perintah hapus data sekaligus dari tabel 'users' dan tabel 'siswa'
            $sql = "DELETE users, siswa FROM users 
                    LEFT JOIN siswa ON siswa.user_id = users.id 
                    WHERE users.id = ?";
        } elseif ($role === 'guru') {
            // FUNGSI: Menyiapkan perintah hapus data sekaligus dari tabel 'users' dan tabel 'guru'
            $sql = "DELETE users, guru FROM users 
                    LEFT JOIN guru ON guru.user_id = users.id 
                    WHERE users.id = ?";
        } else {
            // FUNGSI: Menyiapkan perintah hapus data dari tabel 'users' saja (untuk admin)
            $sql = "DELETE FROM users WHERE id = ?";
        }

        // FUNGSI: Menjalankan proses penghapusan data secara aman ke database
        $stmt_delete = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt_delete, "i", $id);

        // FUNGSI: Memberikan pesan pemberitahuan sesuai status berhasil atau gagalnya proses hapus
        if (mysqli_stmt_execute($stmt_delete)) {
            $pesan = "User berhasil dihapus!";
        } else {
            $pesan = "Gagal menghapus user.";
        }

    } else {
        // FUNGSI: Memberi pesan jika ID yang dicari tidak ada di database
        $pesan = "Data user tidak ditemukan.";
    }

} else {
    // FUNGSI: Mengarahkan kembali ke halaman tabel jika tidak ada ID yang dikirim
    header("Location: tabel.php");
    exit();
}

// FUNGSI: Menampilkan pesan alert (pop-up) lalu mengarahkan halaman kembali ke tabel.php
echo "<script>
        alert('$pesan'); 
        window.location.href = 'tabel.php';
      </script>";
exit();
?>