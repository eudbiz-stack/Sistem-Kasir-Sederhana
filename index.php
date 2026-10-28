<?php
session_start();
// Logika Pintar: Jika user sudah login, langsung arahkan ke dashboard
// agar tidak perlu melihat halaman landing lagi.
if (isset($_SESSION['id_user'])) {
    header("Location: modul/dashboard.php");
    exit;
}
?>
<!doctype html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir - Warung Pondok Mengkudu</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }

        /* Animasi sederhana untuk elemen mengambang */
        .float-anim {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }
    </style>
</head>

<body class="bg-slate-50 flex flex-col min-h-screen selection:bg-blue-200 selection:text-blue-900">

    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-blue-800 p-2 rounded-lg text-white">
                    <i data-lucide="utensils" class="w-5 h-5"></i>
                </div>
                <h1 class="font-bold text-xl text-slate-800 tracking-tight">Pondok Mengkudu</h1>
            </div>
            <a href="login.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2 rounded-full text-sm font-semibold transition-all">
                Masuk Kasir
            </a>
        </div>
    </nav>

    <main class="flex-1 flex flex-col items-center justify-center relative overflow-hidden px-6 py-16 lg:py-24">

        <div class="absolute top-10 left-10 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 float-anim"></div>
        <div class="absolute bottom-10 right-10 w-72 h-72 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 float-anim" style="animation-delay: 2s;"></div>

        <div class="max-w-3xl w-full text-center relative z-10">
            <span class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-700 text-xs font-bold tracking-wider mb-6">
                Nurliza Herliana (22441055) & Amalia Retno Syahputri (22441005)
            </span>
            <h2 class="text-4xl md:text-6xl font-extrabold text-slate-900 mb-6 leading-tight">
                Kelola Warung Lebih <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-blue-500">Cepat & Cerdas</span>
            </h2>
            <p class="text-lg text-slate-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Sistem Point of Sales (POS) khusus untuk Warung Pondok Mengkudu. Catat pesanan Bakso, Mie Ayam, dan aneka minuman dengan mudah tanpa ribet.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="login.php" class="w-full sm:w-auto px-8 py-3.5 bg-blue-800 hover:bg-blue-900 text-white rounded-full font-semibold shadow-lg shadow-blue-800/30 hover:shadow-blue-800/50 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    Buka Aplikasi Kasir
                </a>
            </div>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mt-20 relative z-10 w-full">

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform">
                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">Transaksi Cepat</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Sistem keranjang belanja cerdas (Session Cart) untuk melayani pelanggan tanpa jeda (loading) yang lama.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 mb-6 group-hover:scale-110 transition-transform">
                    <i data-lucide="layout-list" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">Manajemen Menu</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Atur harga Mie Sop, Bakso Krikil, hingga ketersediaan stok makanan langsung dari dashboard admin.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mb-6 group-hover:scale-110 transition-transform">
                    <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">Laporan Keuangan</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Pantau total omset penjualan harian hingga bulanan dengan fitur filter tanggal dan cetak nota.
                </p>
            </div>

        </div>
    </main>

    <footer class="bg-white border-t border-slate-100 py-8 text-center text-slate-500 text-sm">
        <p>&copy; <?= date('Y') ?> Warung Pondok Mengkudu - Jl. Mengkudu, Binjai Barat. All rights reserved.</p>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>