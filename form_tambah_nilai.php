<?php
/** @var mysqli $koneksi */

// 1. PANGGIL FILE PENDUKUNG
require_once('navbar.php');
require_once('koneksi.php');

// 2. MENANGKAP PARAMETER DARI URL (atau dari POST jika form disubmit)
$id_siswa = $_GET['id_siswa'] ?? $_POST['id_siswa'] ?? '';
$mapel_id = $_GET['mapel_id'] ?? $_POST['mapel_id'] ?? '';

// Validasi: Jika id_siswa atau mapel_id kosong, hentikan program dan berikan peringatan
if (empty($id_siswa) || empty($mapel_id)) {
    echo "<script>alert('Error: Data Siswa atau Mata Pelajaran tidak boleh kosong!'); window.location='nilai_siswa_guru.php';</script>";
    exit;
}

// 3. MENGAMBIL DATA SISWA DARI TABEL 'siswa'
$sql_siswa = "SELECT * FROM siswa WHERE id = '$id_siswa'";
$hasil_siswa = mysqli_query($koneksi, $sql_siswa);
$data_siswa = mysqli_fetch_assoc($hasil_siswa);

// 4. MENGAMBIL DATA MATA PELAJARAN DARI TABEL 'mapel'
$sql_mapel = "SELECT * FROM mapel WHERE id = '$mapel_id'";
$hasil_mapel = mysqli_query($koneksi, $sql_mapel);
$data_mapel = mysqli_fetch_assoc($hasil_mapel);

// Menyiapkan variabel untuk ditampilkan di form
$nama_siswa = $data_siswa['nama_siswa'] ?? 'Siswa Tidak Ditemukan';
$kelas_siswa = $data_siswa['kelas'] ?? '-';
$nama_mapel = $data_mapel['nama_mapel'] ?? 'Pelajaran Tidak Ditemukan';

// 5. PROSES PENYIMPANAN KETIKA TOMBOL "Simpan Nilai" DIKLIK
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mencegah SQL Injection
    $kkm = mysqli_real_escape_string($koneksi, $_POST['kkm']);
    $pengetahuan = mysqli_real_escape_string($koneksi, $_POST['pengetahuan']);
    $keterampilan = mysqli_real_escape_string($koneksi, $_POST['keterampilan']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    
    // QUERY INSERT KE TABEL 'nilai_rapor'
    $sql_simpan = "INSERT INTO nilai_rapor (siswa_id, mapel_id, nilai_kkm, nilai_pengetahuan, nilai_keterampilan, Keterangan) 
                   VALUES ('$id_siswa', '$mapel_id', '$kkm', '$pengetahuan', '$keterampilan', '$keterangan')";
    
    // Menjalankan query simpan ke database
    if (mysqli_query($koneksi, $sql_simpan)) {
        echo "<script>alert('Nilai berhasil disimpan!'); window.location='nilai_siswa_guru.php?kelas=" . urlencode($kelas_siswa) . "&mapel_id=" . urlencode($mapel_id) . "';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan nilai: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Nilai Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2; /* Cream soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d; /* Biru Navy */
        }
        /* Penyesuaian navbar agar selaras jika ada navbar custom */
        nav.navbar, .navbar {
            background-color: #1b365d !important;
        }
        .navbar .navbar-brand,
        .navbar .nav-link,
        .navbar .navbar-text {
            color: #ffffff !important;
        }
        
        .form-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 35px;
            border: 1px solid #e8e6e1;
        }
        h2 {
            color: #1b365d;
            font-weight: 700;
        }
        .border-navy {
            border-color: #1b365d !important;
        }
        .info-box {
            background-color: #f5f2eb !important; /* Cream card background */
            border: 1px solid #e2ddd5 !important;
            border-radius: 8px;
        }
        .form-label {
            font-weight: 600;
            color: #1b365d;
        }
        .form-control {
            border-radius: 6px;
            padding: 10px 14px;
            border-color: #ced4da;
        }
        .form-control:focus {
            border-color: #1b365d;
            box-shadow: 0 0 0 0.2rem rgba(27, 54, 93, 0.15);
        }
        .btn-primary {
            background-color: #1b365d;
            border-color: #1b365d;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #142845;
            border-color: #142845;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- TAMPILAN HALAMAN HTML FORM TAMBAH NILAI -->
<div class="container mt-4 mb-5" style="max-width: 800px;">

    <div class="form-card">
        <!-- Judul Form -->
        <div class="mb-4">
            <h2 class="fw-bold mb-2">Tambah Nilai Siswa</h2>
            <hr class="border-navy border-2 opacity-75">
        </div>

        <!-- Kotak Informasi Mata Pelajaran & Kelas -->
        <div class="card mb-4 info-box shadow-sm">
            <div class="card-body">
                <h5 class="card-title fw-bold text-dark mb-1"><?php echo htmlspecialchars($nama_mapel); ?></h5>
                <p class="card-text text-muted mb-0">Kelas: <?php echo htmlspecialchars($kelas_siswa); ?></p>
            </div>
        </div>

        <!-- Form Input Nilai -->
        <form action="" method="POST">
            
            <!-- HIDDEN INPUT: Menyimpan id_siswa dan mapel_id agar terbawa saat tombol Simpan ditekan -->
            <input type="hidden" name="id_siswa" value="<?php echo htmlspecialchars($id_siswa); ?>">
            <input type="hidden" name="mapel_id" value="<?php echo htmlspecialchars($mapel_id); ?>">

            <!-- Input Nama Siswa -->
            <div class="mb-3">
                <label class="form-label">Nama Siswa</label>
                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($nama_siswa); ?>" readonly>
            </div>

            <!-- Input Nilai KKM -->
            <div class="mb-3">
                <label class="form-label">Nilai KKM</label>
                <input type="number" name="kkm" class="form-control" value="75" required>
            </div>

            <!-- Input Nilai Pengetahuan -->
            <div class="mb-3">
                <label class="form-label">Nilai Pengetahuan</label>
                <input type="number" name="pengetahuan" class="form-control" placeholder="Masukkan nilai pengetahuan" required>
            </div>

            <!-- Input Nilai Keterampilan -->
            <div class="mb-3">
                <label class="form-label">Nilai Keterampilan</label>
                <input type="number" name="keterampilan" class="form-control" placeholder="Masukkan nilai keterampilan" required>
            </div>

            <!-- Input Keterangan -->
            <div class="mb-4">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Tuntas, Perlu Bimbingan"></textarea>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex justify-content-between pt-2">
                <a href="nilai_siswa_guru.php?kelas=<?php echo urlencode($kelas_siswa); ?>&mapel_id=<?php echo $mapel_id; ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Nilai</button>
            </div>

        </form>
    </div>

</div>

<!-- Panggil bagian footer -->
<?php 
if (file_exists("footer.php")) {
    require "footer.php"; 
}
?>

</body>
</html>