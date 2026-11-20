<?php
// Nyalain session dulu kalau belum aktif biar $_SESSION bisa dipake
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil data session username, nama, sama role; kalau kosong pake default
$username = $_SESSION['username'] ?? 'user';
$nama = $_SESSION['nama'] ?? 'admin';
$role = $_SESSION['role'] ?? '';

// Tentukan link Home sesuai role biar gak nyasar
$url_home = 'tabel.php';
if ($role === 'guru') {
    $url_home = 'daftar_guru.php';
} elseif ($role === 'siswa') {
    $url_home = 'daftar_siswa.php'; // Ganti ke file beranda siswa kalau beda nama
}

// Load CSS Bootstrap 5 sekali aja biar halaman kayak formadmin.php gak berantakan
if (empty($GLOBALS['bs_css_loaded'])) {
    $GLOBALS['bs_css_loaded'] = true;
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">';
}
?>

<!-- Komponen Navbar Bootstrap -->
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
    <div class="container-fluid">
        <!-- Brand / Logo Navbar -->
        <a class="navbar-brand fw-bold" href="<?php echo htmlspecialchars($url_home); ?>">Navbar</a>
        
        <!-- Tombol toggle buat layar kecil / HP -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Isi menu navbar -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Menu utama di kiri dibedain berdasarkan role -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <?php if ($role === 'admin'): ?>
                    <!-- ADMIN: tampil semua menu -->
                    <li class="nav-item">
                        <a class="nav-link" href="tabel.php">Home</a>
                    </li>

                    <!-- Dropdown User -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                            User
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="tabel.php">Daftar User</a></li>
                            <li><a class="dropdown-item" href="formuser.php">Daftar Seluruh</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="form.php">Tambah Siswa</a></li>
                            <li><a class="dropdown-item" href="formadmin.php">Tambah Admin</a></li>
                            <li><a class="dropdown-item" href="formuser.php">Tambah Seluruh</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Mapel -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                            Mapel
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="tabel_mapel.php">Daftar Mapel</a></li>
                            <li><a class="dropdown-item" href="form_tambah_mapel.php">Tambah Mapel</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Siswa -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                            Siswa
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="daftar_siswa.php">Daftar Siswa</a></li>
                            <li><a class="dropdown-item" href="form.php">Tambah Siswa</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Guru -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                            Guru
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="daftar_guru.php">Daftar Guru</a></li>
                            <li><a class="dropdown-item" href="form.php">Tambah Guru</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Mengajar -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                            Mengajar
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="daftar_mengajar.php">Daftar Mengajar</a></li>
                            <li><a class="dropdown-item" href="form_tambah_mengajar.php">Tambah Mengajar</a></li>
                        </ul>
                    </li>

                    <!-- Menu Dashboard khusus Admin -->
                    <li class="nav-item">
                        <a class="nav-link" href="dashbord.php">Dashboard</a>
                    </li>

                <?php elseif ($role === 'guru'): ?>
                    <!-- GURU: cuma tampil Home dan Daftar Kelas -->
                    <li class="nav-item">
                        <a class="nav-link" href="daftar_guru.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="daftar_kelas_guru.php">Daftar Kelas</a>
                    </li>

                <?php elseif ($role === 'siswa'): ?>
                    <!-- SISWA: cuma tampil Home dan Nilai Saya -->
                    <li class="nav-item">
                        <a class="nav-link" href="daftar_siswa.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nilai_siswa.php">Nilai Saya</a>
                    </li>

                <?php endif; ?>
            </ul>

            <!-- Search + profil akun di kanan -->
            <div class="d-flex align-items-center gap-3">
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>

                <!-- Dropdown profil -->
<div class="dropdown">
    <a class="nav-link dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
        <?php echo htmlspecialchars($nama); ?> (<?php echo htmlspecialchars($username); ?>)
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
       <li><a class="dropdown-item" href="profile.php">Profil</a></li>
       <!-- Arahkan Pengaturan ke edit_akun_admin.php khusus admin, selain itu ke edit_akun_siswa_guru.php -->
       <li>
           <a class="dropdown-item" href="<?php echo ($role === 'admin') ? 'edit_akun_admin.php' : 'edit_akun_siswa_guru.php'; ?>">
               Pengaturan
           </a>
       </li>
       <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
    </ul>
</div>
        </div>
    </div>
</nav>

