<?php
/** @var mysqli $koneksi */
// FUNGSI: Mengubungkan halaman ini dengan database
require_once 'koneksi.php';

// FUNGSI: Mengambil parameter ID dan Role yang dikirim lewat URL (contoh: edit.php?id=1&role=siswa)
$id   = $_GET['id'] ?? '';
$role = $_GET['role'] ?? '';

// FUNGSI: Menyiapkan variabel kosong untuk menampung data dari database
$user  = null;
$siswa = null;
$guru  = null;

// FUNGSI: Mengambil data akun dari tabel 'users' berdasarkan ID jika ID-nya ada
if (!empty($id)) {
    $queryUser = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$id'");
    $user      = mysqli_fetch_assoc($queryUser);
}

// FUNGSI: Mengambil data profil detail berdasarkan perannya (Siswa atau Guru)
if ($role == 'siswa') {
    // Kalau siswa, ambil data dari tabel 'siswa'
    $querySiswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE user_id = '$id'");
    $siswa      = mysqli_fetch_assoc($querySiswa);
} elseif ($role == 'guru') {
    // Kalau guru, ambil data dari tabel 'guru'
    $queryGuru = mysqli_query($koneksi, "SELECT * FROM guru WHERE user_id = '$id'");
    $guru      = mysqli_fetch_assoc($queryGuru);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data User</title>
    <!-- FUNGSI: Memanggil library Bootstrap 5 agar tampilan form rapi -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <!-- FUNGSI: Mengatur lebar dan posisi kotak form agar di tengah layar -->
        <div class="col-12 col-md-8 col-lg-6">
            
            <div class="card shadow-sm border-0">
                <!-- FUNGSI: Menampilkan judul card dinamis mengikuti role (Siswa/Guru/Admin) -->
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Edit Data <?= ucfirst($role); ?></h5>
                </div>
                <div class="card-body p-4">

                    <!-- =================================================== -->
                    <!-- FUNGSI: Form khusus untuk mengedit data SISWA       -->
                    <!-- =================================================== -->
                    <?php if ($role == 'siswa'): ?>

                        <!-- FUNGSI: Mengirim data form ke 'aksi_edit.php' dengan perintah 'editsiswa' -->
                        <form action="aksi_edit.php?aksi=editsiswa" method="POST">
                            <!-- FUNGSI: Menyimpan ID dan Role tersembunyi untuk dikirim ke proses simpan -->
                            <input type="hidden" name="id_user" value="<?= htmlspecialchars($id); ?>">
                            <input type="hidden" name="role" value="<?= htmlspecialchars($role); ?>">

                            <!-- FUNGSI: Field untuk mengubah Username -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" required>
                            </div>

                            <!-- FUNGSI: Field untuk mengganti Password (bisa dikosongkan jika tidak diganti) -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak mengganti password">
                            </div>

                            <!-- FUNGSI: Field untuk mengubah NIS -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">NIS</label>
                                <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($siswa['nis'] ?? ''); ?>" required>
                            </div>

                            <!-- FUNGSI: Field untuk mengubah Nama Siswa -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Siswa</label>
                                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($siswa['nama_siswa'] ?? ''); ?>" required>
                            </div>

                            <!-- FUNGSI: Field untuk mengubah Kelas -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kelas</label>
                                <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($siswa['kelas'] ?? ''); ?>" required>
                            </div>

                            <!-- FUNGSI: Dropdown untuk memilih Jenis Kelamin siswa -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <!-- Otomatis memilih opsi L/P sesuai data lama di database -->
                                    <option value="L" <?= ($siswa['jenis_kelamin'] ?? '') == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?= ($siswa['jenis_kelamin'] ?? '') == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>

                            <!-- FUNGSI: Tombol Simpan Perubahan dan Batal -->
                            <div class="d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                                <a href="tabel.php" class="btn btn-outline-secondary w-100">Batal</a>
                            </div>
                        </form>

                    <!-- =================================================== -->
                    <!-- FUNGSI: Form khusus untuk mengedit data GURU        -->
                    <!-- =================================================== -->
                    <?php elseif ($role == 'guru'): ?>

                        <!-- FUNGSI: Mengirim data form ke 'aksi_edit.php' dengan perintah 'editguru' -->
                        <form action="aksi_edit.php?aksi=editguru" method="POST">
                            <input type="hidden" name="id_user" value="<?= htmlspecialchars($id); ?>">
                            <input type="hidden" name="role" value="<?= htmlspecialchars($role); ?>">

                            <!-- FUNGSI: Field untuk mengubah Username guru -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" required>
                            </div>

                            <!-- FUNGSI: Field untuk mengubah Password guru -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak mengganti password">
                            </div>

                            <!-- FUNGSI: Field untuk mengubah NIP guru -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">NIP</label>
                                <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($guru['nip'] ?? ''); ?>" required>
                            </div>

                            <!-- FUNGSI: Field untuk mengubah Nama Guru -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Guru</label>
                                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($guru['nama'] ?? ''); ?>" required>
                            </div>

                            <!-- FUNGSI: Dropdown untuk memilih Jenis Kelamin guru -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L" <?= ($guru['jenis_kelamin'] ?? '') == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?= ($guru['jenis_kelamin'] ?? '') == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>

                            <!-- FUNGSI: Tombol Simpan Perubahan dan Batal -->
                            <div class="d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                                <a href="daftar_guru.php" class="btn btn-outline-secondary w-100">Batal</a>
                            </div>
                        </form>

                    <!-- =================================================== -->
                    <!-- FUNGSI: Form khusus untuk mengedit data ADMIN       -->
                    <!-- =================================================== -->
                    <?php elseif ($role == 'admin'): ?>

                        <!-- FUNGSI: Mengirim data form ke 'aksi_edit.php' dengan perintah 'editadmin' -->
                        <form action="aksi_edit.php?aksi=editadmin" method="POST">
                            <input type="hidden" name="id_user" value="<?= htmlspecialchars($id); ?>">
                            <input type="hidden" name="role" value="<?= htmlspecialchars($role); ?>">

                            <!-- FUNGSI: Field untuk mengubah Username admin -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" required>
                            </div>

                            <!-- FUNGSI: Field untuk mengubah Password admin -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak mengganti password">
                            </div>

                            <!-- FUNGSI: Tombol Simpan Perubahan dan Batal -->
                            <div class="d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                                <a href="tabel.php" class="btn btn-outline-secondary w-100">Batal</a>
                            </div>
                        </form>

                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>