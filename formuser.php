<?php 
// untuk Memanggil file navbar.php untuk menampilkan menu navigasi di bagian atas
require "navbar.php" 
?>

<style>
    /*untuk Sembunyikan input khusus siswa, guru, dan jenis kelamin secara bawaan */
    .box-siswa,
    .box-guru,
    .box-jk {
        display: none;
    }

    /* Jika opsi 'Siswa' dipilih pada dropdown role, tampilkan input khusus siswa dan jenis kelamin */
    .card-body:has(#role option[value="siswa"]:checked) .box-siswa,
    .card-body:has(#role option[value="siswa"]:checked) .box-jk {
        display: block;
    }

    /*  Jika opsi 'Guru' dipilih pada dropdown role, tampilkan input khusus guru dan jenis kelamin */
    .card-body:has(#role option[value="guru"]:checked) .box-guru,
    .card-body:has(#role option[value="guru"]:checked) .box-jk {
        display: block;
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <!-- Mengatur lebar form agar berada di tengah layar (lebar 6 kolom Bootstrap) -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">

                    <!-- Form pendaftaran yang datanya akan dikirim ke 'aksi_tambah_user.php' dengan metode POST -->
                    <form action="aksi_tambah_user.php" method="POST">
                        <h4>Data Kredensial Akun (Tabel Users)</h4>

                        <!-- Input untuk Username (Wajib diisi) -->
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Username Akun | Contoh: ujang123" required>
                        </div>
                        
                        <!-- Input untuk Password (Wajib diisi) -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password Akun | Contoh: password123" required>
                        </div>

                        <!-- Dropdown Pilihan Role (Siswa, Guru, atau Admin) -->
                        <div class="mb-3">
                            <label for="role" class="form-label">Pilih Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" selected disabled>Pilih Role</option>
                                <option value="siswa">Siswa</option>
                                <option value="guru">Guru</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <!-- INPUT KHUSUS SISWA (Muncul hanya jika role 'siswa' dipilih) -->
                        <div class="mb-3 box-siswa">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" class="form-control" name="nama_siswa" placeholder="Masukkan nama siswa">
                        </div>

                        <div class="mb-3 box-siswa">
                            <label class="form-label">NIS</label>
                            <input type="text" class="form-control" name="nis" placeholder="Masukkan NIS">
                        </div>

                        <div class="mb-3 box-siswa">
                            <label class="form-label">Kelas</label>
                            <input type="text" class="form-control" name="kelas" placeholder="Contoh: XII RPL 1">
                        </div>

                        <!-- INPUT KHUSUS GURU (Muncul hanya jika role 'guru' dipilih) -->
                        <div class="mb-3 box-guru">
                            <label class="form-label">Nama Guru</label>
                            <input type="text" class="form-control" name="nama" placeholder="Masukkan nama guru">
                        </div>

                        <div class="mb-3 box-guru">
                            <label class="form-label">NIP</label>
                            <input type="text" class="form-control" name="nip" placeholder="Masukkan NIP">
                        </div>

                        <!-- INPUT JENIS KELAMIN (Muncul jika role 'siswa' atau 'guru' dipilih) -->
                        <div class="mb-3 box-jk">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-select" name="jenis_kelamin">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <!-- Tombol untuk mengirim data atau kembali ke halaman tabel -->
                        <button type="submit" name="tambah2" class="btn btn-primary">Simpan Data</button>
                        <a href="tabel.php" class="btn btn-secondary">Kembali</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Memanggil file footer.php untuk menutup struktur halaman bawah
require "footer.php" 
?>
