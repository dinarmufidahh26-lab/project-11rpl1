<?php
// Memulai session jika belum aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/** @var mysqli $koneksi */
// Memanggil file koneksi database
require_once('koneksi.php');

// Menyiapkan dan menjalankan query SQL untuk mengambil data guru, akun, dan mapel
$sql = "SELECT users.username, users.role, guru.id, guru.nip, guru.nama AS nama, mapel.nama_mapel 
        FROM guru 
        JOIN users ON guru.user_id = users.id 
        LEFT JOIN mapel ON guru.id = mapel.guru_id
        WHERE users.role ='guru'";

$hasil = mysqli_query($koneksi, $sql);
$data = [];

// Menyimpan hasil query ke dalam array $data
if ($hasil && mysqli_num_rows($hasil) > 0) {
    while ($row = mysqli_fetch_assoc($hasil)) {
        $data[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Guru & Mapel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS Kustom untuk Tema Navy & Cream -->
    <style>
        body {
            background-color: #f7f6f2 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d;
        }

        /* Navbar agar aman dan bisa diklik */
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

        /* Desain Card & Tabel */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: #f5f2eb !important;
            border: 1px solid #e2ddd5 !important;
        }

        .card-body {
            background-color: #f5f2eb !important;
            border-radius: 12px;
        }

        .table-custom thead {
            background-color: #1b365d;
            color: #ffffff;
        }
        .table-custom th {
            background-color: #1b365d !important;
            color: #ffffff !important;
            border-color: #142845 !important;
        }
        .table-custom td {
            vertical-align: middle;
            background-color: #ffffff;
        }

        h1 {
            color: #1b365d;
            font-weight: 700;
        }

        .nama-guru-link {
            color: #1b365d;
            text-decoration: none;
            font-weight: 600;
        }
        .nama-guru-link:hover {
            color: #0d6efd;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Memanggil file navigasi (navbar) -->
    <?php require_once('navbar.php'); ?>

    <!-- Kontainer Utama Halaman -->
    <div class="container my-4 mb-5">

        <!-- Judul Halaman -->
        <div class="text-center mb-4">
            <h1 class="fw-bold fs-2 m-0">Daftar Guru</h1>
        </div>

        <!-- Card Pembungkus Tabel Data -->
        <div class="card shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0 table-custom">
                        <thead class="text-center">
                            <tr>
                                <th scope="col" style="width: 8%;">No</th>
                                <th scope="col" style="width: 20%;">Username</th>
                                <th scope="col" style="width: 15%;">Role</th>
                                <th scope="col" style="width: 25%;">Nama Guru</th>
                                <th scope="col" style="width: 32%;">Mapel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Mengecek apakah array data guru tersedia
                            if (!empty($data)) {
                                $no = 1; // Inisialisasi nomor urut
                                foreach ($data as $row) {
                            ?>
                                <tr>
                                   <!-- Menampilkan nomor urut -->
                                   <th scope="row" class="text-center"><?php echo $no++; ?></th>
                                   <!-- Menampilkan username guru -->
                                   <td class="text-center"><?php echo htmlspecialchars($row['username']); ?></td>
                                   <!-- Menampilkan role akun -->
                                   <td class="text-center"><span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['role']); ?></span></td>
                                   <td>
                                       <!-- Link menuju detail profil guru -->
                                       <a href="detail_guru.php?id=<?php echo $row['id']; ?>" class="nama-guru-link">
                                           <?php echo htmlspecialchars($row['nama']); ?>
                                       </a>
                                   </td>
                                   <td class="text-center">
                                       <!-- Mengecek apakah ada data mata pelajaran -->
                                       <?php if (!empty($row['nama_mapel'])): ?>
                                           <span class="badge bg-secondary"><?php echo htmlspecialchars($row['nama_mapel']); ?></span>
                                       <?php else: ?>
                                           <span class="text-muted">-</span>
                                       <?php endif; ?>
                                   </td>
                                </tr>
                            <?php 
                                } 
                            } else {
                            ?>
                                <!-- Pesan jika data kosong -->
                                <tr>
                                  <td colspan="5" class="text-center text-muted py-4">Tidak ada data relasi guru dan mapel.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Memanggil bagian bawah web (footer) jika ada -->
    <?php 
    if (file_exists("footer.php")) {
        require_once "footer.php"; 
    }
    ?>
</html>