<?php
// Panggil dulu file koneksi biar nyambung ke database
require_once 'koneksi.php';

// Ambil id data mengajar dari URL, kalau gak ada baru cek dari input hidden pas form di-submit
$id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);

// Ambil data lama di tabel mengajar sesuai id-nya
$q_mengajar = mysqli_query($koneksi, "SELECT * FROM mengajar WHERE id = $id LIMIT 1");
$mengajar   = mysqli_fetch_assoc($q_mengajar);

// Ambil daftar kelas unik dari tabel siswa biar gak double di dropdown
$kelas_list = [];
$q_kelas = mysqli_query($koneksi, "SELECT DISTINCT kelas FROM siswa WHERE kelas IS NOT NULL AND kelas != '' ORDER BY kelas ASC");
if ($q_kelas) {
    while ($row = mysqli_fetch_assoc($q_kelas)) {
        $kelas_list[] = $row['kelas'];
    }
}

// Ambil daftar mapel sekalian sama nama gurunya buat isi pilihan mapel
$mapel_list = [];
$q_mapel = mysqli_query(
    $koneksi,
    "SELECT mapel.id, mapel.nama_mapel, mapel.guru_id, guru.nama AS nama_guru
     FROM mapel
     LEFT JOIN guru ON mapel.guru_id = guru.id
     ORDER BY mapel.id ASC"
);
if ($q_mapel) {
    while ($row = mysqli_fetch_assoc($q_mapel)) {
        $mapel_list[] = $row;
    }
}

$pesan_error = '';

// Jalanin proses update kalau tombol "Simpan Perubahan" diklik
if (isset($_POST['simpan'])) {
    // Amankan input kelas sama id mapel yang dipilih user
    $kelas_pilih = mysqli_real_escape_string($koneksi, $_POST['kelas'] ?? '');
    $mapel_pilih = (int) ($_POST['mapel_id'] ?? 0);

    // Cari siswa_id yang kelasnya cocok sama pilihan baru
    $siswa_id = 0;
    if ($kelas_pilih !== '') {
        $q_s = mysqli_query($koneksi, "SELECT id FROM siswa WHERE kelas = '$kelas_pilih' ORDER BY id ASC LIMIT 1");
        if ($d_s = mysqli_fetch_assoc($q_s)) {
            $siswa_id = (int) $d_s['id'];
        }
    }

    // Kasih peringatan kalau kelas yang dipilih ternyata belum ada siswanya, biar gak diam-diam balik ke kelas lama
    if ($siswa_id <= 0) {
        $pesan_error = "Belum ada data siswa di kelas '$kelas_pilih'. Pastikan ada siswa dengan kelas '$kelas_pilih' di tabel siswa.";
    } elseif ($id <= 0) {
        $pesan_error = "ID data mengajar tidak terbaca.";
    } else {
        // Ambil guru_id ngikutin mapel yang dipilih
        $guru_id = (int) ($mengajar['guru_id'] ?? 0);
        if ($mapel_pilih > 0) {
            $q_g = mysqli_query($koneksi, "SELECT guru_id FROM mapel WHERE id = $mapel_pilih LIMIT 1");
            if ($d_g = mysqli_fetch_assoc($q_g)) {
                $guru_id = (int) ($d_g['guru_id'] ?? 0);
            }
        }

        // Update baris di tabel mengajar sesuai id-nya
        $update = mysqli_query(
            $koneksi,
            "UPDATE mengajar 
             SET siswa_id = '$siswa_id', guru_id = '$guru_id', mapel_id = '$mapel_pilih' 
             WHERE id = $id"
        );

        // Kalau sukses langsung lempar balik ke daftar_mengajar.php
        if ($update) {
            header("Location: daftar_mengajar.php");
            exit;
        } else {
            $pesan_error = "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    }
}

// Tentukan kelas mana yang kepilih / tampil di dropdown saat ini
$kelas_aktif = $_POST['kelas'] ?? '';
if ($kelas_aktif === '' && !empty($mengajar['siswa_id'])) {
    $q_ks = mysqli_query($koneksi, "SELECT kelas FROM siswa WHERE id = " . (int) $mengajar['siswa_id'] . " LIMIT 1");
    if ($d_ks = mysqli_fetch_assoc($q_ks)) {
        $kelas_aktif = $d_ks['kelas'] ?? '';
    }
}

// Tentukan mapel mana yang kepilih saat ini
$mapel_aktif = isset($_POST['mapel_id']) ? (int) $_POST['mapel_id'] : (int) ($mengajar['mapel_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mengajar</title>
    <!-- Panggil Bootstrap 5 biar tampilannya rapi -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .box-edit {
            max-width: 740px;
            margin: 50px auto;
        }
        .card {
            border-radius: 6px;
            border-color: #dee2e6;
        }
        .card-header {
            background-color: #f1f3f5;
            border-bottom: 1px solid #dee2e6;
            padding: 14px 22px;
        }
        .card-body {
            background-color: #ffffff;
            padding: 22px;
        }
    </style>
</head>
<body>

<div class="container box-edit">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0 fw-normal">Edit Data Mengajar</h4>
        </div>
        <div class="card-body">
            <!-- Munculin notif error kalau ada yang kurang pas -->
            <?php if ($pesan_error !== '') { ?>
                <div class="alert alert-warning"><?= htmlspecialchars($pesan_error) ?></div>
            <?php } ?>

            <!-- Sertain ?id= di action sama input hidden biar id-nya gak ilang pas diklik simpan -->
            <form method="POST" action="edit_mengajar.php?id=<?= (int) $id ?>">
                <input type="hidden" name="id" value="<?= (int) $id ?>">

                <div class="mb-3">
                    <label for="kelas" class="form-label">Pilih Kelas</label>
                    <select name="kelas" id="kelas" class="form-select" required>
                        <?php if (!empty($kelas_list)) { ?>
                            <?php foreach ($kelas_list as $kls) { ?>
                                <option value="<?= htmlspecialchars($kls) ?>" <?= ($kls == $kelas_aktif) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kls) ?>
                                </option>
                            <?php } ?>
                        <?php } else { ?>
                            <!-- Opsi cadangan kalau tabel siswa belum punya data kelas -->
                            <option value="XI RPL 1" selected>XI RPL 1</option>
                            <option value="XI RPL 2">XI RPL 2</option>
                            <option value="X RPL 2">X RPL 2</option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="mapel_id" class="form-label">Pilih Mata Pelajaran</label>
                    <select name="mapel_id" id="mapel_id" class="form-select" required>
                        <?php if (!empty($mapel_list)) { ?>
                            <?php foreach ($mapel_list as $mpl) {
                                $teks = $mpl['nama_mapel'] . (!empty($mpl['nama_guru']) ? ' | ' . $mpl['nama_guru'] : '');
                            ?>
                                <option value="<?= (int) $mpl['id'] ?>" <?= ((int) $mpl['id'] == $mapel_aktif) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($teks) ?>
                                </option>
                            <?php } ?>
                        <?php } else { ?>
                            <!-- Opsi cadangan kalau data mapel kosong -->
                            <option value="1" selected>Bahasa Inggris | Ahmad Setiawan S.Pd</option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Tombol simpan dan balik -->
                <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
                <a href="daftar_mengajar.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>