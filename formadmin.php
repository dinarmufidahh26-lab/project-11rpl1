<?php
// Panggil navbar atas dulu biar menu navigasi tampil di halaman tambah admin
require_once "navbar.php";
?>

<!-- Bungkus utama form biar posisinya rapi di tengah layar -->
<div class="container mt-4 mb-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <!-- Card pembungkus form akun admin -->
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <!-- Kirim data inputan ke aksi_tambah_user.php pake metode POST -->
                    <form action="aksi_tambah_user.php" method="POST">

                        <h4 class="mb-3">Data Kredensial Akun (Tabel Users)</h4>

                        <!-- Input username akun admin -->
                        <div class="mb-3">
                            <input type="text" class="form-control" name="username" placeholder="Username Akun | Contoh: ujang123" required>
                        </div>

                        <!-- Input password akun admin + role otomatis admin -->
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Password Akun | Contoh: password123" required>
                            <input type="hidden" name="role" value="admin">
                        </div>

                        <!-- Tombol simpan data dan tombol kembali ke tabel user -->
                        <button type="submit" name="tambah2" class="btn btn-primary">Simpan Data</button>
                        <a href="tabel.php" class="btn btn-secondary">Kembali</a>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php if (isset($_POST['kirim'])): ?>
    <!-- Bagian output kalau form dikirim pake tombol bernama 'kirim' -->
    <div class="container mb-4">
        <h1>Output data users</h1>
        <?php
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        ?>
    </div>
<?php endif; ?>

<?php
// Panggil footer bawah kalau filenya ada
if (file_exists("footer.php")) {
    require_once "footer.php";
}
?>