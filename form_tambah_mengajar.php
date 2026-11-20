<?php
// ==========================================
// 1. NYALAIN SESSION & KONEKSI DATABASE
// ==========================================
// Aktifin session dulu kalau belum nyala biar data login bisa dibaca
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/** @var mysqli $koneksi */
// Panggil file koneksi biar query ke database jalan
require_once('koneksi.php');

// Ambil daftar guru buat ditampilin di pilihan dropdown form
$sql_guru = "SELECT * FROM guru ORDER BY nama ASC";
$query_guru = mysqli_query($koneksi, $sql_guru);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tambah Mengajar</title>
    
    <!-- Panggil Bootstrap 5.3.8 CSS biar konsisten dengan navbar dan footer -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- CSS kustom biar warnanya tetap selaras tema navy & cream -->
    <style>
        body {
            background-color: #f7f6f2 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d;
        }

        /* Atur navbar biar gampang dipencet dan posisinya aman */
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

        /* Bikin dropdown menu muncul di atas elemen lain dan teksnya jelas */
        .navbar .dropdown-menu {
            z-index: 1060 !important;
        }
        .navbar .dropdown-menu .dropdown-item {
            color: #212529 !important;
        }

        /* Gaya card pembungkus form */
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
            padding: 30px;
        }

        h4 {
            color: #1b365d;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #1b365d;
        }

        .form-control, .form-select {
            background-color: #ffffff !important;
            border-color: #dcd7cd !important;
            border-radius: 6px;
            padding: 10px 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #1b365d !important;
            box-shadow: 0 0 0 0.2rem rgba(27, 54, 93, 0.15) !important;
        }

        /* Tombol simpan dan kembali */
        .btn-primary {
            background-color: #1b365d !important;
            border-color: #1b365d !important;
            font-weight: 600;
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background-color: #142845 !important;
        }

        .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            font-weight: 600;
            padding: 10px 20px;
        }
    </style>
</head>
<body>

    <!-- Panggil navbar bagian atas kalau filenya ada -->
    <?php 
    if (file_exists("navbar.php")) {
        require_once('navbar.php'); 
    }
    ?>

    <!-- Kontainer utama halaman form -->
    <div class="container my-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">

                <!-- Card pembungkus form -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        
                        <!-- Judul form -->
                        <h4 class="mb-3">Form Tambah Mengajar</h4>
                        <hr class="mb-4" style="border-color: #1b365d; opacity: 0.5;">

                        <!-- Form input data ke file aksi penyimpanan -->
                        <form action="aksi_tambah_mengajar.php" method="POST">
                            
                            <h5 class="fw-semibold mb-3 text-secondary fs-6">Pengajar</h5>

                            <!-- Pilihan kelas -->
                            <div class="mb-3">
                                <label for="kelas" class="form-label">Pilih Kelas</label>
                                <select class="form-select" id="kelas" name="kelas" required>
                                    <option value="">Pilih Kelas...</option>
                                    <option value="X RPL 1">X RPL 1</option>
                                    <option value="X RPL 2">X RPL 2</option>
                                    <option value="XI RPL 1">XI RPL 1</option>
                                    <option value="XI RPL 2">XI RPL 2</option>
                                    <option value="XI RPL 3">XI RPL 3</option>
                                    <option value="XI RPL 4">XI RPL 4</option>
                                    <option value="XII RPL 1">XII RPL 1</option>
                                </select>
                            </div>

                            <!-- Pilihan guru dari tabel database guru -->
                            <div class="mb-4">
                                <label for="guru_id" class="form-label">Pilih Guru</label>
                                <select class="form-select" id="guru_id" name="guru_id" required>
                                    <option value="">Pilih Guru...</option>
                                    <?php 
                                    // Loop semua data guru biar muncul di dropdown pilihan
                                    while ($guru = mysqli_fetch_assoc($query_guru)) {
                                    ?>
                                        <option value="<?php echo $guru['id']; ?>">
                                            <?php echo htmlspecialchars($guru['nama']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <!-- Tombol simpan dan kembali -->
                            <div class="d-flex gap-2">
                                <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
                                <a href="daftar_mengajar.php" class="btn btn-secondary">Kembali</a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Panggil footer bagian bawah (Bootstrap JS bundle sudah di-load dari footer.php, jadi gak perlu nambah lagi di bawahnya) -->
    <?php 
    if (file_exists("footer.php")) {
        require_once "footer.php"; 
    }
    ?>