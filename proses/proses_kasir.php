<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php'; // Panggil koneksi database

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// 1. TAMBAH ITEM KE KERANJANG
if ($aksi == 'tambah') {
    $id_menu = (int)$_POST['id_menu'];
    $jumlah = (int)$_POST['jumlah'];

    // Ambil data menu dari database berdasarkan ID
    $query = mysqli_query($koneksi, "SELECT nama_menu, harga FROM menu WHERE id_menu = '$id_menu'");
    $menu = mysqli_fetch_assoc($query);

    if ($menu) {
        // Jika menu sudah ada di keranjang, tambahkan quantity-nya
        if (isset($_SESSION['keranjang'][$id_menu])) {
            $_SESSION['keranjang'][$id_menu]['jumlah'] += $jumlah;
        } else {
            // Jika belum, buat item baru di array
            $_SESSION['keranjang'][$id_menu] = [
                'nama' => $menu['nama_menu'],
                'harga' => $menu['harga'],
                'jumlah' => $jumlah
            ];
        }
    }
    header("Location: ../modul/transaksi.php");
    exit;
}

// 2. HAPUS ITEM DARI KERANJANG
elseif ($aksi == 'hapus') {
    $id_menu = (int)$_GET['id'];
    if (isset($_SESSION['keranjang'][$id_menu])) {
        unset($_SESSION['keranjang'][$id_menu]);
    }
    header("Location: ../modul/transaksi.php");
    exit;
}

// 3. RESET KERANJANG
elseif ($aksi == 'reset') {
    unset($_SESSION['keranjang']);
    header("Location: ../modul/transaksi.php");
    exit;
}

// 4. SIMPAN TRANSAKSI KE DATABASE
elseif ($aksi == 'simpan') {
    if (empty($_SESSION['keranjang'])) {
        header("Location: ../modul/transaksi.php");
        exit;
    }

    $id_user = $_SESSION['id_user'];
    $total_harga = (int)$_POST['total_harga'];
    $uang_bayar = (int)$_POST['uang_bayar'];
    $kembalian = $uang_bayar - $total_harga;

    // Proteksi: Uang bayar tidak boleh kurang
    if ($uang_bayar < $total_harga) {
        $_SESSION['pesan'] = "Uang pembayaran kurang!";
        $_SESSION['tipe'] = "gagal";
        header("Location: ../modul/transaksi.php");
        exit;
    }

    // Generate Nomor Faktur Otomatis (Format: TRX-TahunBulanHariJamMenitDetik)
    // Contoh: TRX-20260601203919
    $no_faktur = 'TRX-' . date('YmdHis');
    $waktu_sekarang = date('Y-m-d H:i:s');

    // Mulai transaksi database (Mencegah data separuh masuk)
    mysqli_begin_transaction($koneksi);

    try {
        // A. Insert ke tabel `transaksi` (Header)
        $query_transaksi = "INSERT INTO transaksi (no_faktur, tanggal_transaksi, total_harga, total_bayar, kembalian, id_user) 
                            VALUES ('$no_faktur', '$waktu_sekarang', '$total_harga', '$uang_bayar', '$kembalian', '$id_user')";
        mysqli_query($koneksi, $query_transaksi);

        // Dapatkan ID transaksi yang baru saja digenerate auto_increment
        $id_transaksi = mysqli_insert_id($koneksi);

        // B. Insert ke tabel `detail_transaksi` (Looping isi keranjang)
        foreach ($_SESSION['keranjang'] as $id_menu => $item) {
            $jumlah = $item['jumlah'];
            $subtotal = $item['harga'] * $jumlah;

            $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_menu, jumlah, subtotal) 
                             VALUES ('$id_transaksi', '$id_menu', '$jumlah', '$subtotal')";
            mysqli_query($koneksi, $query_detail);
        }

        // Jika semua insert berhasil, commit perubahan permanen ke database
        mysqli_commit($koneksi);

        // Kosongkan keranjang setelah berhasil dibayar
        unset($_SESSION['keranjang']);

        $_SESSION['pesan'] = "Transaksi berhasil! Kembalian: Rp " . number_format($kembalian, 0, ',', '.');
        $_SESSION['tipe'] = "sukses";
    } catch (Exception $e) {
        // Jika ada query yang gagal, batalkan semua insert (rollback)
        mysqli_rollback($koneksi);

        $_SESSION['pesan'] = "Gagal memproses transaksi: " . $e->getMessage();
        $_SESSION['tipe'] = "gagal";
    }

    header("Location: ../modul/transaksi.php");
    exit;
}
