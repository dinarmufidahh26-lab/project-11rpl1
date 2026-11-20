<?php
/** @var mysqli $koneksi */

// ==========================================
// 1. PANGGIL FILE PENDUKUNG
// ==========================================
// Panggil koneksi dulu agar $koneksi siap dipakai navbar dan query
require_once('koneksi.php');
require_once('navbar.php');

// ==========================================
// 2. TANGKAP PARAMETER DARI URL / FORM POST
// ==========================================
// Tangkap id_siswa, kelas, dan mapel_id (bisa dari GET saat buka halaman atau POST saat submit)
$id_siswa = $_GET['id_siswa'] ?? $_POST['id_siswa'] ?? '';
$kelas    = $_GET['kelas'] ?? $_POST['kelas'] ?? '';
$mapel_id = $_GET['mapel_id'] ?? $_POST['mapel_id'] ?? '';

// Jika mapel_id kosong, hentikan dan balik ke nilai_siswa_guru.php
if (empty($mapel_id)) {
    echo "<script>alert('Error: Mata Pelajaran tidak boleh kosong!'); window.location='nilai_siswa_guru.php';</script>";
    exit;
}

// Jika id_siswa belum ada tetapi parameter kelas ada, ambil siswa pertama di kelas tersebut
// (atau jika id_siswa sudah ada, ambil data siswa berdasarkan id_siswa)
if (!empty($id_siswa)) {
    $id_siswa_aman = mysqli_real_escape_string($koneksi, $id_siswa);
    $sql_siswa     = "SELECT * FROM siswa WHERE id = '$id_siswa_aman' LIMIT 1";
} elseif (!empty($kelas)) {
    $kelas_aman    = mysqli_real_escape_string($koneksi, $kelas);
    $sql_siswa     = "SELECT * FROM siswa WHERE kelas = '$kelas_aman' ORDER BY id ASC LIMIT 1";
} else {
    echo "<script>alert('Error: Data Siswa atau Kelas tidak boleh kosong!'); window.location='nilai_siswa_guru.php';</script>";
    exit;
}

$hasil_siswa = mysqli_query($koneksi, $sql_siswa);
$data_siswa  = mysqli_fetch_assoc($hasil_siswa);

if (!$data_siswa) {
    echo "<script>alert('Error: Data Siswa tidak ditemukan!'); window.location='nilai_siswa_guru.php';</script>";
    exit;
}

// Pastikan $id_siswa terisi sesuai data siswa yang ditemukan
$id_siswa    = $data_siswa['id'];
$nama_siswa  = $data_siswa['nama_siswa'] ?? 'Siswa Tidak Ditemukan';
$kelas_siswa = $data_siswa['kelas'] ?? ($kelas !== '' ? $kelas : '-');

// ==========================================
// 3. AMBIL DATA MATA PELAJARAN & NILAI RAPOR
// ==========================================
$mapel_id_aman = mysqli_real_escape_string($koneksi, $mapel_id);
$sql_mapel     = "SELECT * FROM mapel WHERE id = '$mapel_id_aman' LIMIT 1";
$hasil_mapel   = mysqli_query($koneksi, $sql_mapel);
$data_mapel    = mysqli_fetch_assoc($hasil_mapel);
$nama_mapel    = $data_mapel['nama_mapel'] ?? 'Pelajaran Tidak Ditemukan';

// Ambil data nilai yang sudah ada di tabel nilai_rapor
$sql_nilai   = "SELECT * FROM nilai_rapor WHERE siswa_id = '$id_siswa' AND mapel_id = '$mapel_id_aman' LIMIT 1";
$hasil_nilai = mysqli_query($koneksi, $sql_nilai);
$data_nilai  = mysqli_fetch_assoc($hasil_nilai);

$kkm          = $data_nilai['nilai_kkm'] ?? '75';
$pengetahuan  = $data_nilai['nilai_pengetahuan'] ?? '';
$keterampilan = $data_nilai['nilai_keterampilan'] ?? '';
$keterangan   = $data_nilai['Keterangan'] ?? '';

