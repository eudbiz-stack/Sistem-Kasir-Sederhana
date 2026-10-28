<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php'; // Panggil koneksi database

$page = 'master_menu';
$title = 'Kelola Data Menu';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col overflow-auto">
    <?php include __DIR__ . '/../includes/topbar.php'; ?>

    <main class="flex-1 p-6 bg-slate-50">

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="mb-4 px-4 py-3 rounded text-sm <?= $_SESSION['tipe'] == 'sukses' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400' ?>">
                <?= $_SESSION['pesan']; ?>
            </div>
            <?php
            unset($_SESSION['pesan']);
            unset($_SESSION['tipe']);
            ?>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-semibold text-slate-800 text-lg">Daftar Makanan & Minuman</h3>
                <button onclick="bukaModal('tambah')" class="bg-blue-800 hover:bg-blue-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    + Tambah Menu
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-medium">No</th>
                            <th class="px-5 py-3 font-medium">Nama Menu</th>
                            <th class="px-5 py-3 font-medium">Kategori</th>
                            <th class="px-5 py-3 font-medium">Harga</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM menu ORDER BY jenis ASC, nama_menu ASC");
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)) {
                            // Mewarnai badge status
                            $warna_status = $row['status'] == 'tersedia' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                            $harga = number_format($row['harga'], 0, ',', '.');

                            // Data disiapkan untuk dilempar ke Javascript saat tombol Edit diklik
                            $data_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

                            echo "
                            <tr class='border-b border-slate-50 hover:bg-slate-50/50'>
                                <td class='px-5 py-3 text-slate-600'>{$no}</td>
                                <td class='px-5 py-3 font-medium text-slate-800'>{$row['nama_menu']}</td>
                                <td class='px-5 py-3 capitalize text-slate-600'>{$row['jenis']}</td>
                                <td class='px-5 py-3 text-slate-600'>Rp {$harga}</td>
                                <td class='px-5 py-3'>
                                    <span class='px-2 py-1 rounded text-xs capitalize font-medium {$warna_status}'>{$row['status']}</span>
                                </td>
                                <td class='px-5 py-3 flex justify-center gap-2'>
                                    <button onclick='bukaModal(\"edit\", {$data_json})' class='text-blue-600 hover:text-blue-800 text-xs font-medium'>Edit</button>
                                    <a href='../proses/proses_menu.php?aksi=hapus&id={$row['id_menu']}' onclick='return confirm(\"Yakin ingin menghapus menu ini?\")' class='text-red-500 hover:text-red-700 text-xs font-medium'>Hapus</a>
                                </td>
                            </tr>
                            ";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<div id="modalMenu" class="fixed inset-0 bg-slate-900/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
        <h3 id="modalTitle" class="text-lg font-bold text-slate-800 mb-4">Tambah Menu Baru</h3>

        <form id="formMenu" action="../proses/proses_menu.php" method="POST">
            <input type="hidden" name="aksi" id="aksi" value="tambah">
            <input type="hidden" name="id_menu" id="id_menu">

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Menu</label>
                <input type="text" name="nama_menu" id="nama_menu" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <select name="jenis" id="jenis" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                        <option value="cemilan">Cemilan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="status" id="status" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                        <option value="tersedia">Tersedia</option>
                        <option value="habis">Habis</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
                <input type="number" name="harga" id="harga" min="0" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="tutupModal()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-800 text-white rounded-lg text-sm font-medium hover:bg-blue-900">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Logika sederhana untuk mengatur Modal Form menggunakan Javascript Murni
    function bukaModal(jenis_aksi, data = null) {
        const modal = document.getElementById('modalMenu');
        const form = document.getElementById('formMenu');

        // Reset form setiap kali modal dibuka
        form.reset();

        if (jenis_aksi === 'tambah') {
            document.getElementById('modalTitle').innerText = 'Tambah Menu Baru';
            document.getElementById('aksi').value = 'tambah';
        } else if (jenis_aksi === 'edit' && data) {
            document.getElementById('modalTitle').innerText = 'Edit Data Menu';
            document.getElementById('aksi').value = 'edit';

            // Isi form dengan data yang dilempar dari PHP
            document.getElementById('id_menu').value = data.id_menu;
            document.getElementById('nama_menu').value = data.nama_menu;
            document.getElementById('jenis').value = data.jenis;
            document.getElementById('status').value = data.status;
            document.getElementById('harga').value = data.harga;
        }

        // Tampilkan modal (mengubah class hidden Tailwind)
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupModal() {
        const modal = document.getElementById('modalMenu');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

<?php include '../includes/footer.php'; ?>