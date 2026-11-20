<?php
// Hubungin halaman ini ke file koneksi database
require_once 'koneksi.php';

// Nyalain session dulu kalau belum aktif biar $_SESSION bisa dipake
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login, kalau belum arahin ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil username yang lagi login sekarang
$usernameLogin = $_SESSION['username'];

// Cari data akun admin di tabel users sesuai username yang login
$stmt = mysqli_prepare($koneksi, "
    SELECT id, username, role
    FROM users
    WHERE username = ? AND role = 'admin'
");
mysqli_stmt_bind_param($stmt, "s", $usernameLogin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

// Tolak akses kalau bukan akun admin
if (!$admin) {
    http_response_code(403);
    exit("Halaman ini khusus admin.");
}

// Simpen id dan username admin ke variabel
$id = $admin['id'];
$username = $admin['username'];
$pesan = "";

// Jalanin proses update pas tombol "Simpan Data" diklik
if (isset($_POST['simpan'])) {
    // Ambil input username dan password baru dari form
    $usernameBaru = trim($_POST['username']);
    $passwordBaru = $_POST['password'];

    // Pastiin username gak dikosongin
    if ($usernameBaru === '') {
        $pesan = "Username tidak boleh kosong.";
    } else {
        // Cek apakah username baru sudah dipake akun lain selain admin ini
        $cek = mysqli_prepare($koneksi, "
            SELECT id FROM users
            WHERE username = ? AND id != ?
        ");
        mysqli_stmt_bind_param($cek, "si", $usernameBaru, $id);
        mysqli_stmt_execute($cek);
        $hasilCek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasilCek) > 0) {
            $pesan = "Username sudah digunakan.";
        } else {
            // Update username admin di database
            $update = mysqli_prepare($koneksi, "
                UPDATE users
                SET username = ?
                WHERE id = ? AND role = 'admin'
            ");
            mysqli_stmt_bind_param($update, "si", $usernameBaru, $id);

            if (mysqli_stmt_execute($update)) {
                // Kalau kolom password diisi, hash dulu lalu update ke database
                if ($passwordBaru !== '') {
                    $passwordHash = password_hash(
                        $passwordBaru,
                        PASSWORD_DEFAULT
                    );

                    $ubahPassword = mysqli_prepare($koneksi, "
                        UPDATE users
                        SET password_hash = ?
                        WHERE id = ? AND role = 'admin'
                    ");
                    mysqli_stmt_bind_param(
                        $ubahPassword,
                        "si",
                        $passwordHash,
                        $id
                    );

                    if (!mysqli_stmt_execute($ubahPassword)) {
                        $pesan = "Username berubah, tetapi password gagal diubah.";
                    }
                }

                // Samain username di session biar gak kepental/mismatch
                $_SESSION['username'] = $usernameBaru;

                // Kalau semua aman tanpa pesan error, balik ke halaman profil
                if ($pesan === '') {
                    header("Location: profile.php");
                    exit;
                }

                $username = $usernameBaru;
            } else {
                $pesan = "Gagal memperbarui akun.";
            }
        }
    }

    // Pertahanin ketikan username biar gak hilang kalau ada error
    $username = $usernameBaru;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Atur encoding karakter -->
    <meta charset="UTF-8">
    <!-- Biar responsif di HP maupun laptop -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Edit User (Admin)</title>
    <!-- Panggil Bootstrap 5.3.8 CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        crossorigin="anonymous"
    >
    <style>
        /* Latar belakang cream lembut dan teks utama navy */
        body {
            background-color: #f7f6f2 !important;
            color: #1b365d;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Bingkai utama form edit user dengan nuansa cream & navy */
        .edit-card {
            border: 1px solid #d8d2c5;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(27, 54, 93, 0.06);
            background-color: #f5f2eb;
        }

        /* Header kartu berwarna biru navy dengan teks putih */
        .edit-card-header {
            background-color: #1b365d;
            border-bottom: 1px solid #142845;
            padding: 14px 20px;
            font-size: 1.05rem;
            font-weight: 600;
            color: #ffffff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        /* Ruang dalam isi kartu berwarna cream */
        .edit-card-body {
            background-color: #f5f2eb;
            padding: 22px 24px 26px 24px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Judul bagian kredensial akun */
        .section-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #1b365d;
            margin-bottom: 18px;
        }

        /* Label input form */
        .form-label-custom {
            font-size: 0.92rem;
            font-weight: 600;
            color: #1b365d;
            margin-bottom: 6px;
        }

        /* Kotak input putih bersih dengan garis batas cream keabu-abuan */
        .form-control-custom {
            border: 1px solid #d5cfc4;
            border-radius: 6px;
            padding: 9px 12px;
            font-size: 0.95rem;
            color: #1b365d;
            background-color: #ffffff;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        /* Efek fokus warna navy saat input diklik */
        .form-control-custom:focus {
            border-color: #1b365d;
            box-shadow: 0 0 0 0.2rem rgba(27, 54, 93, 0.16);
            outline: 0;
        }

        /* Tombol simpan utama berwarna navy */
        .btn-simpan {
            background-color: #1b365d;
            border-color: #1b365d;
            color: #ffffff;
            border-radius: 6px;
            padding: 7px 16px;
            font-size: 0.92rem;
            font-weight: 600;
        }

        .btn-simpan:hover,
        .btn-simpan:focus {
            background-color: #142845;
            border-color: #142845;
            color: #ffffff;
        }

        /* Tombol kembali pelengkap bernuansa netral serasi */
        .btn-kembali {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #ffffff;
            border-radius: 6px;
            padding: 7px 16px;
            font-size: 0.92rem;
            font-weight: 600;
        }

        .btn-kembali:hover,
        .btn-kembali:focus {
            background-color: #565e64;
            border-color: #565e64;
            color: #ffffff;
        }
    </style>
</head>
<body>

<!-- Wadah utama form di tengah halaman -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            <!-- Kartu form edit user admin -->
            <div class="card edit-card">
                <div class="edit-card-header">
                    Form Edit User (Admin)
                </div>

                <div class="edit-card-body">
                    <!-- Judul bagian kredensial -->
                    <h2 class="section-title">Data Kredensial Akun</h2>

                    <!-- Tampilin pesan kalau ada validasi atau error -->
                    <?php if ($pesan !== '') { ?>
                        <div class="alert alert-warning py-2" role="alert">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <!-- Form kirim data ke halaman ini sendiri -->
                    <form method="POST">
                        <!-- Input username -->
                        <div class="mb-3">
                            <label for="username" class="form-label form-label-custom">Username:</label>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-control form-control-custom"
                                value="<?= htmlspecialchars($username); ?>"
                                required
                            >
                        </div>

                        <!-- Input password baru -->
                        <div class="mb-3">
                            <label for="password" class="form-label form-label-custom">Password Akun Baru:</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control form-control-custom"
                                placeholder="Password baru | jika tidak diubah, kosongkan"
                            >
                        </div>

                        <!-- Tombol simpan data dan kembali -->
                        <div class="d-flex gap-2 pt-1">
                            <button type="submit" name="simpan" class="btn btn-primary btn-simpan">
                                Simpan Data
                            </button>
                            <a href="daftar_siswa.php" class="btn btn-secondary btn-kembali">
                                Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Panggil footer kalau ada (sudah mengurus Bootstrap JS bundle) -->
<?php
if (file_exists("footer.php")) {
    require_once "footer.php";
}
?>
</body>
</html>