<?php
// ==========================================
// 1. KONEKSI & SESSION
// ==========================================
// Menghubungkan file ini ke database MySQL melalui file koneksi.php
include 'koneksi.php';

// Memulai session PHP untuk membaca data login pengguna (guru yang sedang aktif)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mengambil ID Guru yang sedang login dari session. 
$id_guru = $_SESSION['user_id'] ?? $_SESSION['id_guru'] ?? 7; 

// ==========================================
// 2. MENANGKAP PARAMETER URL (AKSI & KELAS)
// ==========================================
$aksi = $_GET['aksi'] ?? '';
// Menangkap nilai 'kelas' dari URL untuk memfilter data siswa sesuai kelas yang dipilih
$kelas = $_GET['kelas'] ?? $_GET['nama_kelas'] ?? 'XI RPL 1';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nilai Siswa</title>
    <!-- Memuat framework CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Kustom Tema Biru Navy & Cream -->
    <style>
        body {
            background-color: #f7f6f2; /* Cream sangat soft untuk background luar */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        /* Navbar dengan tema Biru Navy */
        .navbar-custom {
            background-color: #1b365d !important;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link,
        .navbar-custom .navbar-text {
            color: #fff !important;
        }
        .navbar-custom .nav-link.active {
            color: #f5f2eb !important;
            font-weight: bold;
            border-bottom: 2px solid #f5f2eb;
        }
        /* Desain Card & Tabel */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
        }
        .table-custom thead {
            background-color: #1b365d;
            color: #ffffff;
        }
        .table-custom th, .table-custom td {
            vertical-align: middle;
        }
        /* Tombol Aksi */
        .btn-primary {
            background-color: #1b365d;
            border-color: #1b365d;
        }
        .btn-primary:hover {
            background-color: #142845;
            border-color: #142845;
        }
        h2 {
            color: #1b365d;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- ========================================== -->
    <!-- 3. NAVBAR (MENU NAVIGASI ATAS)            -->
    <!-- ========================================== -->
    <nav class="navbar navbar-expand-lg navbar-custom px-4 mb-4 shadow-sm">
        <a class="navbar-brand" href="daftar_kelas_guru.php">Portal Guru</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <!-- Tautan menuju halaman Home/Data Guru -->
                <li class="nav-item"><a class="nav-link" href="daftar_guru.php">Home</a></li>
                <!-- Tautan menu aktif yang mengarah ke file ini sendiri -->
                <li class="nav-item"><a class="nav-link active" href="daftar_kelas_guru.php">Nilai Siswa</a></li>
            </ul>
            <!-- Menampilkan nama guru yang sedang login -->
            <span class="navbar-text">Endah, S.Pd</span>
        </div>
    </nav>

    <div class="container mb-5">
        
        <!-- ========================================== -->
        <!-- 4. KONDISI HALAMAN: DETAIL NILAI SISWA     -->
        <!-- ========================================== -->
        <?php if ($aksi == 'detail' && !empty($kelas)): ?>
            <?php
            // Menangkap mapel_id dari URL untuk dibawa ke form tambah/edit/hapus
            $mapel_id = $_GET['mapel_id'] ?? '';

            // Query untuk mengambil data siswa dan nilai berdasarkan kelas dan mapel_id
            $query_nilai = mysqli_query($koneksi, "
                SELECT s.id AS siswa_id, s.nis, s.nama_siswa, n.nilai_kkm, n.nilai_pengetahuan, n.nilai_keterampilan, n.Keterangan 
                FROM siswa s 
                LEFT JOIN nilai_rapor n ON s.id = n.siswa_id AND n.mapel_id = '$mapel_id'
                WHERE s.kelas = '$kelas'
            ");
            ?>
            
            <!-- Bagian Judul dan Tombol Kembali -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Daftar Nilai Siswa - Kelas <?= htmlspecialchars($kelas); ?></h2>
                <!-- Tombol Kembali ke daftar kelas awal -->
                <a href="daftar_kelas_guru.php" class="btn btn-secondary">Kembali</a>
            </div>

            <!-- Tabel Daftar Nilai Siswa -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <table class="table table-bordered table-striped align-middle table-custom">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Nilai KKM</th>
                                <th class="text-center">Pengetahuan</th>
                                <th class="text-center">Keterampilan</th>
                                <th class="text-center">Keterangan</th>
                                <th style="width: 15%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            if ($query_nilai && mysqli_num_rows($query_nilai) > 0) {
                                while ($data = mysqli_fetch_assoc($query_nilai)) { 
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($data['nis'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($data['nama_siswa'] ?? ''); ?></td>
                                <td class="text-center"><?= $data['nilai_kkm'] ?? '<span class="text-muted">-</span>'; ?></td>
                                <td class="text-center"><?= $data['nilai_pengetahuan'] ?? '<span class="text-muted">Belum ada</span>'; ?></td>
                                <td class="text-center"><?= $data['nilai_keterampilan'] ?? '<span class="text-muted">Belum ada</span>'; ?></td>
                                <td class="text-center">
                                    <?php if(!empty($data['Keterangan'])): ?>
                                        <span class="badge bg-success px-2 py-1"><?= htmlspecialchars($data['Keterangan']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ada</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Kolom Tombol Aksi (Tambah / Edit / Hapus) -->
                                <td class="text-center">
                                    <?php if (empty($data['nilai_pengetahuan']) && empty($data['nilai_keterampilan'])): ?>
                                        <!-- Jika nilai belum diisi, tampilkan tombol Tambah -->
                                        <a href="form_tambah_nilai.php?id_siswa=<?= $data['siswa_id']; ?>&mapel_id=<?= $mapel_id; ?>" class="btn btn-primary btn-sm">Tambah</a>
                                    <?php else: ?>
                                        <!-- Jika nilai sudah ada, tampilkan tombol Edit dan Hapus -->
                                        <a href="form_edit_nilai_siswa_guru.php?id_siswa=<?= $data['siswa_id']; ?>&mapel_id=<?= $mapel_id; ?>" class="btn btn-warning btn-sm text-dark">Edit</a>
                                        <a href="aksi_hapus_nilai.php?id_siswa=<?= $data['siswa_id']; ?>&mapel_id=<?= $mapel_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus nilai ini?')">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                } 
                            } else {
                                echo '<tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data siswa di kelas ini.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- ========================================== -->
        <!-- 5. KONDISI HALAMAN: DAFTAR KELAS UTAMA     -->
        <!-- ========================================== -->
        <?php else: ?>
            <h2 class="text-center mb-4">Daftar Kelas yang Diajar</h2>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <!-- Tabel Daftar Kelas dan Mata Pelajaran Guru -->
                    <table class="table table-bordered table-striped align-middle table-custom">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">No</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center">Jumlah Siswa</th>
                                <th style="width: 10%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            /** @var mysqli $koneksi */

                            // Mengambil data mata pelajaran berdasarkan guru yang login, khusus untuk kelas 'XI RPL 1' saja
                            $query = mysqli_query($koneksi, "
                                SELECT m.id AS mapel_id, m.kode_mapel, m.nama_mapel, s.kelas, 
                                (SELECT COUNT(*) FROM siswa WHERE siswa.kelas = 'XI RPL 1') as jumlah_siswa 
                                FROM mapel m 
                                JOIN siswa s ON s.kelas = 'XI RPL 1'
                                WHERE m.guru_id = '$id_guru'
                                GROUP BY m.id
                            "); 

                            if (!$query) {
                                die("Query Error: " . mysqli_error($koneksi));
                            }

                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) { 
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['kelas'] ?? 'XI RPL 1'); ?></td>
                                <td><?= htmlspecialchars($row['nama_mapel'] ?? ''); ?></td>
                                <td class="text-center"><?= $row['jumlah_jumlah_siswa'] ?? $row['jumlah_siswa'] ?? 0; ?> siswa</td>
                                <td class="text-center">
                                    <!-- Tombol Lihat mengarah ke detail kelas dan membawa parameter mapel_id -->
                                    <a href="daftar_kelas_guru.php?aksi=detail&kelas=XI+RPL+1&mapel_id=<?= $row['mapel_id']; ?>" class="btn btn-primary btn-sm">Lihat</a>
                                </td>
                            </tr>
                            <?php 
                                } 
                            } else {
                                echo '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada kelas yang diajar.</td></tr>';
                            }
                            ?> 
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>