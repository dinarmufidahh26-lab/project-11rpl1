<?php
// Memulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memanggil koneksi database
require_once 'koneksi.php';


// Mengambil data guru dari tabel SQL guru
$sql = "SELECT * FROM guru ORDER BY id DESC";
$query = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Guru</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
<div class="container mt-5">
  <h2 class="text-center mb-4">Daftar Data Guru</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">

            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>NIP</th>
                    <th>Nama Guru</th>
                    <th>Jenis Kelamin</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if ($query && mysqli_num_rows($query) > 0) {
                    $no = 1;

                    while ($guru = mysqli_fetch_assoc($query)) {
                ?>
                   <a>
                     <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($guru['id']); ?></td>
                            <td><?= htmlspecialchars($guru['nip']); ?></td>
                            <td><?= htmlspecialchars($guru['nama']); ?></td>
                            <td><?= htmlspecialchars($guru['jenis_kelamin']); ?></td>

                            <td> 
                                
                                <a href="aksi_edit_.php?id=<?= $guru['id']; ?>"
                                   class="btn btn-warning btn-sm">Edit</a>

                                <a href="aksi_delete.php?id=<?= $guru['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus data guru?')">
                                   Remove</a>
                               </a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">
                          Belum ada data guru.
                          </td></tr>';
                }
                ?>
            </tbody>

        </table>
    </div>

</div>

</body>
</html>