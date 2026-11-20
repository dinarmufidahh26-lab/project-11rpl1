<?php
require 'navbar.php';
require 'koneksi.php'; // Pastikan file koneksi database terhubung

// 1. Ambil ID mapel dari URL (misal: form_edit.php?id=1)
$id_mapel = $_GET['id'] ?? '';

// Jika ID tidak ada di URL, kembalikan ke halaman tabel
if (empty($id_mapel)) {
    echo "<script>alert('ID Mata Pelajaran tidak ditemukan!'); window.location.href='tabel_mapel.php';</script>";
    exit;
}

// Amankan input ID dari SQL Injection
$id_mapel = mysqli_real_escape_string($koneksi, $id_mapel);

// 2. Query untuk mengambil data mapel berdasarkan ID
$sql_mapel = "SELECT * FROM mapel WHERE id = '$id_mapel'";
$query_mapel = mysqli_query($koneksi, $sql_mapel);
$data_mapel = mysqli_fetch_assoc($query_mapel);

// Jika data mapel tidak ditemukan di database
if (!$data_mapel) {
    echo "<script>alert('Data Mata Pelajaran tidak ditemukan di database!'); window.location.href='tabel_mapel.php';</script>";
    exit;
}

// 3. Query untuk mengambil semua data guru (diurutkan berdasarkan nama)
$sql_guru = "SELECT * FROM guru ORDER BY nama ASC";
$query_guru = mysqli_query($koneksi, $sql_guru);

if (!$query_guru) {
    die("Query Error: " . mysqli_error($koneksi));
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Edit Mata Pelajaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>

    <!-- Awal Form Edit Mata Pelajaran -->
    <div class="container mt-4">
      <div class="row">
        <div class="col-md-8 col-lg-6">
          <h3 class="mb-4">Form Edit Mata Pelajaran</h3>
          
          <form action="aksi_edit_mapel.php" method="POST">
            <!-- Input Hidden untuk menyimpan ID Mapel -->
            <input type="hidden" name="id" value="<?= htmlspecialchars($data_mapel['id']); ?>">

            <h5 class="fw-semibold mb-3">Mata Pelajaran</h5>
            
            <!-- Input Kode Mapel -->
            <div class="mb-3">
              <label for="kode_mapel" class="form-label">Kode Mata Pelajaran</label>
              <input type="text" class="form-control" id="kode_mapel" name="kode_mapel" value="<?= htmlspecialchars($data_mapel['kode_mapel']); ?>" required>
            </div>

            <!-- Input Nama Mapel -->
            <div class="mb-3">
              <label for="nama_mapel" class="form-label">Nama Mata Pelajaran</label>
              <input type="text" class="form-control" id="nama_mapel" name="nama_mapel" value="<?= htmlspecialchars($data_mapel['nama_mapel']); ?>" required>
            </div>

            <!-- Select Pilih Guru -->
            <div class="mb-4">
              <label for="guru" class="form-label">Pilih Guru</label>
              <select class="form-select" id="guru" name="id_guru" required>
                <option value="">-- Pilih Guru --</option>
                <?php while ($guru = mysqli_fetch_assoc($query_guru)) : ?>
                  <!-- Cek apakah id guru cocok dengan guru_id pada data_mapel -->
                  <?php $selected = ($guru['id'] == $data_mapel['guru_id']) ? 'selected' : ''; ?>
                  <option value="<?= $guru['id']; ?>" <?= $selected; ?>>
                    <?= htmlspecialchars($guru['nama']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Update Data</button>
              <a href="tabel_mapel.php" class="btn btn-secondary">Batal / Kembali</a>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Akhir Form Edit Mata Pelajaran -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

<?php
require "footer.php";
?>