<?php
$title = 'Rekap Tamu Sowan';
$ndalemPage = 'rekap';
$mobileTitle = 'Rekap Tamu';
$mobileBackUrl = base_url('/ndalem');
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/ndalem_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/ndalem_mobile_header.php'; ?>

        <main class="p-4 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 animate-fade-up">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Rekap Masuk Ndalem</h1>
                    <p class="text-gray-400 text-sm mt-0.5">Tamu sowan — sekarang & jadwal mendatang · <?= date('d F Y') ?></p>
                </div>
                <div class="flex items-center gap-2 mt-3 sm:mt-0 px-3 py-1.5 bg-white rounded-full border text-xs text-gray-500">
                    <span class="live-dot"></span>
                    <span id="rekapUpdate">Live · refresh 5 detik</span>
                </div>
            </div>

            <!-- Summary stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div class="stat-card animate-fade-up animate-delay-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-[10px] font-semibold uppercase tracking-wide">Total Sowan</p>
                            <p class="text-2xl font-extrabold text-gray-800 mt-0.5" id="statTotal"><?= $summary['total'] ?></p>
                        </div>
                        <div class="stat-icon bg-gray-100"><i data-lucide="users" class="w-5 h-5 text-gray-600"></i></div>
                    </div>
                </div>
                <div class="stat-card border-amber-100 bg-amber-50/30 animate-fade-up animate-delay-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-600 text-[10px] font-semibold uppercase tracking-wide">Akan Masuk</p>
                            <p class="text-2xl font-extrabold text-amber-700 mt-0.5" id="statUpcoming"><?= $summary['upcoming'] ?></p>
                        </div>
                        <div class="stat-icon bg-amber-100"><i data-lucide="log-in" class="w-5 h-5 text-amber-600"></i></div>
                    </div>
                </div>
                <div class="stat-card border-indigo-100 bg-indigo-50/30 animate-fade-up animate-delay-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-600 text-[10px] font-semibold uppercase tracking-wide">Sedang Sowan</p>
                            <p class="text-2xl font-extrabold text-indigo-700 mt-0.5" id="statMeeting"><?= $summary['in_meeting'] ?></p>
                        </div>
                        <div class="stat-icon bg-indigo-100"><i data-lucide="message-circle" class="w-5 h-5 text-indigo-600"></i></div>
                    </div>
                </div>
                <div class="stat-card border-emerald-100 bg-emerald-50/30 animate-fade-up animate-delay-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-600 text-[10px] font-semibold uppercase tracking-wide">Sudah Masuk</p>
                            <p class="text-2xl font-extrabold text-emerald-700 mt-0.5" id="statEntered"><?= $summary['entered'] ?></p>
                        </div>
                        <div class="stat-icon bg-emerald-100"><i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i></div>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <!-- Akan Masuk -->
                <div class="card overflow-hidden animate-fade-up animate-delay-2">
                    <div class="px-5 py-4 border-b border-amber-100 bg-gradient-to-r from-amber-50 to-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                                <i data-lucide="hourglass" class="w-4 h-4 text-amber-600"></i>
                            </div>
                            <div>
                                <h2 class="font-semibold text-gray-800">Akan Masuk Ndalem</h2>
                                <p class="text-xs text-gray-400">Menunggu waktu / check-in / persetujuan</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold" id="countUpcoming"><?= count($upcoming) ?></span>
                    </div>
                    <div class="p-4 space-y-2 max-h-[520px] overflow-y-auto" id="listUpcoming">
                        <?php if (empty($upcoming)): ?>
                        <div class="text-center py-12">
                            <i data-lucide="inbox" class="w-10 h-10 text-gray-200 mx-auto mb-2"></i>
                            <p class="text-gray-400 text-sm">Tidak ada tamu menunggu</p>
                        </div>
                        <?php else: ?>
                        <?php foreach ($upcoming as $v): ?>
                            <?php $visitor = $v; $variant = 'upcoming'; require __DIR__ . '/../partials/ndalem_visitor_card.php'; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sudah Masuk -->
                <div class="card overflow-hidden animate-fade-up animate-delay-3">
                    <div class="px-5 py-4 border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                <i data-lucide="door-open" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <div>
                                <h2 class="font-semibold text-gray-800">Sudah Masuk Ndalem</h2>
                                <p class="text-xs text-gray-400">Sedang sowan atau selesai</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold" id="countEntered"><?= count($entered) ?></span>
                    </div>
                    <div class="p-4 space-y-2 max-h-[520px] overflow-y-auto" id="listEntered">
                        <?php if (empty($entered)): ?>
                        <div class="text-center py-12">
                            <i data-lucide="user-check" class="w-10 h-10 text-gray-200 mx-auto mb-2"></i>
                            <p class="text-gray-400 text-sm">Belum ada tamu yang masuk</p>
                        </div>
                        <?php else: ?>
                        <?php foreach ($entered as $v): ?>
                            <?php $visitor = $v; $variant = 'entered'; require __DIR__ . '/../partials/ndalem_visitor_card.php'; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($summary['jadwal_mendatang'] > 0): ?>
            <div class="card mt-4 p-4 flex items-center gap-3 bg-blue-50/50 border-blue-100 animate-fade-up">
                <i data-lucide="calendar-clock" class="w-5 h-5 text-blue-500"></i>
                <p class="text-sm text-blue-800"><strong><?= $summary['jadwal_mendatang'] ?></strong> tamu dengan jadwal kunjungan mendatang</p>
            </div>
            <?php endif; ?>

            <?php if ($summary['rejected'] > 0): ?>
            <div class="card mt-6 p-4 flex items-center gap-3 bg-red-50/50 border-red-100 animate-fade-up">
                <i data-lucide="info" class="w-5 h-5 text-red-400"></i>
                <p class="text-sm text-red-700"><strong><?= $summary['rejected'] ?></strong> tamu sowan ditolak hari ini</p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="<?= base_url('/assets/js/live-poll.js') ?>"></script>
<script>
LivePoll.start(async () => {
    try {
        const res = await fetch('<?= base_url('/ndalem/api/rekap') ?>');
        const data = await res.json();
        const s = data.summary || {};
        LivePoll.setText('statTotal', s.total ?? 0);
        LivePoll.setText('statUpcoming', s.upcoming ?? 0);
        LivePoll.setText('statMeeting', s.in_meeting ?? 0);
        LivePoll.setText('statEntered', s.entered ?? 0);
        LivePoll.setText('countUpcoming', (data.upcoming || []).length);
        LivePoll.setText('countEntered', (data.entered || []).length);
        LivePoll.setLiveLabel('rekapUpdate');
    } catch (e) {}
});
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
