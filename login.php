<?php
// Memulai session jika belum aktif agar $_SESSION bisa dicek
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Memanggil file koneksi di folder yang sama (tugasphp)
require_once 'koneksi.php';

// Jika sudah login, langsung arahkan ke dashboard.php (diperbaiki dari 5dashboard.php)
if (isset($_SESSION['role'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

// Memproses login jika tombol submit dengan name="login" ditekan
if (isset($_POST['login'])) {
    // Mengamankan input username dan mengambil password dari form
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Mencari user di tabel users berdasarkan username
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $user  = mysqli_fetch_assoc($query);

    // Mengecek password (mendukung password hash maupun teks biasa)
    if ($user && (password_verify($password, $user['password']) || $password == $user['password'])) {
        // Menyimpan data session untuk dipakai di dashboard.php
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2;
            color: #1b365d;
        }
        .login-card {
            width: 100%;
            max-width: 360px;
            background-color: #f5f2eb;
            border: 1px solid #e2ddd5;
        }
        .btn-navy {
            background-color: #1b365d;
            border-color: #1b365d;
            color: #ffffff;
        }
        .btn-navy:hover {
            background-color: #142846;
            border-color: #142846;
            color: #ffffff;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-sm p-4 login-card rounded-3">
        <h3 class="text-center mb-4 fw-bold" style="color: #1b365d;">Login</h3>

        <!-- Menampilkan pesan error jika login gagal -->
        <?php if ($error != "") { ?>
            <div class="alert alert-danger py-2 small" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php } ?>

        <!-- Dikirim ke aksi_login.php dengan metode POST -->
        <form action="aksi_login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label small fw-semibold">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan Username" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
            </div>

            <!-- Ditambahkan name="login" agar dikenali saat disubmit -->
            <button type="submit" name="login" class="btn btn-navy w-100 my-2 fw-semibold">Login</button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">Belum memiliki akun? <a href="form_register.php" class="text-decoration-none fw-semibold" style="color: #1b365d;">Register</a></small>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>