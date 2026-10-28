<?php
session_start(); // Memulai kembali session untuk bisa menghapusnya

// Menghapus semua variabel session
session_unset();

// Menghancurkan session
session_destroy();

// Mengarahkan kembali ke halaman login
header("Location: login.php");
exit;
