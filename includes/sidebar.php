<aside class="w-56 bg-slate-900 text-white flex flex-col shrink-0 hidden md:flex">
  <div class="p-5 border-b border-slate-700">
    <h1 class="font-bold text-lg text-white text-center">Pondok Mengkudu</h1>
  </div>
  <nav class="flex-1 p-3 space-y-1">
    <a href="dashboard.php" class="sidebar-link <?= (isset($page) && $page == 'dashboard') ? 'active' : 'hover:bg-slate-700 text-slate-300' ?> w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors">
      <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
    </a>
    <a href="transaksi.php" class="sidebar-link <?= (isset($page) && $page == 'transaksi') ? 'active' : 'hover:bg-slate-700 text-slate-300' ?> w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors">
      <i data-lucide="shopping-cart" class="w-4 h-4"></i> Transaksi Kasir
    </a>
    <a href="master_menu.php" class="sidebar-link <?= (isset($page) && $page == 'master_menu') ? 'active' : 'hover:bg-slate-700 text-slate-300' ?> w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors">
      <i data-lucide="layout-list" class="w-4 h-4"></i> Kelola Menu
    </a>
    <a href="laporan.php" class="sidebar-link <?= (isset($page) && $page == 'laporan') ? 'active' : 'hover:bg-slate-700 text-slate-300' ?> w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors">
      <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Laporan
    </a>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
      <div class="pt-4 mt-2 border-t border-slate-700">
        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Administrator</p>
        <a href="manajemen_user.php" class="sidebar-link <?= (isset($page) && $page == 'manajemen_user') ? 'active' : 'hover:bg-slate-700 text-slate-300' ?> w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors">
          <i data-lucide="users" class="w-4 h-4"></i> Manajemen User
        </a>
      </div>
    <?php endif; ?>
  </nav>

  <footer class="p-4 border-t border-slate-700 text-xs text-slate-400 text-center">
    <p>© <?= date('Y') ?> V.1.0</p>
  </footer>
</aside>