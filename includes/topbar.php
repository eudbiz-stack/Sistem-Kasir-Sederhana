<header class="bg-white border-b px-6 py-4 flex items-center justify-between">
    <h2 class="font-semibold text-slate-800 text-xl">
        <?= isset($title) ? $title : 'Dashboard Kasir' ?>
    </h2>

    <div class="flex items-center gap-5">

        <div class="text-right">
            <span class="block text-sm font-medium text-slate-600">
                <?= date('l, d F Y') ?>
            </span>
            <span id="jam-live" class="block text-xs text-slate-400 mt-0.5">
                <?= date('H:i:s') ?> WIB
            </span>
        </div>

        <div class="h-8 w-px bg-slate-200"></div>

        <a href="../logout.php" onclick="return confirm('Apakah Anda yakin ingin menutup sesi kasir?')"
            class="flex items-center gap-2 text-sm font-medium text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            Logout
        </a>
    </div>
</header>

<script>
    function updateJam() {
        const waktu = new Date();
        const jam = waktu.getHours().toString().padStart(2, '0');
        const menit = waktu.getMinutes().toString().padStart(2, '0');
        const detik = waktu.getSeconds().toString().padStart(2, '0');

        const elemenJam = document.getElementById('jam-live');
        if (elemenJam) {
            elemenJam.innerText = jam + ':' + menit + ':' + detik + ' WIB';
        }
    }

    // Jalankan fungsi updateJam setiap 1 detik (1000 milidetik)
    setInterval(updateJam, 1000);
</script>