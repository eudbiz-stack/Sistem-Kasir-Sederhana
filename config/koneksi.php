<?php
$host     = "localhost";
$username = "root";
$password = ""; // Kosongkan jika menggunakan XAMPP bawaan
$database = "db_warung_mengkudu";

$koneksi = mysqli_connect($host, $username, $password, $database);

// Periksa Koneksi
if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set zona waktu agar sesuai dengan waktu lokal transaksi
date_default_timezone_set('Asia/Jakarta');
