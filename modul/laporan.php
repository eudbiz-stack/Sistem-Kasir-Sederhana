<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php'; // Panggil koneksi database

$page = 'laporan';
$title = 'Laporan Penjualan';

// Menangkap nilai filter tanggal dari URL (jika ada)
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01'); // Default: Awal bulan ini
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-d'); // Default: Hari ini

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col overflow-auto w-full">

    <div class="print:hidden">
        <?php include __DIR__ . '/../includes/topbar.php'; ?>
    </div>

    <main class="flex-1 p-6 bg-slate-50 print:bg-white print:p-0">

        <div class="hidden print:block text-center mb-6">
            <h2 class="text-2xl font-bold text-slate-800">WARUNG PONDOK MENGKUDU</h2>
            <p class="text-sm text-slate-600">Jl. Mengkudu, Sukaramai, Binjai Barat</p>
            <h3 class="text-lg font-semibold mt-4">Laporan Penjualan</h3>
            <p class="text-sm">Periode: <?= date('d M Y', strtotime($tgl_mulai)) ?> s.d <?= date('d M Y', strtotime($tgl_selesai)) ?></p>
            <hr class="mt-4 border-slate-800 border-t-2">
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-6 print:hidden">
            <form action="" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" value="<?= $tgl_mulai ?>" required class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-full sm:w-auto">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" value="<?= $tgl_selesai ?>" required class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-full sm:w-auto">
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex-1 sm:flex-none">
                        Tampilkan
                    </button>
                    <button type="button" onclick="window.print()" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex-1 sm:flex-none flex justify-center items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i> Cetak
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden print:shadow-none print:border-none">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left print:text-xs">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 print:bg-white print:border-b-2 print:border-slate-800">
                        <tr>
                            <th class="px-5 py-3 font-medium">No</th>
                            <th class="px-5 py-3 font-medium">No. Faktur</th>
                            <th class="px-5 py-3 font-medium">Tanggal & Waktu</th>
                            <th class="px-5 py-3 font-medium">Kasir</th>
                            <th class="px-5 py-3 font-medium text-right">Total Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query mengambil data transaksi berdasarkan rentang tanggal
                        // Menggunakan fungsi DATE() untuk mengabaikan jam/menit/detik saat pemfilteran
                        $query_laporan = "
                            SELECT t.no_faktur, t.tanggal_transaksi, t.total_harga, u.nama_lengkap 
                            FROM transaksi t 
                            LEFT JOIN user u ON t.id_user = u.id_user 
                            WHERE DATE(t.tanggal_transaksi) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
                            ORDER BY t.tanggal_transaksi ASC
                        ";

                        $result = mysqli_query($koneksi, $query_laporan);
                        $no = 1;
                        $grand_total = 0;

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $grand_total += $row['total_harga'];
                                $waktu = date('d M Y, H:i', strtotime($row['tanggal_transaksi']));
                                $total = number_format($row['total_harga'], 0, ',', '.');
                                $nama_kasir = $row['nama_lengkap'] ? $row['nama_lengkap'] : 'Sistem';

                                echo "
                                <tr class='border-b border-slate-50 print:border-slate-200'>
                                    <td class='px-5 py-3 text-slate-600'>{$no}</td>
                                    <td class='px-5 py-3 font-medium text-slate-800'>{$row['no_faktur']}</td>
                                    <td class='px-5 py-3 text-slate-600'>{$waktu}</td>
                                    <td class='px-5 py-3 text-slate-600'>{$nama_kasir}</td>
                                    <td class='px-5 py-3 font-medium text-slate-800 text-right'>Rp {$total}</td>
                                </tr>
                                ";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='5' class='px-5 py-8 text-center text-slate-400 italic'>Tidak ada data transaksi pada periode ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 print:bg-white print:border-t-2 print:border-slate-800">
                        <tr>
                            <th colspan="4" class="px-5 py-4 text-right font-bold text-slate-800 text-base">TOTAL PENDAPATAN:</th>
                            <th class="px-5 py-4 text-right font-bold text-blue-800 text-base">Rp <?= number_format($grand_total, 0, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </main>
</div>

<?php include '../includes/footer.php'; ?>