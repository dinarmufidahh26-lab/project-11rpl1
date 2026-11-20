<?php
// Nyalain session dulu kalau belum aktif biar $_SESSION bisa dipake
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/** @var mysqli $koneksi */
// Panggil file koneksi biar nyambung ke database di folder tugasphp
require_once('koneksi.php');

// Ambil data mengajar terus digabung sama guru, mapel, dan siswa biar datanya lengkap
$sql = "SELECT 
            mengajar.id,
            mengajar.mapel_id,
            guru.nama AS nama_guru,
            siswa.kelas AS kelas,
            mapel.nama_mapel,
            mapel.kode_mapel
        FROM mengajar
        LEFT JOIN guru ON mengajar.guru_id = guru.id
        LEFT JOIN mapel ON mengajar.mapel_id = mapel.id
        LEFT JOIN siswa ON mengajar.siswa_id = siswa.id
        ORDER BY mengajar.id ASC";

$hasil = mysqli_query($koneksi, $sql);
$data = [];

// Tampung hasil query ke array $data biar gampang di-loop di tabel
if ($hasil && mysqli_num_rows($hasil) > 0) {
    while ($row = mysqli_fetch_assoc($hasil)) {
        $data[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Guru Mengajar</title>
    <!-- Panggil Bootstrap 5.3.8 CSS biar konsisten sama navbar dan footer -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- CSS kustom biar warnanya selaras tema navy & cream -->
    <style>
        body {
            background-color: #f7f6f2 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1b365d;
        }

        /* Atur navbar biar aman dan gampang diklik */
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

        /* Bikin dropdown menu tetap jelas tulisannya dan muncul di lapisan atas */
        .navbar .dropdown-menu {
            z-index: 1060 !important;
        }
        .navbar .dropdown-menu .dropdown-item {
            color: #212529 !important;
        }

        /* Gaya card dan tabel */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: #f5f2eb !important;
            border: 1px solid #e2ddd5 !important;
        }

        .card-body {
            background-color: #f5f2eb !important;
            border-radius: 12px;
        }

        .table-custom thead {
            background-color: #1b365d;
            color: #ffffff;
        }
        .table-custom th {
            background-color: #1b365d !important;
            color: #ffffff !important;
            border-color: #142845 !important;
        }
        .table-custom td {
            vertical-align: middle;
            background-color: #ffffff;
        }

        h1 {
            color: #1b365d;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- Panggil navbar atas kalau filenya ada -->
    <?php 
    if (file_exists("navbar.php")) {
        require_once('navbar.php'); 
    }
    ?>

    <!-- Bungkus utama halaman -->
    <div class="container my-4 mb-5">

        <!-- Judul halaman -->
        <div class="text-center mb-4">
            <h1 class="fw-bold fs-2 m-0">Daftar Guru Mengajar</h1>
        </div>

        <!-- Card pembungkus tabel data -->
        <div class="card shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0 table-custom">
                        <thead class="text-center">
                            <tr>
                                <th scope="col" style="width: 8%;">No</th>
                                <th scope="col" style="width: 24%;">Nama Guru</th>
                                <th scope="col" style="width: 15%;">Kelas</th>
                                <th scope="col" style="width: 24%;">Nama Mata Pelajaran</th>
                                <th scope="col" style="width: 14%;">Kode Mapel</th>
                                <th scope="col" style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Cek dulu ada gak isinya di $data
                            if (!empty($data)) {
                                $no = 1; // Nomor urut di tabel
                                foreach ($data as $row) {
                                    // Pakai id asli dari tabel mengajar biar pas diklik edit/hapus ngarah ke baris yang bener
                                    $id_mengajar = (int) $row['id'];
                            ?>
                                <tr>
                                   <!-- Nomor urut -->
                                   <th scope="row" class="text-center"><?php echo $no++; ?></th>
                                   
                                   <!-- Nama guru -->
                                   <td class="fw-semibold">
                                       <?php echo !empty($row['nama_guru']) ? htmlspecialchars($row['nama_guru']) : '<span class="text-muted">Belum ditentukan</span>'; ?>
                                   </td>

                                   <!-- Kelas -->
                                   <td class="text-center">
                                       <?php echo !empty($row['kelas']) ? htmlspecialchars($row['kelas']) : '<span class="text-muted">-</span>'; ?>
                                   </td>
                                   
                                   <!-- Nama mata pelajaran -->
                                   <td>
                                       <?php echo !empty($row['nama_mapel']) ? htmlspecialchars($row['nama_mapel']) : '<span class="text-muted">-</span>'; ?>
                                   </td>
                                   
                                   <!-- Kode mata pelajaran -->
                                   <td class="text-center">
                                       <?php if (!empty($row['kode_mapel'])) { ?>
                                           <span class="badge bg-secondary"><?php echo htmlspecialchars($row['kode_mapel']); ?></span>
                                       <?php } else { ?>
                                           <span class="text-muted">-</span>
                                       <?php } ?>
                                   </td>
                                   
                                   <!-- Tombol edit & hapus pake id dari tabel mengajar -->
                                   <td class="text-center">
                                       <a href="edit_mengajar.php?id=<?php echo $id_mengajar; ?>" class="btn btn-warning btn-sm px-3 mb-1">Edit</a>
                                       <a href="aksi_hapus_mengajar.php?id=<?php echo $id_mengajar; ?>" class="btn btn-danger btn-sm px-2 mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                                   </td>
                                </tr>
                            <?php 
                                } 
                            } else {
                            ?>
                                <!-- Muncul kalau data di tabel mengajar masih kosong -->
                                <tr>
                                  <td colspan="6" class="text-center text-muted py-4">Belum ada data guru mengajar di database.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Panggil footer bawah (sudah memuat Bootstrap JS bundle sekali, jadi gak perlu nambah script Bootstrap lagi di bawahnya) -->
    <?php 
    if (file_exists("footer.php")) {
        require_once "footer.php"; 
    }
    ?>