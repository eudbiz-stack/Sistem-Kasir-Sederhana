<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php'; // Panggil koneksi database

if (!isset($koneksi)) {
    die('Koneksi database gagal: variabel $koneksi tidak ditemukan.');
}

// Cek apakah data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Cari user berdasarkan username
    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password (mencocokkan input dengan hash di database)
        if (password_verify($password, $user['password'])) {

            // Jika berhasil, buat variabel sesi
            $_SESSION['id_user']    = $user['id_user'];
            $_SESSION['username']   = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role']       = $user['role'];

            // Arahkan ke halaman dashboard
            header("Location: ../modul/dashboard.php");
            exit;
        } else {
            // Password salah
            $_SESSION['error'] = "Password yang Anda masukkan salah!";
            header("Location: ../login.php");
            exit;
        }
    } else {
        // Username tidak ditemukan
        $_SESSION['error'] = "Username tidak ditemukan!";
        header("Location: ../login.php");
        exit;
    }
} else {
    // Jika ada yang mencoba akses file ini langsung dari URL
    header("Location: ../login.php");
    exit;
}
