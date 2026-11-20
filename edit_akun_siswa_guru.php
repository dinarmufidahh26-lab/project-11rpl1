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

// Mengambil username akun yang login
$username = $_SESSION['username'];

// Mengambil data akun dari tabel users
$queryUser = mysqli_query($koneksi, "
    SELECT * FROM users
    WHERE username = '$username'
");
$user = mysqli_fetch_assoc($queryUser);

// Memeriksa akun
if (!$user) {
    die("Akun tidak ditemukan.");
}

// Mengambil ID dan role akun
$id = $user['id'];
$role = strtolower(trim($user['role']));

// Menyiapkan nilai awal
$nama = "";
$nomor = "";
$kelas = "";
$jenis_kelamin = "";
$pesan = "";

// ===================================
// MENGAMBIL DATA SISWA
// ===================================
if ($role == 'siswa') {
    $query = mysqli_query($koneksi, "
        SELECT * FROM siswa
        WHERE user_id = '$id'
    ");
    $data = mysqli_fetch_assoc($query);
    if ($data) {
        $nama = $data['nama_siswa'];
        $nomor = $data['nis'];
        $kelas = $data['kelas'];
        $jenis_kelamin = $data['jenis_kelamin'];
    } else {
        die("Data siswa tidak ditemukan. Periksa user_id pada tabel siswa.");
    }

// ===================================
// MENGAMBIL DATA GURU
// ===================================
} elseif ($role == 'guru') {
    $query = mysqli_query($koneksi, "
        SELECT * FROM guru
        WHERE user_id = '$id'
    ");
    $data = mysqli_fetch_assoc($query);
    if ($data) {
        $nama = $data['nama'];
        $nomor = $data['nip'];
        $jenis_kelamin = $data['jenis_kelamin'];
    } 
} 

// ===================================
// MENYIMPAN PERUBAHAN DATA
// ===================================
if (isset($_POST['simpan'])) {
    $namaBaru = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nomorBaru = mysqli_real_escape_string($koneksi, $_POST['nomor']);
    $jenisBaru = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);

    if ($role == 'siswa') {
        $kelasBaru = mysqli_real_escape_string($koneksi, $_POST['kelas']);
        $update = mysqli_query($koneksi, "
            UPDATE siswa SET
                nama_siswa = '$namaBaru',
                nis = '$nomorBaru',
                kelas = '$kelasBaru',
                jenis_kelamin = '$jenisBaru'
            WHERE user_id = '$id'
        ");
    } else {
        $update = mysqli_query($koneksi, "
            UPDATE guru SET
                nama = '$namaBaru',
                nip = '$nomorBaru',
                jenis_kelamin = '$jenisBaru'
            WHERE user_id = '$id'
        ");
    }

    if ($update) {
        header("Location: profile.php");
        exit;
    } else {
        $pesan = "Data gagal diperbarui: " . mysqli_error($koneksi);
    }
}

// Fungsi untuk mengamankan teks
function aman($teks) {
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2; /* Cream soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d; /* Biru Navy */
        }
        .kotak {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e8e6e1;
        }
        h2 {
            font-weight: bold;
            color: #1b365d; /* Biru Navy */
            margin-bottom: 25px;
            text-align: center;
            border-bottom: 2px solid #f5f2eb;
            padding-bottom: 12px;
        }
        .form-label {
            font-weight: 600;
            color: #1b365d;
        }
        .form-control, .form-select {
            border-color: #ced4da;
            border-radius: 6px;
            padding: 10px 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1b365d;
            box-shadow: 0 0 0 0.2rem rgba(27, 54, 93, 0.15);
        }
        .mb-3 {
            margin-bottom: 20px !important;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-primary {
            background-color: #1b365d; /* Biru Navy */
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
        }
        .btn-primary:hover {
            background-color: #142845; /* Navy Lebih Gelap */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="kotak">
        <h2>Edit Profile</h2>
        <?php if ($pesan != '') { ?>
            <div class="alert alert-danger">
                <?= aman($pesan); ?>
            </div>
        <?php } ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= aman($nama); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">
                    <?= $role == 'siswa' ? 'NIS' : 'NIP'; ?>
                </label>
                <input type="text" name="nomor" class="form-control" value="<?= aman($nomor); ?>" required>
            </div>
            <?php if ($role == 'siswa') { ?>
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas" class="form-control" value="<?= aman($kelas); ?>" required>
                </div>
            <?php } ?>
            <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L" <?= $jenis_kelamin == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?= $jenis_kelamin == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= ($role == 'guru') ? 'daftar_guru.php' : 'daftar_siswa.php'; ?>" class="btn btn-secondary">
                    Batal</a>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<?php 
if (file_exists("footer.php")) {
    require "footer.php"; 
}
?>