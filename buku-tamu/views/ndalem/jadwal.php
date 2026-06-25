<?php
$title = 'Jadwal Terima Tamu';
$ndalemPage = 'jadwal';
$mobileTitle = 'Jadwal Tamu';
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/ndalem_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/ndalem_mobile_header.php'; ?>

        <main class="p-4 md:p-8 max-w-3xl">
            <div class="mb-6 animate-fade-up">
                <h1 class="text-2xl font-bold text-gray-800">Jadwal Terima Tamu</h1>
                <p class="text-gray-400 text-sm">Informasi rekap — dikelola oleh admin pesantren</p>
            </div>

            <div class="card p-6 mb-6 animate-fade-up animate-delay-1">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <i data-lucide="home" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-800">Ndalem — Sowan Pengasuh</h2>
                        <p class="text-xs text-gray-400">Waktu rekomendasi menerima tamu sowan</p>
                    </div>
                </div>
                <?= render_jadwal_terima_html($ndalemGrouped, 'Ndalem') ?>
            </div>

            <div class="card p-6 animate-fade-up animate-delay-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <i data-lucide="building-2" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-800">Pesantren — Kantor</h2>
                        <p class="text-xs text-gray-400">Waktu rekomendasi urusan administrasi & tamu umum</p>
                    </div>
                </div>
                <?= render_jadwal_terima_html($pesantrenGrouped, 'Pesantren') ?>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6 italic">
                Jadwal ini hanya informasi rekap. Tamu tetap dapat mendaftar di luar jam di atas.
            </p>
        </main>
    </div>
</div>
<script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