// ==========================================
// 4. PROSES UPDATE KETIKA FORM DI-SUBMIT (POST)
// ==========================================
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Amankan input dari form agar terhindar dari SQL Injection
    $id_siswa_post    = mysqli_real_escape_string($koneksi, $_POST['id_siswa'] ?? $id_siswa);
    $mapel_id_post    = mysqli_real_escape_string($koneksi, $_POST['mapel_id'] ?? $mapel_id);
    $kkm_baru         = mysqli_real_escape_string($koneksi, $_POST['kkm'] ?? '75');
    $pengetahuan_baru = mysqli_real_escape_string($koneksi, $_POST['pengetahuan'] ?? '');
    $keterampilan_baru= mysqli_real_escape_string($koneksi, $_POST['keterampilan'] ?? '');
    $keterangan_baru  = mysqli_real_escape_string($koneksi, $_POST['keterangan'] ?? '');

    // Cek apakah baris nilai untuk siswa & mapel ini sudah ada di nilai_rapor
    $cek_ada = mysqli_query(
        $koneksi,
        "SELECT id FROM nilai_rapor WHERE siswa_id = '$id_siswa_post' AND mapel_id = '$mapel_id_post' LIMIT 1"
    );

    if ($cek_ada && mysqli_num_rows($cek_ada) > 0) {
        // Jika sudah ada, update nilainya
        $sql_simpan = "UPDATE nilai_rapor SET
                        nilai_kkm = '$kkm_baru',
                        nilai_pengetahuan = '$pengetahuan_baru',
                        nilai_keterampilan = '$keterampilan_baru',
                        Keterangan = '$keterangan_baru'
                       WHERE siswa_id = '$id_siswa_post' AND mapel_id = '$mapel_id_post'";
    } else {
        // Jika belum ada baris nilai di nilai_rapor, generate id baru lalu insert
        $q_max   = mysqli_query($koneksi, "SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM nilai_rapor");
        $d_max   = mysqli_fetch_assoc($q_max);
        $next_id = (int) ($d_max['next_id'] ?? 1);

        $sql_simpan = "INSERT INTO nilai_rapor (id, siswa_id, mapel_id, nilai_kkm, nilai_pengetahuan, nilai_keterampilan, Keterangan)
                       VALUES ('$next_id', '$id_siswa_post', '$mapel_id_post', '$kkm_baru', '$pengetahuan_baru', '$keterampilan_baru', '$keterangan_baru')";
    }

    // Kembali ke halaman daftar nilai guru sesuai kelas & mapel_id
    $url_kembali = "nilai_siswa_guru.php?kelas=" . urlencode($kelas_siswa) . "&mapel_id=" . urlencode($mapel_id_post);

    if (mysqli_query($koneksi, $sql_simpan)) {
        echo "<script>alert('Nilai berhasil diperbarui!'); window.location='" . $url_kembali . "';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui nilai: " . addslashes(mysqli_error($koneksi)) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Nilai Siswa</title>
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
        
        /* Kotak Pembungkus Utama Form */
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
        
        /* Kotak Informasi Mata Pelajaran & Kelas */
        .info-box {
            background-color: #f5f2eb !important; /* Cream card background */
            border: 1px solid #e2ddd5 !important;
            border-radius: 8px;
        }
        
        /* Elemen Form */
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
        
        /* Tombol Aksi */
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

<!-- TAMPILAN HALAMAN HTML FORM EDIT NILAI -->
<div class="container mt-4 mb-5" style="max-width: 800px;">

    <div class="form-card">
        <!-- Judul Form -->
        <div class="mb-4">
            <h2 class="fw-bold mb-2">Edit Nilai Siswa</h2>
            <hr class="border-navy border-2 opacity-75">
        </div>

        <!-- Kotak Informasi Mata Pelajaran & Kelas -->
        <div class="card mb-4 info-box shadow-sm">
            <div class="card-body">
                <h5 class="card-title fw-bold text-dark mb-1"><?php echo htmlspecialchars($nama_mapel); ?></h5>
                <p class="card-text text-muted mb-0">Kelas: <?php echo htmlspecialchars($kelas_siswa); ?></p>
            </div>
        </div>

        <!-- Form Edit Nilai -->
        <form action="" method="POST">
            
            <!-- HIDDEN INPUT: Menyimpan id_siswa, kelas, dan mapel_id agar terbawa saat submit -->
            <input type="hidden" name="id_siswa" value="<?php echo htmlspecialchars($id_siswa); ?>">
            <input type="hidden" name="kelas" value="<?php echo htmlspecialchars($kelas_siswa); ?>">
            <input type="hidden" name="mapel_id" value="<?php echo htmlspecialchars($mapel_id); ?>">

            <!-- Input Nama Siswa -->
            <div class="mb-3">
                <label class="form-label">Nama Siswa</label>
                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($nama_siswa); ?>" readonly>
            </div>

            <!-- Input Nilai KKM -->
            <div class="mb-3">
                <label class="form-label">Nilai KKM</label>
                <input type="number" name="kkm" class="form-control" value="<?php echo htmlspecialchars($kkm); ?>" required>
            </div>

            <!-- Input Nilai Pengetahuan -->
            <div class="mb-3">
                <label class="form-label">Nilai Pengetahuan</label>
                <input type="number" name="pengetahuan" class="form-control" value="<?php echo htmlspecialchars($pengetahuan); ?>" placeholder="Masukkan nilai pengetahuan" required>
            </div>

            <!-- Input Nilai Keterampilan -->
            <div class="mb-3">
                <label class="form-label">Nilai Keterampilan</label>
                <input type="number" name="keterampilan" class="form-control" value="<?php echo htmlspecialchars($keterampilan); ?>" placeholder="Masukkan nilai keterampilan" required>
            </div>

            <!-- Input Keterangan -->
            <div class="mb-4">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Baik, Tuntas"><?php echo htmlspecialchars($keterangan); ?></textarea>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex justify-content-between pt-2">
                <a href="nilai_siswa_guru.php?kelas=<?php echo urlencode($kelas_siswa); ?>&mapel_id=<?php echo urlencode($mapel_id); ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update Nilai</button>
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