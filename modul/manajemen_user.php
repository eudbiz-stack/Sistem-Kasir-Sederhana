<?php
session_start();
// Proteksi Ganda: Pastikan login DAN role-nya adalah admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php'; // Panggil koneksi database

$page = 'manajemen_user';
$title = 'Kelola Akun Pengguna';

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

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-semibold text-slate-800 text-lg">Daftar Pengguna Sistem</h3>
                <button onclick="bukaModal('tambah')" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah User
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-medium">No</th>
                            <th class="px-5 py-3 font-medium">Nama Lengkap</th>
                            <th class="px-5 py-3 font-medium">Username</th>
                            <th class="px-5 py-3 font-medium">Role</th>
                            <th class="px-5 py-3 font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM user ORDER BY role ASC, nama_lengkap ASC");
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)) {
                            $badge_role = $row['role'] == 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700';

                            // Hapus field password dari JSON agar aman (tidak bocor ke frontend)
                            $data_edit = $row;
                            unset($data_edit['password']);
                            $data_json = htmlspecialchars(json_encode($data_edit), ENT_QUOTES, 'UTF-8');

                            echo "
                            <tr class='border-b border-slate-50 hover:bg-slate-50/50'>
                                <td class='px-5 py-3 text-slate-600'>{$no}</td>
                                <td class='px-5 py-3 font-medium text-slate-800'>{$row['nama_lengkap']}</td>
                                <td class='px-5 py-3 text-slate-600 font-mono text-xs'>{$row['username']}</td>
                                <td class='px-5 py-3'>
                                    <span class='px-2 py-1 rounded text-xs capitalize font-medium {$badge_role}'>{$row['role']}</span>
                                </td>
                                <td class='px-5 py-3 flex justify-center gap-3'>
                                    <button onclick='bukaModal(\"edit\", {$data_json})' class='text-blue-600 hover:text-blue-800 text-xs font-medium'>Edit</button>";

                            // Cegah admin menghapus dirinya sendiri
                            if ($row['id_user'] != $_SESSION['id_user']) {
                                echo "<a href='../proses/proses_user.php?aksi=hapus&id={$row['id_user']}' onclick='return confirm(\"Yakin ingin menghapus user ini?\")' class='text-red-500 hover:text-red-700 text-xs font-medium'>Hapus</a>";
                            } else {
                                echo "<span class='text-slate-300 text-xs italic' title='Anda sedang login menggunakan akun ini'>Hapus</span>";
                            }

                            echo "</td></tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<div id="modalUser" class="fixed inset-0 bg-slate-900/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
        <h3 id="modalTitle" class="text-lg font-bold text-slate-800 mb-4">Tambah User Baru</h3>

        <form id="formUser" action="../proses/proses_user.php" method="POST">
            <input type="hidden" name="aksi" id="aksi" value="tambah">
            <input type="hidden" name="id_user" id="id_user">

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Username (Untuk Login)</label>
                <input type="text" name="username" id="username" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <p id="bantuanPassword" class="text-xs text-slate-400 mt-1 hidden">Biarkan kosong jika tidak ingin mengubah password saat ini.</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Hak Akses (Role)</label>
                <select name="role" id="role" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    <option value="kasir">Kasir</option>
                    <option value="admin">Administrator / Pemilik</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="tutupModal()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-800 text-white rounded-lg text-sm font-medium hover:bg-blue-900">Simpan User</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModal(jenis, data = null) {
        const modal = document.getElementById('modalUser');
        document.getElementById('formUser').reset();

        if (jenis === 'tambah') {
            document.getElementById('modalTitle').innerText = 'Tambah User Baru';
            document.getElementById('aksi').value = 'tambah';
            document.getElementById('password').required = true;
            document.getElementById('bantuanPassword').classList.add('hidden');
        } else if (jenis === 'edit' && data) {
            document.getElementById('modalTitle').innerText = 'Edit Data User';
            document.getElementById('aksi').value = 'edit';

            document.getElementById('id_user').value = data.id_user;
            document.getElementById('nama_lengkap').value = data.nama_lengkap;
            document.getElementById('username').value = data.username;
            document.getElementById('role').value = data.role;

            // Password tidak wajib diisi jika hanya ingin edit nama/username
            document.getElementById('password').required = false;
            document.getElementById('bantuanPassword').classList.remove('hidden');
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupModal() {
        const modal = document.getElementById('modalUser');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

<?php include '../includes/footer.php'; ?>