<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php'; // Panggil koneksi database

// Pastikan yang mengakses script ini adalah Admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../modul/dashboard.php");
    exit;
}

// Menangani Hapus Data (GET)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = (int)$_GET['id'];

    // Mencegah admin menghapus dirinya sendiri (Security layer)
    if ($id_hapus == $_SESSION['id_user']) {
        $_SESSION['pesan'] = "Aksi ditolak! Anda tidak bisa menghapus akun yang sedang Anda gunakan.";
        $_SESSION['tipe'] = "gagal";
    } else {
        $hapus = mysqli_query($koneksi, "DELETE FROM user WHERE id_user = '$id_hapus'");
        if ($hapus) {
            $_SESSION['pesan'] = "Akun pengguna berhasil dihapus.";
            $_SESSION['tipe'] = "sukses";
        } else {
            $_SESSION['pesan'] = "Gagal menghapus! Akun ini mungkin sudah melakukan transaksi di masa lalu.";
            $_SESSION['tipe'] = "gagal";
        }
    }
    header("Location: ../modul/manajemen_user.php");
    exit;
}

// Menangani Tambah & Edit Data (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])) {

    $aksi         = $_POST['aksi'];
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $username     = mysqli_real_escape_string($koneksi, strtolower(trim($_POST['username']))); // Username selalu huruf kecil
    $role         = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password_raw = $_POST['password'];

    if ($aksi == 'tambah') {
        // Cek apakah username sudah ada di database
        $cek_username = mysqli_query($koneksi, "SELECT username FROM user WHERE username = '$username'");
        if (mysqli_num_rows($cek_username) > 0) {
            $_SESSION['pesan'] = "Gagal! Username '$username' sudah dipakai, silakan pilih yang lain.";
            $_SESSION['tipe'] = "gagal";
        } else {
            // Enkripsi password sebelum disimpan ke database
            $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

            $query = "INSERT INTO user (username, password, nama_lengkap, role) 
                      VALUES ('$username', '$password_hash', '$nama_lengkap', '$role')";

            if (mysqli_query($koneksi, $query)) {
                $_SESSION['pesan'] = "User baru berhasil ditambahkan.";
                $_SESSION['tipe'] = "sukses";
            }
        }
    } elseif ($aksi == 'edit') {
        $id_user = (int)$_POST['id_user'];

        // Cek username duplicate (Kecuali username miliknya sendiri)
        $cek_username = mysqli_query($koneksi, "SELECT username FROM user WHERE username = '$username' AND id_user != '$id_user'");
        if (mysqli_num_rows($cek_username) > 0) {
            $_SESSION['pesan'] = "Gagal! Username '$username' sudah dipakai orang lain.";
            $_SESSION['tipe'] = "gagal";
        } else {
            // Jika kolom password diisi, update beserta passwordnya (di-hash ulang)
            if (!empty($password_raw)) {
                $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
                $query = "UPDATE user SET nama_lengkap = '$nama_lengkap', username = '$username', role = '$role', password = '$password_hash' WHERE id_user = '$id_user'";
            } else {
                // Jika password dikosongkan, update nama, username & role saja
                $query = "UPDATE user SET nama_lengkap = '$nama_lengkap', username = '$username', role = '$role' WHERE id_user = '$id_user'";
            }

            if (mysqli_query($koneksi, $query)) {
                $_SESSION['pesan'] = "Data pengguna berhasil diperbarui.";
                $_SESSION['tipe'] = "sukses";
            }
        }
    }

    header("Location: ../modul/manajemen_user.php");
    exit;
}
