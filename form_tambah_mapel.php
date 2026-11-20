<?php
require_once 'koneksi.php';

// Ambil semua data guru dari tabel guru untuk opsi dropdown
$sql_guru = "SELECT * FROM guru ORDER BY nama ASC"; 
$query_guru = mysqli_query($koneksi, $sql_guru);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Tambah Mata Pelajaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2 !important; /* Cream soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d; /* Biru Navy */
        }
        
        /* Navbar kustom agar z-index aman dan bisa diklik */
        nav.navbar, .navbar {
            background-color: #1b365d !important;
            position: relative;
            z-index: 1050 !important;
        }
        .navbar .navbar-brand,
        .navbar .nav-link,
        .navbar .navbar-text {
            color: #ffffff !important;
        }

        /* Card Pembungkus Form Utama */
        .form-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 35px;
            border: 1px solid #e8e6e1;
            margin-top: 30px;
            margin-bottom: 40px;
        }

        h3 {
            color: #1b365d !important;
            font-weight: 700;
        }

        h5 {
            color: #1b365d !important;
        }

        .form-label {
            font-weight: 600;
            color: #1b365d !important;
        }

        .form-control, .form-select {
            border-radius: 6px !important;
            padding: 10px 14px !important;
            border-color: #ced4da !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1b365d !important;
            box-shadow: 0 0 0 0.2rem rgba(27, 54, 93, 0.15) !important;
        }

        /* Tombol Aksi */
        .btn-primary {
            background-color: #1b365d !important;
            border-color: #1b365d !important;
            padding: 10px 20px !important;
            border-radius: 6px !important;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #142845 !important;
            border-color: #142845 !important;
        }

        .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            padding: 10px 20px !important;
            border-radius: 6px !important;
            font-weight: 600;
        }
        .btn-secondary:hover {
            background-color: #5a6268 !important;
        }
    </style>
  </head>
  <body>

    <!-- Panggil navbar di atas agar menu & profil bisa diklik -->
    <?php require_once 'navbar.php'; ?>

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
          <div class="form-card">
              
              <h3 class="mb-3">Form Tambah Mata Pelajaran</h3>
              <hr class="mb-4" style="border-color: #1b365d; opacity: 0.75;">
              
              <form action="aksi_tambah_mapel.php" method="POST">
                <h5 class="fw-semibold mb-3">Mata Pelajaran</h5>
                
                <div class="mb-3">
                  <label for="kode_mapel" class="form-label">Kode Mata Pelajaran</label>
                  <input type="text" class="form-control" id="kode_mapel" name="kode_mapel" required>
                </div>

                <div class="mb-3">
                  <label for="nama_mapel" class="form-label">Nama Mata Pelajaran</label>
                  <input type="text" class="form-control" id="nama_mapel" name="nama_mapel" required>
                </div>

                <div class="mb-4">
                  <label for="guru_id" class="form-label">Pilih Guru</label>
                  <!-- PERBAIKAN: Ubah name="guru" jadi name="guru_id" -->
                  <select class="form-select" id="guru_id" name="guru_id" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php 
                    while ($guru = mysqli_fetch_assoc($query_guru)) : 
                    ?>
                      <!-- Pastikan 'id' dan 'nama' sesuai dengan nama kolom di tabel guru -->
                      <option value="<?= $guru['id']; ?>">
                        <?= htmlspecialchars($guru['nama']); ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">Simpan Data</button>
                  <a href="tabel_mapel.php" class="btn btn-secondary">Kembali</a>
                </div>
              </form>

          </div>
        </div>
      </div>
    </div>

    <!-- Panggil footer di bagian bawah jika ada -->
    <?php 
    if (file_exists("footer.php")) {
        require_once "footer.php"; 
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>