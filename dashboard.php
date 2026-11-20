<?php
require_once 'koneksi.php';

cek_login();

$role = strtolower($_SESSION['role']);
$id_user = (int) $_SESSION['user_id'];

$sapaan = "";
$kotak = [];

// FUNGSI MENGAMBIL JUMLAH DATA
function dashboardJumlah($koneksi, $sql) {
    $query = mysqli_query($koneksi, $sql);

    if (!$query) {
        die("Error Database: " . mysqli_error($koneksi));
    }

    $data = mysqli_fetch_row($query);
    return $data[0] ?? 0;
}

// =====================================
// DASHBOARD ADMIN
// =====================================
if ($role == 'admin') {

    $sapaan = "Selamat Datang, Admin!";

    $kotak = [
        "Jumlah User" => dashboardJumlah($koneksi,
            "SELECT COUNT(*) FROM users"),

        "Jumlah Siswa" => dashboardJumlah($koneksi,
            "SELECT COUNT(*) FROM siswa"),

        "Jumlah Guru" => dashboardJumlah($koneksi,
            "SELECT COUNT(*) FROM guru"),

        "Mata Pelajaran" => dashboardJumlah($koneksi,
            "SELECT COUNT(*) FROM mapel"),

        "Data Mengajar" => dashboardJumlah($koneksi,
            "SELECT COUNT(*) FROM mengajar"),

        "Data Nilai" => dashboardJumlah($koneksi,
            "SELECT COUNT(*) FROM nilai_rapor")
    ];

// =====================================
// DASHBOARD GURU
// =====================================
} elseif ($role == 'guru') {

    $query = mysqli_query($koneksi,
        "SELECT * FROM guru WHERE user_id='$id_user'");

    $guru = mysqli_fetch_assoc($query);

    if ($guru) {

        $id_guru = (int) $guru['id'];

        $sapaan = "Selamat Datang, " . $guru['nama_guru'];

        $kotak = [
            "Mapel Diampu" => dashboardJumlah($koneksi,
                "SELECT COUNT(*) FROM mapel
                 WHERE guru_id='$id_guru'"),

            "Siswa Diajar" => dashboardJumlah($koneksi,
                "SELECT COUNT(DISTINCT mengajar.siswa_id)
                 FROM mengajar
                 JOIN mapel ON mapel.id = mengajar.mapel_id
                 WHERE mapel.guru_id='$id_guru'"),

            "Nilai Diinput" => dashboardJumlah($koneksi,
                "SELECT COUNT(*) FROM nilai_rapor
                 JOIN mapel ON mapel.id = nilai_rapor.mapel_id
                 WHERE mapel.guru_id='$id_guru'")
        ];

    } else {
        $sapaan = "Akun guru belum terhubung dengan data guru.";
    }

// =====================================
// DASHBOARD SISWA
// =====================================
} elseif ($role == 'siswa') {

    $query = mysqli_query($koneksi,
        "SELECT * FROM siswa WHERE user_id='$id_user'");

    $siswa = mysqli_fetch_assoc($query);

    if ($siswa) {

        $id_siswa = (int) $siswa['id'];

        $nama = $siswa['nama_siswa'];
        $kelas = $siswa['kelas'];

        $sapaan = "Selamat Datang, $nama (Kelas $kelas)";

        $kotak = [
            "Mapel dengan Nilai" => dashboardJumlah($koneksi,
                "SELECT COUNT(DISTINCT mapel_id)
                 FROM nilai_rapor
                 WHERE siswa_id='$id_siswa'"),

            "Nilai Masuk" => dashboardJumlah($koneksi,
                "SELECT COUNT(*) FROM nilai_rapor
                 WHERE siswa_id='$id_siswa'"),

            "Rata-rata Nilai" => dashboardJumlah($koneksi,
                "SELECT ROUND(AVG(
                    (nilai_pengetahuan + nilai_keterampilan) / 2
                ))
                FROM nilai_rapor
                WHERE siswa_id='$id_siswa'")
        ];

    } else {
        $sapaan = "Akun siswa belum terhubung dengan data siswa.";
    }

} else {
    die("Role tidak dikenali!");
}

// MEMANGGIL NAVBAR
require_once 'navbar.php';

?>

<!-- TAMPILAN DASHBOARD -->

<div class="container mt-4 mb-5">

    <h1 class="display-5 fw-normal">Dashboard</h1>

    <hr class="border border-dark border-2 opacity-100">

    <!-- SAPAAN -->
    <div class="alert alert-info shadow-sm">
        <h5 class="mb-0">
            <?= htmlspecialchars($sapaan); ?>
        </h5>
    </div>

    <!-- KOTAK STATISTIK -->
    <div class="row">

        <?php foreach ($kotak as $judul => $isi) { ?>

            <div class="col-md-4 col-sm-6 mb-3">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h6 class="text-muted">
                            <?= htmlspecialchars($judul); ?>
                        </h6>

                        <h2 class="fw-bold text-primary">
                            <?= htmlspecialchars((string)$isi); ?>
                        </h2>

                    </div>

                </div>

            </div>

        <?php } ?>

    </div>

</div>

<?php require_once 'footer.php'; ?>