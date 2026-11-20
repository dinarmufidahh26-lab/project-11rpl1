<?php
// Menghubungkan ke database
require_once 'koneksi.php';

// Memulai session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// 1. Mengambil data akun dari tabel users
$stmt = mysqli_prepare($koneksi, "SELECT id, username, role FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    die("Data akun tidak ditemukan.");
}

$id = $user['id'];
$role = strtolower(trim($user['role']));

if ($role != 'siswa') {
    die("Halaman ini khusus untuk akses siswa.");
}

// 2. Mengambil data spesifik siswa (mendapatkan id siswa)
$stmt_siswa = mysqli_prepare($koneksi, "SELECT id, nama_siswa, nis, kelas FROM siswa WHERE user_id = ?");
mysqli_stmt_bind_param($stmt_siswa, "i", $id);
mysqli_stmt_execute($stmt_siswa);
$data_siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_siswa));

if (!$data_siswa) {
    die("Data siswa tidak ditemukan.");
}

$siswa_id = $data_siswa['id'];
$nama = $data_siswa['nama_siswa'];
$nis = $data_siswa['nis'];
$kelas = $data_siswa['kelas'];

// 3. Mengambil data nilai menggunakan tabel 'nilai_rapor' dan melakukan JOIN ke tabel 'mapel'
$query_nilai = mysqli_query($koneksi, "
    SELECT nr.*, m.nama_mapel 
    FROM nilai_rapor nr
    JOIN mapel m ON nr.mapel_id = m.id
    WHERE nr.siswa_id = '$siswa_id'
");

$total_rata = 0;
$jumlah_mapel = 0;
$data_nilai_array = [];

while ($row = mysqli_fetch_assoc($query_nilai)) {
    // Menghitung rata-rata dari nilai pengetahuan dan keterampilan per mapel
    $rata_mapel = ($row['nilai_pengetahuan'] + $row['nilai_keterampilan']) / 2;
    $row['rata_mapel'] = $rata_mapel;
    
    $total_rata += $rata_mapel;
    $jumlah_mapel++;
    
    $data_nilai_array[] = $row;
}

// Menghitung rata-rata keseluruhan dari semua mapel
$rata_rata_keseluruhan = ($jumlah_mapel > 0) ? ($total_rata / $jumlah_mapel) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilai Siswa - <?= htmlspecialchars($nama); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2 !important; /* Cream soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d; /* Biru Navy */
        }
        
        /* Tema Navy & Cream untuk Header & Card */
        h2, h4, .card-title {
            color: #1b365d !important;
        }
        
        .card-rata {
            max-width: 400px;
            margin: 0 auto;
            border: 1px solid #e2ddd5 !important;
            border-radius: 12px;
            background-color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .card-rata h1 {
            color: #1b365d !important;
            font-weight: 700;
        }

        /* Kotak Utama / Card Pembungkus */
        .card.shadow-sm.border {
            background-color: #ffffff !important;
            border-radius: 12px !important;
            border: 1px solid #e8e6e1 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
        }

        /* Styling Tabel Custom Navy */
        .table thead.table-light {
            background-color: #1b365d !important;
            color: #ffffff !important;
        }
        .table thead.table-light th {
            background-color: #1b365d !important;
            color: #ffffff !important;
            border-color: #142845 !important;
        }
        .table td {
            vertical-align: middle;
        }

        /* Tombol & Pagination */
        .btn-secondary {
            background-color: #1b365d !important;
            border-color: #1b365d !important;
        }
        .btn-secondary:hover {
            background-color: #142845 !important;
            border-color: #142845 !important;
        }
        .page-item.active .page-link {
            background-color: #1b365d !important;
            border-color: #1b365d !important;
        }
    </style>
</head>
<body class="py-5">

    <div class="container">
        <!-- Bagian Header Profil Siswa -->
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-3">Nilai Siswa</h2>
            <h4 class="fw-semibold mb-2"><?= htmlspecialchars($nama); ?></h4>
            <p class="mb-1 text-muted">NIS: <strong><?= htmlspecialchars($nis); ?></strong></p>
            <p class="text-muted">Kelas: <strong><?= htmlspecialchars($kelas); ?></strong></p>
        </div>

        <!-- Kartu Rata-rata Nilai -->
        <div class="card card-rata text-center shadow-sm mb-5 bg-white">
            <div class="card-body py-4">
                <h6 class="text-muted mb-2">Rata-rata Nilai</h6>
                <h1 class="display-4 fw-normal mb-0"><?= number_format($rata_rata_keseluruhan, 2, '.', ''); ?></h1>
            </div>
        </div>

        <!-- Tabel Daftar Nilai -->
        <div class="card shadow-sm border">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Daftar Nilai</h5>

                <!-- Kontrol Tabel (Tampilkan & Cari) -->
                <div class="row justify-content-between align-items-center mb-3">
                    <div class="col-auto d-flex align-items-center">
                        <span class="me-2">Tampilkan</span>
                        <select class="form-select form-select-sm d-inline-block w-auto me-2">
                            <option value="10">10</option>
                        </select>
                        <span>data per halaman</span>
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <label for="cari" class="me-2">Cari:</label>
                        <input type="text" id="cari" class="form-control form-control-sm">
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>KKM</th>
                                <th>Pengetahuan</th>
                                <th>Keterampilan</th>
                                <th>Rata-rata</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data_nilai_array) > 0) : ?>
                                <?php $no = 1; foreach ($data_nilai_array as $nilai) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($nilai['nama_mapel']); ?></td>
                                        <td><?= htmlspecialchars($nilai['nilai_kkm']); ?></td>
                                        <td><?= htmlspecialchars($nilai['nilai_pengetahuan']); ?></td>
                                        <td><?= htmlspecialchars($nilai['nilai_keterampilan']); ?></td>
                                        <td><strong><?= number_format($nilai['rata_mapel'], 2, '.', ''); ?></strong></td>
                                        <td><?= htmlspecialchars($nilai['Keterangan']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">Belum ada data nilai rapor tersedia.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Tabel (Informasi & Navigasi Halaman) -->
                <div class="row justify-content-between align-items-center mt-3">
                    <div class="col-auto text-muted small">
                        Menampilkan halaman 1 dari 1
                    </div>
                    <div class="col-auto">
                        <nav>
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

        <!-- Tombol Navigasi Kembali ke Profil -->
        <div class="mt-4 text-end">
            <a href="daftar_siswa.php" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>

</body>
</html>