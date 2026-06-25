<?php
$ndalemPage = $ndalemPage ?? '';
$user = Auth::user();
?>
<aside class="sidebar hidden md:flex md:w-64 flex-col fixed h-full z-30">
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                <i data-lucide="home" class="w-5 h-5 text-gold-400"></i>
            </div>
            <div>
                <p class="text-white font-bold text-sm">Ndalem</p>
                <p class="text-emerald-300/60 text-xs">Asisten Pengasuh</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 p-4 space-y-1">
        <a href="<?= base_url('/ndalem') ?>" class="sidebar-link <?= $ndalemPage === 'antrean' ? 'active' : '' ?>">
            <i data-lucide="list-ordered" class="w-4 h-4"></i> Antrean Sowan
        </a>
        <a href="<?= base_url('/ndalem/rekap') ?>" class="sidebar-link <?= $ndalemPage === 'rekap' ? 'active' : '' ?>">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i> Rekap Tamu
        </a>
        <a href="<?= base_url('/ndalem/jadwal') ?>" class="sidebar-link <?= $ndalemPage === 'jadwal' ? 'active' : '' ?>">
            <i data-lucide="calendar-days" class="w-4 h-4"></i> Jadwal Terima
        </a>
    </nav>
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-2 mb-3">
            <div class="avatar bg-white/20 !text-white text-xs"><?= user_initials($user['name']) ?></div>
            <div>
                <p class="text-white text-sm font-medium"><?= e($user['name']) ?></p>
            </div>
        </div>
        <a href="<?= base_url('/ndalem/logout') ?>" class="sidebar-link text-red-300">
            <i data-lucide="log-out" class="w-4 h-4"></i> Logout
        </a>
    </div>
</aside>
