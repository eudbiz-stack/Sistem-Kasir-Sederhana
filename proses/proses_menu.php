<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php'; // Panggil koneksi database

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

// Menangani Hapus Data (melalui URL GET)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_menu = (int)$_GET['id'];

    // Mencegah error foreign key (jika menu sudah pernah ditransaksikan)
    // Sebaiknya tidak dihapus permanen, tapi cukup diubah statusnya.
    // Tapi jika memang ingin hapus paksa:
    $hapus = mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu = '$id_menu'");

    if ($hapus) {
        $_SESSION['pesan'] = "Data menu berhasil dihapus.";
        $_SESSION['tipe'] = "sukses";
    } else {
        $_SESSION['pesan'] = "Gagal menghapus! Menu ini mungkin sedang terikat dengan data transaksi historis.";
        $_SESSION['tipe'] = "gagal";
    }

    header("Location: ../modul/master_menu.php");
    exit;
}

// Menangani Tambah & Edit Data (melalui Form POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])) {

    // Bersihkan inputan untuk mencegah SQL Injection
    $aksi       = $_POST['aksi'];
    $nama_menu  = mysqli_real_escape_string($koneksi, trim($_POST['nama_menu']));
    $jenis      = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $status     = mysqli_real_escape_string($koneksi, $_POST['status']);
    $harga      = (int)$_POST['harga'];

    if ($aksi == 'tambah') {
        $query = "INSERT INTO menu (nama_menu, jenis, harga, status) 
                  VALUES ('$nama_menu', '$jenis', '$harga', '$status')";

        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Menu baru berhasil ditambahkan.";
            $_SESSION['tipe'] = "sukses";
        } else {
            $_SESSION['pesan'] = "Gagal menambah menu: " . mysqli_error($koneksi);
            $_SESSION['tipe'] = "gagal";
        }
    } elseif ($aksi == 'edit') {
        $id_menu = (int)$_POST['id_menu'];
        $query = "UPDATE menu SET 
                  nama_menu = '$nama_menu', 
                  jenis = '$jenis', 
                  harga = '$harga', 
                  status = '$status' 
                  WHERE id_menu = '$id_menu'";

        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Data menu berhasil diperbarui.";
            $_SESSION['tipe'] = "sukses";
        } else {
            $_SESSION['pesan'] = "Gagal memperbarui menu: " . mysqli_error($koneksi);
            $_SESSION['tipe'] = "gagal";
        }
    }

    // Kembalikan ke halaman master menu
    header("Location: ../modul/master_menu.php");
    exit;
} else {
    // Jika akses file langsung tanpa POST/GET yang valid
    header("Location: ../modul/master_menu.php");
    exit;
}
