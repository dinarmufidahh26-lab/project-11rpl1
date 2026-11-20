<?php
/** @var mysqli $koneksi */

// PANGGIL FILE LAIN
// Panggil tampilan menu atas (navbar)
require_once('navbar.php');
// Panggil setelan koneksi biar bisa nyambung ke database MySQL
require_once('koneksi.php');

// MENGAMBIL PARAMETER DARI URL
// Mengambil data kelas dan mapel_id yang dikirimkan saat tombol "Lihat" diklik dari halaman daftar kelas
$kelas = $_GET['kelas'] ?? 'XI RPL 1';
$mapel_id = $_GET['mapel_id'] ?? '9';

// MENGAMBIL NAMA MATA PELAJARAN BERDASARKAN ID
// Query untuk mendapatkan nama mapel agar bisa ditampilkan di bawah judul
$sql_mapel = "SELECT nama_mapel FROM mapel WHERE id = '$mapel_id'";
$hasil_mapel = mysqli_query($koneksi, $sql_mapel);
$data_mapel = mysqli_fetch_assoc($hasil_mapel);
$nama_mapel = $data_mapel['nama_mapel'] ?? 'Ppkn'; // Default jika kosong

// QUERY DATABASE UNTUK MENAMPILKAN NILAI SISWA
// PERBAIKAN: Melakukan LEFT JOIN ke tabel 'nilai_rapor' agar data nilai yang tersimpan di database dapat ditarik berdasarkan siswa dan mapel_id
$sql = "SELECT siswa.*, 
        nr.nilai_kkm AS kkm, 
        nr.nilai_pengetahuan AS pengetahuan, 
        nr.nilai_keterampilan AS keterampilan, 
        IF(nr.nilai_pengetahuan IS NOT NULL AND nr.nilai_keterampilan IS NOT NULL, (nr.nilai_pengetahuan + nr.nilai_keterampilan) / 2, NULL) AS nilai_akhir, 
        nr.Keterangan AS keterangan 
        FROM siswa 
        LEFT JOIN nilai_rapor nr ON siswa.id = nr.siswa_id AND nr.mapel_id = '$mapel_id'
        WHERE siswa.kelas = '$kelas'";

$hasil = mysqli_query($koneksi, $sql);

// Menyiapkan wadah array kosong untuk menampung data
$data_nilai = [];
if ($hasil && mysqli_num_rows($hasil) > 0) {
    while ($row = mysqli_fetch_assoc($hasil)) {
        $data_nilai[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Nilai Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2; /* Cream soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d; /* Biru Navy */
        }
        /* Navbar kustom agar warnanya selaras */
        nav.navbar, .navbar {
            background-color: #1b365d !important;
        }
        .navbar .navbar-brand,
        .navbar .nav-link,
        .navbar .navbar-text {
            color: #ffffff !important;
        }
        
        /* Container pembungkus utama */
        .content-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 35px;
            border: 1px solid #e8e6e1;
            margin-bottom: 40px;
        }
        h1 {
            color: #1b365d;
            font-weight: 700;
        }
        .border-navy {
            border-color: #1b365d !important;
        }
        
        /* Styling Tabel Custom */
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
        }

        /* Tombol & Aksi */
        .btn-primary {
            background-color: #1b365d;
            border-color: #1b365d;
        }
        .btn-primary:hover {
            background-color: #142845;
            border-color: #142845;
        }
        .page-item.active .page-link {
            background-color: #1b365d;
            border-color: #1b365d;
        }
    </style>
</head>
<body>

