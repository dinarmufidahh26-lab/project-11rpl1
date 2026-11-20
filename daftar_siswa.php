<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/** @var mysqli $koneksi */
require_once('koneksi.php');

// Query SQL dengan filter kelas 11 RPL 1 dan role siswa
$sql = "SELECT users.username, users.role, siswa.nama_siswa, siswa.kelas 
        FROM siswa 
        JOIN users ON siswa.user_id = users.id 
        WHERE users.role = 'siswa' AND siswa.kelas = 'XI RPL 1'";

$hasil = mysqli_query($koneksi, $sql);
$data = [];

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
    <title>Data Siswa</title>
    <!-- Bootstrap CSS yang Stabil -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS Kustom Tema Biru Navy & Cream Lebih Dominan -->
    <style>
        body {
            background-color: #f7f6f2 !important; /* Cream soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d;
        }

        /* Navbar kustom agar z-index aman & bisa diklik */
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

        /* Desain Card & Tabel dengan nuansa Cream Hangat */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: #f5f2eb !important; /* Cream card background */
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
            background-color: #ffffff; /* Baris tabel tetap kontras putih bersih */
        }
        
        .table-custom tr {
            background-color: #ffffff;
        }

        /* Judul Utama */
        h1 {
            color: #1b365d;
            font-weight: 700;
        }
        .border-navy {
            border-color: #1b365d !important;
        }

        /* Pagination & Tombol Aktif */
        .page-item.active .page-link {
            background-color: #1b365d;
            border-color: #1b365d;
        }
        
        .form-select, .form-control {
            background-color: #ffffff !important;
            border-color: #dcd7cd !important;
        }
    </style>
</head>
<body>

    <!-- Memanggil Navbar yang sudah dipisah -->
    <?php require_once('navbar.php'); ?>

    <!-- TAMPILAN HALAMAN UTAMA -->
    <div class="container my-4 mb-5">

        <!-- Header & Judul Halaman -->
        <div class="text-center mb-4">
            <h1 class="fw-bold fs-2 m-0">Daftar Siswa Kelas 11 RPL 1</h1>
        </div>

        <!-- Fitur Tampilkan & Kolom Pencarian -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <span>Tampilkan</span>
                <select class="form-select form-select-sm" style="width: 80px;">
                    <option value="4">4</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                </select>
                <span>data per halaman</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label for="cari" class="form-label m-0">Cari:</label>
                <input type="text" id="cari" class="form-control form-control-sm" style="width: 200px;" placeholder="Cari siswa...">
            </div>
        </div>

        <!-- Card Pembungkus Tabel -->
        <div class="card shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0 table-custom">
                        <thead class="text-center">
                            <tr>
                                <th scope="col" style="width: 10%;">No</th>
                                <th scope="col" style="width: 25%;">Username</th>
                                <th scope="col" style="width: 20%;">Role</th>
                                <th scope="col" style="width: 25%;">Nama</th>
                                <th scope="col" style="width: 20%;">Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($data)) {
                                $no = 1;
                                foreach ($data as $siswa) {
                            ?>
                                <tr>
                                   <th scope="row" class="text-center"><?php echo $no++; ?></th>
                                   <td class="text-center"><?php echo htmlspecialchars($siswa['username']); ?></td>
                                   <td class="text-center"><span class="badge bg-info text-dark"><?php echo htmlspecialchars($siswa['role']); ?></span></td>
                                   <td class="fw-semibold"><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                                   <td class="text-center"><span class="badge bg-secondary"><?php echo htmlspecialchars($siswa['kelas']); ?></span></td>
                                </tr>
                            <?php 
                                } 
                            } else {
                            ?>
                                <tr>
                                  <td colspan="5" class="text-center text-muted py-4">Tidak ada data siswa untuk kelas 11 RPL 1.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bagian Bawah Tabel (Pagination Info) -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Menampilkan halaman 1 dari 1</span>
            <nav>
                <ul class="pagination pagination-sm m-0">
                    <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item disabled"><a class="page-link" href="#">Selanjutnya</a></li>
                </ul>
            </nav>
        </div>

    </div>

    <!-- Panggil bagian bawah web (footer) -->
    <?php 
    if (file_exists("footer.php")) {
        require_once "footer.php"; 
    }
    ?>
</body>
</html>