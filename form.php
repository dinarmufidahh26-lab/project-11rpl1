<?php require "navbar.php" ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Siswa</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2 !important; /* Cream soft */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d; /* Biru Navy */
        }
        
        /* Navbar kustom agar aman & bisa diklik */
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

        /* Styling Card Form */
        .card {
            background: #ffffff !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid #e8e6e1 !important;
        }

        h4 {
            color: #1b365d !important;
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 15px;
        }

        /* Elemen Form Input */
        .form-control {
            border-radius: 6px !important;
            padding: 10px 14px !important;
            border-color: #ced4da !important;
        }
        .form-control:focus {
            border-color: #1b365d !important;
            box-shadow: 0 0 0 0.2rem rgba(27, 54, 93, 0.15) !important;
        }

        .form-label {
            font-weight: 600;
            color: #1b365d !important;
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

<!--container awal-->
<div class="container mt-4 mb-5">

    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card">
                <div class="card-body p-4">

                    <form action="aksi_tambah_user.php" method="POST">

                        <h4>Data Profil Siswa</h4>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="nis" placeholder="Nomor Induk Siswa | Contoh: 12345678">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="nama_siswa" placeholder="Nama Lengkap Siswa | Contoh: Ujang A">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="kelas" placeholder="Kelas Siswa | Contoh: XI-RPL-1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki" value="L" checked>
                                <label class="form-check-label" for="laki">
                                    Laki-laki
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan" value="P">
                                <label class="form-check-label" for="perempuan">
                                    Perempuan
                                </label>
                            </div>
                        </div>
                        <hr class="my-4">

                        <h4>Data Kredensial Akun (Tabel Users)</h4>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="username" placeholder="Username Akun | Contoh: ujang123">
                        </div>
                        <div class="mb-4">
                            <input type="password" class="form-control" name="password" placeholder="Password Akun | Contoh: password123">
                            <input type="hidden" name="role" value="siswa">
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="tabel.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="tambah2" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (isset($_POST['kirim'])): ?>
    <h1>Outout data siswa</h1>

    <?php

    $nis           =   $_POST['nis'];
    $nama_siswa          =   $_POST['nama_siswa'];
    $kelas         = $_POST['kelas'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $username      =   $_POST['username'];
    $password      =   $_POST['password'];

    echo "NIS =" . $nis . "<br>";
    echo "Nama =" . $nama . "<br>";
    echo "Kelas =" . $kelas . "<br>";
    echo "Jenis =" . $jenis_kelamin . "<br><br><br>";
    ?>
<?php endif; ?>
<?php require "footer.php" ?>

<!-- Bootstrap 5 JS Bundle (Agar Navbar & Dropdown Profil Tetap Berfungsi Normal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>