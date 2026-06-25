<?php
$adminPage = $adminPage ?? '';
$user = Auth::user();
?>
<aside class="sidebar hidden md:flex md:w-64 flex-col fixed h-full z-30">
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <?php
            $brandSize = 'sm';
            $brandIcon = 'shield';
            $brandClass = 'bg-white/10 border border-white/10 rounded-xl';
            $brandMargin = '';
            require __DIR__ . '/pesantren_brand.php';
            ?>
            <div>
                <p class="text-white font-bold text-sm">Pos Keamanan</p>
                <p class="text-emerald-300/60 text-xs truncate max-w-[140px]"><?= e($app['pesantren_name']) ?></p>
            </div>
        </div>
    </div>
    <nav class="flex-1 p-4 space-y-1">
        <a href="<?= base_url('/admin') ?>" class="sidebar-link <?= $adminPage === 'dashboard' ? 'active' : '' ?>">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dasbor
        </a>
        <a href="<?= base_url('/admin/statistik') ?>" class="sidebar-link <?= $adminPage === 'statistik' ? 'active' : '' ?>">
            <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Statistik
        </a>
        <a href="<?= base_url('/admin/qr') ?>" class="sidebar-link <?= $adminPage === 'qr' ? 'active' : '' ?>">
            <i data-lucide="qr-code" class="w-4 h-4"></i> Cetak QR Tamu
        </a>
        <div class="pt-3 pb-1 px-3">
            <p class="text-emerald-300/40 text-[10px] font-semibold uppercase tracking-wider">Pengaturan</p>
        </div>
        <a href="<?= base_url('/admin/pengaturan/identitas') ?>" class="sidebar-link <?= $adminPage === 'identitas' ? 'active' : '' ?>">
            <i data-lucide="building-2" class="w-4 h-4"></i> Identitas Pesantren
        </a>
        <a href="<?= base_url('/admin/pengaturan/ndalem') ?>" class="sidebar-link <?= $adminPage === 'ndalem' ? 'active' : '' ?>">
            <i data-lucide="home" class="w-4 h-4"></i> Akun Ndalem
        </a>
        <a href="<?= base_url('/admin/pengaturan/whatsapp') ?>" class="sidebar-link <?= $adminPage === 'whatsapp' ? 'active' : '' ?>">
            <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp
        </a>
        <a href="<?= base_url('/admin/pengaturan/jadwal') ?>" class="sidebar-link <?= $adminPage === 'jadwal' ? 'active' : '' ?>">
            <i data-lucide="calendar-days" class="w-4 h-4"></i> Jadwal Tamu
        </a>
    </nav>
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-2 mb-3">
            <div class="avatar bg-white/20 !text-white text-xs"><?= user_initials($user['name']) ?></div>
            <div>
                <p class="text-white text-sm font-medium"><?= e($user['name']) ?></p>
                <p class="text-emerald-300/50 text-xs">Administrator</p>
            </div>
        </div>
        <a href="<?= base_url('/admin/logout') ?>" class="sidebar-link text-red-300 hover:!text-red-200">
            <i data-lucide="log-out" class="w-4 h-4"></i> Logout
        </a>
    </div>
</aside>
