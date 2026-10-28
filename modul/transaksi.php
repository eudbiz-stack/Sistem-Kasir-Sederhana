<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php'; // Panggil koneksi database

$page = 'transaksi';
$title = 'Kasir - Transaksi Baru';

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col overflow-auto">
    <?php include __DIR__ . '/../includes/topbar.php'; ?>

    <main class="flex-1 p-6 bg-slate-50">

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="mb-4 px-4 py-3 rounded text-sm <?= $_SESSION['tipe'] == 'sukses' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= $_SESSION['pesan']; ?>
            </div>
            <?php unset($_SESSION['pesan'], $_SESSION['tipe']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1 bg-white rounded-xl shadow-sm p-5 border border-slate-100 h-fit">
                <h3 class="font-semibold text-slate-800 mb-4">Pilih Menu</h3>
                <form action="../proses/proses_kasir.php?aksi=tambah" method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Daftar Menu</label>
                        <select name="id_menu" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" required>
                            <option value="">-- Pilih Makanan/Minuman --</option>
                            <?php
                            $query_menu = mysqli_query($koneksi, "SELECT * FROM menu WHERE status = 'tersedia' ORDER BY jenis ASC, nama_menu ASC");
                            while ($menu = mysqli_fetch_assoc($query_menu)) {
                                $harga_format = number_format($menu['harga'], 0, ',', '.');
                                echo "<option value='{$menu['id_menu']}'>{$menu['nama_menu']} - Rp {$harga_format}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                        <input type="number" name="jumlah" min="1" value="1" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white py-2 rounded-lg text-sm font-medium transition">
                        Tambah ke Pesanan
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5 border border-slate-100 flex flex-col">
                <h3 class="font-semibold text-slate-800 mb-4">Keranjang Pesanan</h3>

                <div class="flex-1 overflow-auto border-b border-slate-100 mb-4 min-h-[200px]">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-3 py-2">Menu</th>
                                <th class="px-3 py-2">Harga</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_belanja = 0;
                            if (!empty($_SESSION['keranjang'])) {
                                foreach ($_SESSION['keranjang'] as $id => $item) {
                                    $subtotal = $item['harga'] * $item['jumlah'];
                                    $total_belanja += $subtotal;
                                    echo "
                                    <tr class='border-b border-slate-50'>
                                        <td class='px-3 py-2 font-medium'>{$item['nama']}</td>
                                        <td class='px-3 py-2'>Rp " . number_format($item['harga'], 0, ',', '.') . "</td>
                                        <td class='px-3 py-2 text-center'>{$item['jumlah']}</td>
                                        <td class='px-3 py-2 text-right font-medium'>Rp " . number_format($subtotal, 0, ',', '.') . "</td>
                                        <td class='px-3 py-2 text-center'>
                                            <a href='../proses/proses_kasir.php?aksi=hapus&id={$id}' class='text-red-500 hover:text-red-700 font-bold'>&times;</a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4 text-slate-400 italic'>Keranjang masih kosong.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <form action="../proses/proses_kasir.php?aksi=simpan" method="POST" class="mt-auto">
                    <input type="hidden" name="total_harga" value="<?= $total_belanja ?>">

                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg font-bold text-slate-700">Total Tagihan:</span>
                        <span class="text-2xl font-bold text-blue-800">Rp <?= number_format($total_belanja, 0, ',', '.') ?></span>
                    </div>

                    <div class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Uang Diterima (Rp)</label>
                            <input type="number" name="uang_bayar" min="<?= $total_belanja ?>" required <?= empty($_SESSION['keranjang']) ? 'disabled' : '' ?> class="w-full border border-slate-300 rounded-lg px-3 py-2 text-lg font-medium text-green-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <button type="submit" <?= empty($_SESSION['keranjang']) ? 'disabled' : '' ?> class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-slate-300 text-white rounded-lg text-lg font-medium transition h-[42px]">
                            Proses & Bayar
                        </button>
                        <a href="../proses/proses_kasir.php?aksi=reset" onclick="return confirm('Kosongkan keranjang?')" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition h-[42px] flex items-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>