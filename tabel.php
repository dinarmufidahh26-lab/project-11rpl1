<?php
/** @var mysqli $koneksi */

// ==========================================
// 1. PANGGIL KONEKSI & AMBIL DATA
// ==========================================
// Panggil koneksi dulu biar variabel $koneksi siap dipakai jalanin query
require_once('koneksi.php');

// Ambil semua data user lalu gabung ke tabel siswa dan guru biar namanya kebawa
$sql = "SELECT users.*,
               COALESCE(siswa.nama_siswa, guru.nama) AS nama
        FROM users
        LEFT JOIN siswa ON users.id = siswa.user_id
        LEFT JOIN guru ON users.id = guru.user_id
        ORDER BY users.id DESC";

// Eksekusi query ke database
$hasil = mysqli_query($koneksi, $sql);

// Siapin array kosong buat nampung data dan pesan kalau kosong
$data = [];
$pesan_kosong = '';

// ==========================================
// 2. CEK DAN TAMPUNG DATA
// ==========================================
if ($hasil && mysqli_num_rows($hasil) > 0) {
    // Masukkan tiap baris hasil query ke array $data
    while ($row = mysqli_fetch_assoc($hasil)) {
        $data[] = $row;
    }
} else {
    // Tampilkan info kalau datanya memang kosong
    $pesan_kosong = "tidak ada data";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa & Pengguna</title>
    <!-- Panggil Bootstrap 5 CSS sekali aja di bagian head -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: #f7f6f2 !important; /* Warna cream lembut */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d; /* Warna biru navy */
        }
        
        /* Atur posisi dan z-index navbar biar gampang diklik */
        nav.navbar, .navbar {
            background-color: #1b365d !important;
            position: relative;
            z-index: 1050 !important;
        }
        .navbar .navbar-brand,
        .navbar .nav-link,
        .navbar .navbar-text {
            color: #ffffff !important;
        }
        /* Bikin isi dropdown muncul di atas elemen lain dan teksnya jelas */
        .navbar .dropdown-menu {
            z-index: 1060 !important;
        }
        .navbar .dropdown-menu .dropdown-item {
            color: #212529 !important;
        }

        /* Kotak putih pembungkus konten utama */
        .content-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 35px;
            border: 1px solid #e8e6e1;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        h1 {
            color: #1b365d !important;
            font-weight: 700;
        }

        .border-navy {
            border-color: #1b365d !important;
        }

        /* Gaya header dan isi tabel */
        .table thead {
            background-color: #1b365d !important;
            color: #ffffff !important;
        }
        .table thead th {
            background-color: #1b365d !important;
            color: #ffffff !important;
            border-color: #142845 !important;
        }
        .table td {
            vertical-align: middle;
        }

        /* Warna tombol edit dan hapus */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>
</head>
<body>

<!-- Panggil navbar di dalam body setelah CSS halaman siap -->
<?php require_once('navbar.php'); ?>

<!-- TAMPILAN HALAMAN UTAMA -->
<div class="container">

    <div class="content-container">
        <!-- Judul halaman -->
        <div class="mb-4">
            <h1 class="fw-normal display-6">Data Siswa & Pengguna</h1>
            <hr class="border-navy border-2 opacity-75">
        </div>

        <!-- Tampilkan pesan kalau data kosong -->
        <?php if ($pesan_kosong !== ''): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($pesan_kosong); ?></div>
        <?php endif; ?>

        <!-- Tabel daftar user -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th scope="col" style="width: 10%;">No</th>
                        <th scope="col">Username</th>
                        <th scope="col">Role</th>
                        <th scope="col">Nama</th>
                        <th scope="col" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Mulai nomor urut dari 1
                    $no = 1;

                    foreach ($data as $user) {
                        // Amankan nilai NULL biar htmlspecialchars() gak kena warning di PHP modern
                        $username = (string) ($user['username'] ?? '-');
                        $role     = (string) ($user['role'] ?? '-');
                        $nama     = ($user['nama'] !== null && $user['nama'] !== '') ? (string) $user['nama'] : '-';
                    ?>
                      <tr>
                       <!-- Nomor urut otomatis -->
                       <th scope="row" class="text-center"><?php echo $no++; ?></th>
                       
                       <!-- Data username, role, dan nama -->
                       <td><?php echo htmlspecialchars($username); ?></td>
                       <td>
                           <span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars($role); ?></span>
                       </td>
                       <td><?php echo htmlspecialchars($nama); ?></td>
                       
                       <!-- Tombol edit dan hapus data -->
                       <td class="text-center">
                         <a href="form_edit.php?id=<?php echo urlencode((string) $user['id']); ?>&role=<?php echo urlencode($role); ?>" class="btn btn-warning btn-sm">Edit</a>
                         <a href="aksi_delete.php?id=<?php echo urlencode((string) $user['id']); ?>&role=<?php echo urlencode($role); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</a>
                       </td>
                      </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Panggil footer di bawah kalau filenya ada -->
<?php 
if (file_exists("footer.php")) {
    require_once "footer.php";
}
?>

</body>
</html>