<!-- TAMPILAN HALAMAN UTAMA -->
<div class="container mt-4">

    <div class="content-container">
        <!-- Judul Halaman dan Informasi Kelas & Mapel -->
        <div class="mb-4">
            <h1 class="fw-normal display-6">Daftar Nilai Siswa</h1>
            <p class="text-muted">
                Kelas: <strong><?php echo htmlspecialchars($kelas); ?></strong> &nbsp;|&nbsp; 
                Mata Pelajaran: <strong><?php echo htmlspecialchars($nama_mapel); ?></strong>
            </p>
            <hr class="border-navy border-2 opacity-75">
        </div>

        <!-- Filter & Kolom Pencarian -->
        <div class="row align-items-center mb-3">
            <div class="col-auto">
                <label class="col-form-label fw-semibold">Tampilkan</label>
            </div>
            <div class="col-auto">
                <select class="form-select form-select-sm" style="width: 80px;">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="col-auto">
                <span class="form-text">data per halaman</span>
            </div>

            <!-- Kolom Cari di sebelah kanan -->
            <div class="col-md-3 ms-auto">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light fw-semibold">Cari:</span>
                    <input type="text" class="form-control" placeholder="Cari siswa...">
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Nilai Siswa -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle table-custom">
                <thead>
                    <tr class="text-center">
                        <th scope="col" style="width: 5%;">No</th>
                        <th scope="col" style="width: 22%;">Nama Siswa</th>
                        <th scope="col" style="width: 8%;">KKM</th>
                        <th scope="col" style="width: 12%;">Pengetahuan</th>
                        <th scope="col" style="width: 12%;">Keterampilan</th>
                        <th scope="col" style="width: 12%;">Nilai Akhir</th>
                        <th scope="col" style="width: 14%;">Keterangan</th>
                        <th scope="col" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Inisialisasi nomor urut tabel mulai dari 1
                    $no = 1;

                    // Perulangan untuk mencetak baris data siswa ke dalam tabel
                    if (!empty($data_nilai)) {
                        foreach ($data_nilai as $row) {
                    ?>
                    <tr>
                        <!-- Menampilkan nomor urut -->
                        <th scope="row" class="text-center"><?php echo $no++; ?></th>
                        
                        <!-- Menampilkan Nama Siswa -->
                        <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                        
                        <!-- Menampilkan KKM -->
                        <td class="text-center"><?php echo htmlspecialchars($row['kkm'] ?? '-'); ?></td>
                        
                        <!-- Menampilkan Nilai Pengetahuan -->
                        <td class="text-center">
                            <?php if (!empty($row['pengetahuan'])): ?>
                                <span class="badge bg-primary px-3 py-2"><?php echo $row['pengetahuan']; ?></span>
                            <?php else: ?>
                                <span class="text-muted">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Menampilkan Nilai Keterampilan -->
                        <td class="text-center">
                            <?php if (!empty($row['keterampilan'])): ?>
                                <span class="badge bg-primary px-3 py-2"><?php echo $row['keterampilan']; ?></span>
                            <?php else: ?>
                                <span class="text-muted">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Menampilkan Nilai Akhir -->
                        <td class="text-center">
                            <?php if (!empty($row['nilai_akhir'])): ?>
                                <span class="badge bg-success px-3 py-2"><?php echo number_format($row['nilai_akhir'], 2, '.', ''); ?></span>
                            <?php else: ?>
                                <span class="text-muted">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Menampilkan Keterangan Predikat -->
                        <td><?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                        
                        <!-- Tombol Aksi (Tambah, Edit, Hapus) -->
                        <td class="text-center">
                            <?php if (empty($row['nilai_akhir'])): ?>
                                <!-- Tombol Tambah jika nilai belum diisi -->
                                <a href="form_tambah_nilai.php?id_siswa=<?php echo $row['id']; ?>&mapel_id=<?php echo $mapel_id; ?>" class="btn btn-primary btn-sm">Tambah</a>
                            <?php else: ?>
                                <!-- Tombol Edit & Hapus jika nilai sudah ada -->
                                <a href="form_edit_nilai_siswa_guru.php?id_siswa=<?php echo $row['id']; ?>&mapel_id=<?php echo $mapel_id; ?>" class="btn btn-warning btn-sm text-dark">Edit</a>
                                <a href="aksi_hapus_nilai.php?id_siswa=<?php echo $row['id']; ?>&mapel_id=<?php echo $mapel_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus nilai ini?')">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        // Pesan jika tidak ada siswa di kelas tersebut
                        echo '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data siswa di kelas ini.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Navigasi Halaman / Pagination Bawah -->
        <div class="row align-items-center mt-3">
            <div class="col-md-6 text-muted small">
                Menampilkan halaman 1 dari 1
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">Selanjutnya</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

</div>

<!-- Panggil bagian bawah web (footer) -->
<?php 
if (file_exists("footer.php")) {
    require "footer.php"; 
}
?>

</body>
</html>