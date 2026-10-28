<?php
session_start();
// Proteksi halaman: pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php'; // Panggil koneksi database

// Variabel untuk layouting
$page = 'dashboard';
$title = 'Dashboard Kasir';
$nama_kasir = $_SESSION['nama_lengkap'];

// 1. Mengambil data statistik hari ini
$hari_ini = date('Y-m-d');

// Total Penjualan Hari Ini
$query_sales = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total_penjualan FROM transaksi WHERE DATE(tanggal_transaksi) = '$hari_ini'");
$data_sales = mysqli_fetch_assoc($query_sales);
$total_sales = $data_sales['total_penjualan'] ? $data_sales['total_penjualan'] : 0;

// Jumlah Transaksi Hari Ini
$query_count = mysqli_query($koneksi, "SELECT COUNT(id_transaksi) AS jumlah_transaksi FROM transaksi WHERE DATE(tanggal_transaksi) = '$hari_ini'");
$data_count = mysqli_fetch_assoc($query_count);
$total_count = $data_count['jumlah_transaksi'];

// Rata-rata per Transaksi Hari Ini
$avg_sales = ($total_count > 0) ? ($total_sales / $total_count) : 0;

// Panggil bagian Header dan Sidebar
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<!-- Wrapper Utama -->
<div class="flex-1 flex flex-col overflow-auto">

    <!-- Panggil Topbar / Navbar Atas -->
    <?php include '../includes/topbar.php'; ?>

    <!-- Konten Utama Dashboard -->
    <main class="flex-1 p-6 bg-slate-50">

        <!-- Hero Section -->
        <div class="rounded-xl overflow-hidden mb-6 relative h-40">
            <img class="w-full h-full object-cover" loading="lazy" src="https://images.pexels.com/photos/33633753/pexels-photo-33633753.jpeg?auto=compress&cs=tinysrgb&w=1280" alt="Suasana Warung">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/70 to-transparent flex items-center px-6">
                <div>
                    <p class="text-white text-xl font-bold">Selamat Datang, <?= $nama_kasir; ?>!</p>
                    <p class="text-slate-200 text-sm mt-1">Sistem Kasir Digital Warung Pondok Mengkudu</p>
                </div>
            </div>
        </div>

        <!-- Summary Penjualan Hari Ini -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
                <p class="text-sm text-slate-500 font-medium">Penjualan Hari Ini</p>
                <p class="text-2xl font-bold text-blue-800 mt-1">Rp <?= number_format($total_sales, 0, ',', '.'); ?></p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
                <p class="text-sm text-slate-500 font-medium">Jumlah Transaksi</p>
                <p class="text-2xl font-bold text-slate-800 mt-1"><?= number_format($total_count, 0, ',', '.'); ?></p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
                <p class="text-sm text-slate-500 font-medium">Rata-rata per Transaksi</p>
                <p class="text-2xl font-bold text-green-700 mt-1">Rp <?= number_format($avg_sales, 0, ',', '.'); ?></p>
            </div>
        </div>

        <!-- Tabel Transaksi Terbaru -->
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-slate-800 text-lg">5 Transaksi Terbaru</h3>
                <a href="transaksi.php" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-3 text-sm text-slate-600">
                <?php
                // Mengambil 5 transaksi paling akhir secara keseluruhan (tidak hanya hari ini)
                $query_recent = mysqli_query($koneksi, "
                    SELECT t.no_faktur, t.tanggal_transaksi, t.total_harga, u.nama_lengkap 
                    FROM transaksi t 
                    LEFT JOIN user u ON t.id_user = u.id_user 
                    ORDER BY t.tanggal_transaksi DESC LIMIT 5
                ");

                if (mysqli_num_rows($query_recent) > 0) {
                    while ($row = mysqli_fetch_assoc($query_recent)) {
                        $waktu = date('d M Y, H:i', strtotime($row['tanggal_transaksi']));
                        $total = number_format($row['total_harga'], 0, ',', '.');
                        $kasir = $row['nama_lengkap'] ? $row['nama_lengkap'] : 'Sistem';

                        echo "
                        <div class='flex justify-between items-center py-2 border-b border-slate-50'>
                            <div>
                                <p class='font-medium text-slate-800'>{$row['no_faktur']}</p>
                                <p class='text-xs text-slate-400'>{$waktu} • Kasir: {$kasir}</p>
                            </div>
                            <span class='font-bold text-slate-700'>Rp {$total}</span>
                        </div>
                        ";
                    }
                } else {
                    echo "<p class='text-slate-400 italic text-center py-4'>Belum ada transaksi.</p>";
                }
                ?>
            </div>
        </div>

    </main>
</div>

<!-- Panggil Footer -->
<?php include '../includes/footer.php'; ?>