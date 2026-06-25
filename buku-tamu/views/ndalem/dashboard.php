<?php
$title = 'Dashboard Ndalem';
$user = Auth::user();
$currentStatus = $pengasuh['status'] ?? 'available';
$ndalemPage = 'antrean';
$mobileTitle = 'Antrean Sowan';
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/ndalem_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/ndalem_mobile_header.php'; ?>

        <main class="p-4 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 animate-fade-up">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Antrean</h1>
                    <p class="text-gray-400 text-sm">Kelola tamu sowan & status pengasuh</p>
                </div>
                <div class="flex items-center gap-2 mt-3 sm:mt-0 px-3 py-1.5 bg-white rounded-full border text-xs text-gray-500">
                    <span class="live-dot"></span>
                    <span id="queueUpdate">Live · refresh 5 detik</span>
                </div>
            </div>

            <!-- Rekap ringkas -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6 animate-fade-up animate-delay-1">
                <a href="<?= base_url('/ndalem/rekap') ?>" class="stat-card hover:shadow-md transition group">
                    <p class="text-gray-400 text-[10px] font-semibold uppercase">Total Sowan Hari Ini</p>
                    <p class="text-2xl font-extrabold text-gray-800 mt-1" id="statTotal"><?= $summary['total'] ?></p>
                </a>
                <a href="<?= base_url('/ndalem/rekap') ?>" class="stat-card border-amber-100 bg-amber-50/40 hover:shadow-md transition">
                    <p class="text-amber-600 text-[10px] font-semibold uppercase">Akan Masuk</p>
                    <p class="text-2xl font-extrabold text-amber-700 mt-1" id="statUpcoming"><?= $summary['upcoming'] ?></p>
                    <p class="text-[10px] text-amber-500/80 mt-0.5">Menunggu · Lihat rekap →</p>
                </a>
                <a href="<?= base_url('/ndalem/rekap') ?>" class="stat-card border-indigo-100 bg-indigo-50/40 hover:shadow-md transition">
                    <p class="text-indigo-600 text-[10px] font-semibold uppercase">Sedang Sowan</p>
                    <p class="text-2xl font-extrabold text-indigo-700 mt-1" id="statMeeting"><?= $summary['in_meeting'] ?></p>
                </a>
                <a href="<?= base_url('/ndalem/rekap') ?>" class="stat-card border-emerald-100 bg-emerald-50/40 hover:shadow-md transition">
                    <p class="text-emerald-600 text-[10px] font-semibold uppercase">Sudah Masuk</p>
                    <p class="text-2xl font-extrabold text-emerald-700 mt-1" id="statEntered"><?= $summary['entered'] ?></p>
                    <p class="text-[10px] text-emerald-500/80 mt-0.5"><span id="statCompleted"><?= $summary['completed'] ?></span> selesai</p>
                </a>
            </div>

            <?php if (!empty($success)): ?>
            <div class="card bg-emerald-50 border-emerald-200 text-emerald-800 px-4 py-3 mb-4 text-sm flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i><?= e($success) ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="card bg-red-50 border-red-200 text-red-800 px-4 py-3 mb-4 text-sm flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i><?= e($error) ?>
            </div>
            <?php endif; ?>

            <!-- Status Control -->
            <div class="card p-6 mb-6 animate-fade-up animate-delay-1">
                <div class="flex items-center gap-2 mb-5">
                    <i data-lucide="toggle-right" class="w-5 h-5 text-pesantren-600"></i>
                    <div>
                        <h2 class="font-semibold text-gray-800">Status Ketersediaan Pengasuh</h2>
                        <p class="text-xs text-gray-400">Saat ini: <strong class="text-pesantren-700"><?= e($pengasuhModel->getStatusLabel($currentStatus)) ?></strong></p>
                    </div>
                </div>
                <form method="POST" action="<?= base_url('/ndalem/status') ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <button type="submit" name="status" value="available"
                            class="status-btn <?= $currentStatus === 'available' ? 'active-available' : '' ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Sedang Luang</p>
                                <p class="text-xs text-gray-400">Menerima tamu sowan</p>
                            </div>
                        </div>
                    </button>
                    <button type="submit" name="status" value="busy"
                            class="status-btn <?= $currentStatus === 'busy' ? 'active-busy' : '' ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center">
                                <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Sibuk</p>
                                <p class="text-xs text-gray-400">Antrean lebih lama</p>
                            </div>
                        </div>
                    </button>
                    <button type="submit" name="status" value="closed"
                            class="status-btn <?= $currentStatus === 'closed' ? 'active-closed' : '' ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                                <i data-lucide="ban" class="w-5 h-5 text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Ditutup</p>
                                <p class="text-xs text-gray-400">Tidak menerima sowan</p>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

            <!-- Queue -->
            <div class="card overflow-hidden animate-fade-up animate-delay-2">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-pesantren-50/50 to-white">
                    <div class="flex items-center gap-2">
                        <i data-lucide="heart-handshake" class="w-5 h-5 text-pesantren-600"></i>
                        <h2 class="font-semibold text-gray-800">Antrean Aktif</h2>
                        <span class="px-2 py-0.5 bg-pesantren-100 text-pesantren-700 rounded-full text-xs font-bold" id="queueCount"><?= count($queue) ?></span>
                    </div>
                    <a href="<?= base_url('/ndalem/rekap') ?>" class="text-xs text-pesantren-600 font-medium hover:underline flex items-center gap-1">
                        <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i> Rekap lengkap
                    </a>
                </div>

                <?php if (empty($queue)): ?>
                <div class="text-center py-20">
                    <div class="w-20 h-20 bg-pesantren-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="coffee" class="w-10 h-10 text-pesantren-300"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada tamu sowan aktif</p>
                    <p class="text-gray-400 text-sm mt-1">Antrean muncul setelah tamu check-in di pos keamanan</p>
                </div>
                <?php else: ?>
                <div class="p-4 space-y-3" id="queueList">
                    <?php foreach ($queue as $i => $v): ?>
                    <div class="queue-card relative flex flex-col lg:flex-row lg:items-center gap-4 <?= $i === 0 ? 'ring-2 ring-pesantren-200' : '' ?>">
                        <?php if ($i === 0): ?>
                        <div class="absolute -top-2 left-4 px-2.5 py-0.5 bg-gold-500 text-white text-[10px] font-bold uppercase rounded-full shadow-sm">Berikutnya</div>
                        <?php endif; ?>
                        <div class="flex items-center gap-4 flex-1">
                            <div class="queue-number"><?= str_pad($v['queue_number'], 3, '0', STR_PAD_LEFT) ?></div>
                            <div>
                                <p class="font-bold text-gray-800 text-lg"><?= e($v['nama_lengkap']) ?></p>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-sm text-gray-500">
                                    <span class="flex items-center gap-1"><i data-lucide="map-pin" class="w-3 h-3"></i><?= e($v['asal']) ?></span>
                                    <span class="flex items-center gap-1"><i data-lucide="phone" class="w-3 h-3"></i><?= e($v['no_hp'] ?: '-') ?></span>
                                </div>
                                <?php if ($v['detail_keperluan']): ?>
                                <p class="text-sm text-gray-600 mt-2 bg-gray-50 rounded-lg px-3 py-2"><?= e($v['detail_keperluan']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($v['waktu_temu'])): ?>
                                <p class="text-xs text-pesantren-700 mt-2 flex items-center gap-1">
                                    <i data-lucide="calendar-clock" class="w-3.5 h-3.5"></i>
                                    Temu: <?= e(format_waktu_temu_display($v['waktu_temu'])) ?>
                                </p>
                                <?php endif; ?>
                                <div class="mt-2"><?= status_badge($v['status']) ?></div>
                            </div>
                        </div>
                        <div class="flex gap-2 flex-wrap lg:flex-col lg:items-stretch">
                            <?php if (in_array($v['status'], ['checked_in', 'in_queue'])): ?>
                            <form method="POST" action="<?= base_url('/ndalem/approve/' . $v['id']) ?>" class="space-y-2 w-full min-w-[12rem]">
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1">Waktu temu tamu <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="waktu_temu" required
                                           value="<?= date('Y-m-d\TH:i', strtotime('+30 minutes')) ?>"
                                           class="w-full text-xs border border-gray-200 rounded-lg px-2 py-2">
                                </div>
                                <button class="btn-primary w-full py-2.5 text-sm rounded-xl">
                                    <i data-lucide="check" class="w-4 h-4"></i> Setujui + Kirim WA
                                </button>
                                <?php if (trim($v['no_hp'] ?? '') === ''): ?>
                                <p class="text-[10px] text-amber-600">Tamu tanpa HP — WA tidak terkirim</p>
                                <?php endif; ?>
                            </form>
                            <form method="POST" action="<?= base_url('/ndalem/reject/' . $v['id']) ?>">
                                <button class="w-full py-2.5 text-sm rounded-xl border border-red-200 text-red-600 hover:bg-red-50 font-medium transition">
                                    Tolak
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php if ($v['status'] === 'approved'): ?>
                            <form method="POST" action="<?= base_url('/ndalem/call/' . $v['id']) ?>">
                                <button class="w-full py-2.5 text-sm rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 font-medium transition flex items-center justify-center gap-1">
                                    <i data-lucide="megaphone" class="w-4 h-4"></i> Panggil Tamu
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php if (in_array($v['status'], ['called', 'approved'])): ?>
                            <form method="POST" action="<?= base_url('/ndalem/complete/' . $v['id']) ?>">
                                <button class="w-full py-2.5 text-sm rounded-xl bg-gray-700 text-white hover:bg-gray-800 font-medium transition">
                                    Selesai
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script src="<?= base_url('/assets/js/live-poll.js') ?>"></script>
<script>
LivePoll.start(async () => {
    try {
        const res = await fetch('<?= base_url('/ndalem/api/queue') ?>');
        const data = await res.json();
        const s = data.summary || {};
        LivePoll.setText('statTotal', s.total ?? 0);
        LivePoll.setText('statUpcoming', s.upcoming ?? 0);
        LivePoll.setText('statMeeting', s.in_meeting ?? 0);
        LivePoll.setText('statEntered', s.entered ?? 0);
        LivePoll.setText('statCompleted', s.completed ?? 0);
        LivePoll.setText('queueCount', (data.queue || []).length);
        LivePoll.setLiveLabel('queueUpdate');
    } catch (e) {}
});
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
