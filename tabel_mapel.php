<?php 
/** @var mysqli $koneksi */

// ==========================================
// 1. MEMANGGIL FILE PENDUKUNG
// ==========================================
// Memanggil file navbar (menu navigasi) untuk ditampilkan di halaman ini
require_once ('navbar.php');
// Memanggil file koneksi.php untuk menghubungkan PHP ke database MySQL
require_once ('koneksi.php');

// ==========================================
// 2. QUERY DATABASE MATA PELAJARAN
// ==========================================
// Menyiapkan perintah SQL untuk mengambil semua data dari tabel mapel 
// dan menggabungkannya (LEFT JOIN) dengan tabel guru berdasarkan guru_id
$sql = "SELECT mapel.*, guru.nama AS nama
        FROM mapel
        LEFT JOIN guru ON mapel.guru_id = guru.id
        ORDER BY mapel.id DESC";

// Menjalankan query SQL ke database menggunakan koneksi yang tersedia
$hasil = mysqli_query($koneksi, $sql);

// Menyiapkan variabel array kosong untuk menampung hasil data dari database
$data = [];

// ==========================================
// 3. CEK DAN AMBIL DATA (LOOPING)
// ==========================================
// Mengecek apakah jumlah baris data hasil query lebih dari 0
if (mysqli_num_rows($hasil) > 0) {
    // Jika ada, ambil setiap baris data ke dalam bentuk array asosiatif, lalu masukkan ke array $data
    while ($row = mysqli_fetch_assoc($hasil)) {
        $data[] = $row;
    }
} else {
    // Jika data tidak ditemukan, cetak teks "Tidak ada data"
    $pesan_kosong = "Tidak ada data";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mata Pelajaran</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2 !important; /* Cream soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d; /* Biru Navy */
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

        /* Container Pembungkus Utama */
        .content-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 35px;
            border: 1px solid #e8e6e1;
            margin-top: 30px;
            margin-bottom: 40px;
        }

        h1 {
            color: #1b365d !important;
            font-weight: 700;
        }

        .border-navy {
            border-color: #1b365d !important;
        }

        /* Styling Tabel Custom */
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

        /* Tombol Aksi */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
            font-weight: 600;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- ========================================== -->
<!-- 4. TAMPILAN HALAMAN UTAMA (HTML & BOOTSTRAP)-->
<!-- ========================================== -->
<div class="container">

    <div class="content-container">
        <!-- Judul Halaman -->
        <div class="mb-4 text-center">
            <h1 class="fw-normal display-6">Data Mata Pelajaran</h1>
            <hr class="border-navy border-2 opacity-75 mx-auto" style="width: 100%;">
        </div>

        <?php if (isset($pesan_kosong)): ?>
            <div class="alert alert-warning text-center" role="alert">
                <?php echo $pesan_kosong; ?>
            </div>
        <?php else: ?>
            <!-- Tabel Data Mapel -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 8%;">No</th>
                            <th scope="col" style="width: 20%;">Kode Mapel</th>
                            <th scope="col" style="width: 30%;">Nama Mapel</th>
                            <th scope="col" style="width: 22%;">Nama guru</th>
                            <th scope="col" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // Inisialisasi variabel nomor urut tabel mulai dari angka 1
                        $no = 1;

                        // Melakukan perulangan (foreach) untuk menampilkan setiap data mapel ke dalam baris tabel
                        foreach ($data as $mapel) {
                        ?>
                            <tr>
                                <!-- Menampilkan nomor urut dan menaikkan nilainya setiap baris -->
                                <th scope="row" class="text-center"><?php echo $no++; ?></th>

                                <!-- Menampilkan kolom Kode Mapel dari database -->
                                <td class="fw-semibold"><?php echo htmlspecialchars($mapel['kode_mapel']); ?></td>

                                <!-- Menampilkan kolom Nama Mapel dari database -->
                                <td><?php echo htmlspecialchars($mapel['nama_mapel']); ?></td>

                                <!-- Menampilkan kolom Nama Guru pengampu dari hasil JOIN -->
                                <td><?php echo htmlspecialchars($mapel['nama'] ?? '-'); ?></td>

                                <!-- Kolom Tombol Aksi (Edit & Delete) -->
                                <td class="text-center">
                                    <!-- Tombol Edit: Mengarah ke file form edit berdasarkan ID mapel -->
                                    <a href="form_edit_mapel.php?id=<?php echo $mapel['id']; ?>" 
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <!-- Tombol Delete: Mengarah ke file aksi delete berdasarkan ID mapel 
                                         disertai konfirmasi pop-up JavaScript (onclick confirm) -->
                                    <a href="aksi_delete_mapel.php?id=<?php echo $mapel['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>

                        <?php
                         } // Akhir dari perulangan foreach
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- ========================================== -->
<!-- 5. FOOTER (BAGIAN BAWAH HALAMAN)          -->
<!-- ========================================== -->
<?php require "footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>