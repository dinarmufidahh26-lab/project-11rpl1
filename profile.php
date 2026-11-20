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

// Mengambil username yang sedang login
$username = $_SESSION['username'];

// Mengambil data akun berdasarkan username
$stmt = mysqli_prepare($koneksi, "
    SELECT id, username, role
    FROM users
    WHERE username = ?
");

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Jika akun tidak ditemukan
if (!$user) {
    die("Data akun tidak ditemukan.");
}

// Mengambil ID dan role akun
$id = $user['id'];
$username = $user['username'];
$role = strtolower(trim($user['role']));

// Menyiapkan data profil awal
$nama = $username;
$nomor = "-";
$kelas = "-";
$jenis_kelamin = "-";

// Mengambil data berdasarkan role
if ($role == 'siswa') {

    // Mengambil data siswa
    $stmt = mysqli_prepare($koneksi, "
        SELECT nama_siswa, nis, kelas, jenis_kelamin
        FROM siswa
        WHERE user_id = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $nama = $data['nama_siswa'];
        $nomor = $data['nis'];
        $kelas = $data['kelas'];
        $jenis_kelamin = $data['jenis_kelamin'];
    }

} elseif ($role == 'guru') {

    // Mengambil data guru
    $stmt = mysqli_prepare($koneksi, "
        SELECT nama, nip, jenis_kelamin
        FROM guru
        WHERE user_id = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $nama = $data['nama'];
        $nomor = $data['nip'];
        $jenis_kelamin = isset($data['jenis_kelamin']) ? $data['jenis_kelamin'] : '-';
    }
}

// Mengamankan data sebelum ditampilkan
$nama_tampil = htmlspecialchars($nama ?? '');
$username_tampil = htmlspecialchars($username ?? '');
$role_tampil = htmlspecialchars(ucfirst($role));
$nomor_tampil = htmlspecialchars((string) $nomor);
$kelas_tampil = htmlspecialchars((string) $kelas);
$jk_tampil = htmlspecialchars((string) $jenis_kelamin);

// Mengambil huruf pertama nama
$inisial = strtoupper(substr($nama, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2; /* Cream soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d; /* Biru Navy */
        }
        .profile-container {
            max-width: 560px;
            margin: 40px auto;
            background-color: #ffffff;
            border: 1px solid #e8e6e1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .profile-header {
            text-align: center;
            padding: 35px 20px;
            background-color: #1b365d; /* Biru Navy */
            color: white;
            border-bottom: 3px solid #f5f2eb;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #f5f2eb; /* Cream */
            color: #1b365d; /* Biru Navy */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            font-weight: bold;
            margin: 0 auto 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .profile-header h2 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            overflow-wrap: anywhere;
            color: #ffffff;
        }
        .role-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            background-color: #f5f2eb; /* Cream */
            color: #198754 !important; /* Warna hijau untuk tulisan role */
        }
        .profile-section {
            padding: 25px;
            border-bottom: 1px solid #f0eee6;
        }
        .profile-section h4 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #1b365d;
            border-bottom: 2px solid #f5f2eb;
            padding-bottom: 8px;
        }
        .profile-row {
            display: grid;
            grid-template-columns: 35% 65%;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .profile-label {
            color: #555555;
            font-weight: 500;
        }
        .profile-value {
            overflow-wrap: anywhere;
            font-weight: 600;
            color: #1b365d;
        }
        .profile-actions {
            padding: 20px 25px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background-color: #fcfbfa;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
            border: none;
        }
        .btn-back:hover {
            background-color: #5a6268;
            color: white;
        }
        .btn-edit {
            background-color: #1b365d; /* Biru Navy */
            color: white;
            border: none;
        }
        .btn-edit:hover {
            background-color: #142845;
            color: white;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="profile-container">

        <!-- Bagian foto profil dan nama -->
        <div class="profile-header">
            <div class="profile-avatar">
                <?= htmlspecialchars($inisial) ?>
            </div>
            <h2><?= $nama_tampil ?></h2>
            <span class="role-badge">
                <?= strtoupper($role_tampil) ?>
            </span>
        </div>

        <!-- Informasi akun -->
        <div class="profile-section">
            <h4>Informasi Akun</h4>
            <div class="profile-row">
                <div class="profile-label">Username</div>
                <div class="profile-value">
                    <?= $username_tampil ?>
                </div>
            </div>
            <div class="profile-row">
                <div class="profile-label">Role</div>
                <div class="profile-value">
                    <?= $role_tampil ?>
                </div>
            </div>
        </div>

        <!-- Data khusus siswa -->
        <?php if ($role == 'siswa') : ?>
            <div class="profile-section">
                <h4>Data Siswa</h4>
                <div class="profile-row">
                    <div class="profile-label">Nama</div>
                    <div class="profile-value"><?= $nama_tampil ?></div>
                </div>
                <div class="profile-row">
                    <div class="profile-label">NIS</div>
                    <div class="profile-value"><?= $nomor_tampil ?></div>
                </div>
                <div class="profile-row">
                    <div class="profile-label">Kelas</div>
                    <div class="profile-value"><?= $kelas_tampil ?></div>
                </div>
                <div class="profile-row">
                    <div class="profile-label">Jenis Kelamin</div>
                    <div class="profile-value"><?= $jk_tampil ?></div>
                </div>
            </div>

        <!-- Data khusus guru -->
        <?php elseif ($role == 'guru') : ?>
            <div class="profile-section">
                <h4>Data Guru</h4>
                <div class="profile-row">
                    <div class="profile-label">Nama</div>
                    <div class="profile-value"><?= $nama_tampil ?></div>
                </div>
                <div class="profile-row">
                    <div class="profile-label">NIP</div>
                    <div class="profile-value"><?= $nomor_tampil ?></div>
                </div>
                <div class="profile-row">
                    <div class="profile-label">Jenis Kelamin</div>
                    <div class="profile-value"><?= $jk_tampil ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tombol bagian bawah -->
    <div class="profile-actions">
    <!-- Tentukan halaman balik sesuai role yang login -->
    <a href="<?= ($role == 'guru') ? 'daftar_guru.php' : 'daftar_siswa.php'; ?>" class="btn btn-secondary btn-back">
        Kembali
    </a>

    <!-- Arahkan ke edit_akun_admin.php khusus admin, selain itu ke edit_akun_siswa_guru.php -->
    <a href="<?= ($role == 'admin') ? 'edit_akun_admin.php' : 'edit_akun_siswa_guru.php'; ?>" class="btn btn-edit">
        Edit Profile
    </a>
</div>
    </div>
</div>

</body>
</html>