<?php 
require_once ('navbar.php');
require_once('koneksi.php');//memanggil file koneksi

$sql = "SELECT users.*,
COALESCE(siswa.nama, guru.nama) AS nama
FROM users
LEFT JOIN siswa ON users.id = siswa.user_id
LEFT JOIN guru ON users.id = guru.user_id
ORDER BY users.id DESC"
;

//untuk menjalankan perintah atau sql atau juga query diatasnya
$hasil = mysqli_query($koneksi, $sql);

//mysqli_num_rows digunakan untuk menghitung jumlah baris dari hasil query.
if (mysqli_num_rows($hasil) > 0 ) {
  //jika hasil query memiliki baris atau data,maka lakukan pengulangan untuk mengambil setetiap datanya,dan datanya menjadi array asosiatif
  while($row = mysqli_fetch_assoc($hasil)){
  //$data[] berfungsi untuk menampung data hasil dari while atau pengulngan diatas,yang akan digunakan atau diimplementasi pada tabel html
    $data[] = $row;

  }
} else {
  echo "tidak ada data";
}

?>

<div class="container">

    <!-- awal -->
    <div class="container center">
        <h1 class="fw-normal display-4">Data Siswa</h1>
        <hr class="border border-dark border-2 opacity-100">
        <div class="row align-items-center">
    </div>

    <table class="table">
  <thead>
    <tr>
      <th scope="col" style="width: 10%;">No</th>
      <th scope="col">Username</th>
      <th scope="col">Role</th>
      <th scope="col">Nama</th>
      <th scope="col" style="width: 15%;">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = 1;
    foreach ($data as $user) {
     ?>
      <tr>
       <th scope="row"><?php echo $no++; ?></th>
       <td><?php echo $user['username'] ?></td>
       <td><?php echo $user['role'] ?></td>
       <td><?php echo $user['nama'] ?></td>
       <td>
          <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
          <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</a>
       </td>
      </tr>
    <?php } ?>
  </tbody>
</table>
<?php require "footer.php"?>
