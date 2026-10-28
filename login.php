<?php
session_start();
// Jika user sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: modul/dashboard.php");
    exit;
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Warung Pondok Mengkudu</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-slate-100">

    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-sm">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Pondok Mengkudu</h1>
            <p class="text-sm text-slate-500 mt-1">Silakan masuk ke akun kasir Anda</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); // Hapus pesan error setelah ditampilkan 
            ?>
        <?php endif; ?>

        <form action="proses/proses_login.php" method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1" for="username">Username</label>
                <input type="text" id="username" name="username" required
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1" for="password">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit"
                class="w-full bg-blue-800 hover:bg-blue-900 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-200">
                Masuk
            </button>
            <a href="index.php" class="block text-center text-blue-600 hover:underline text-sm mt-3">Halaman Utama</a>
        </form>
    </div>

</body>

</html>