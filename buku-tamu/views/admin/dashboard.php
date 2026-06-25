<?php
$title = 'Dasbor Admin';
$user = Auth::user();
$adminPage = 'dashboard';
$mobileTitle = 'Dasbor';
?>

<div class="flex min-h-screen bg-gray-50 no-print">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/admin_mobile_header.php'; ?>

        <main class="p-4 md:p-8">
            <!-- Page header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 animate-fade-up">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dasbor Real-Time</h1>
                    <p class="text-gray-400 text-sm mt-0.5">Monitor tamu yang sedang berada di pesantren</p>
                </div>
                <div class="flex items-center gap-2 mt-3 sm:mt-0 px-3 py-1.5 bg-white rounded-full border text-xs text-gray-500">
                    <span class="live-dot"></span>
                    <span id="lastUpdate">Live · refresh 5 detik</span>
                </div>
            </div>

            <?php if (!empty($error)): ?>
            <div class="card bg-red-50 border-red-200 text-red-800 px-4 py-3 mb-4 text-sm flex items-center gap-2 animate-fade-up">
                <i data-lucide="alert-circle" class="w-4 h-4"></i><?= e($error) ?>
            </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="stat-card animate-fade-up animate-delay-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wide">Tamu Hari Ini</p>
                            <p class="text-3xl font-extrabold text-pesantren-700 mt-1" id="statToday"><?= $stats['today_total'] ?></p>
                        </div>
                        <div class="stat-icon bg-pesantren-50">
                            <i data-lucide="users" class="w-5 h-5 text-pesantren-600"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card animate-fade-up animate-delay-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wide">Di Dalam</p>
                            <p class="text-3xl font-extrabold text-blue-600 mt-1" id="statInside"><?= $stats['currently_inside'] ?></p>
                        </div>
                        <div class="stat-icon bg-blue-50">
                            <i data-lucide="door-open" class="w-5 h-5 text-blue-600"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card col-span-2 animate-fade-up animate-delay-2">
                    <div class="flex items-center gap-4">
                        <div class="stat-icon bg-amber-50">
                            <i data-lucide="calendar" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wide">Tanggal</p>
                            <p class="text-lg font-bold text-gray-800"><?= date('d F Y') ?></p>
                            <p class="text-sm text-pesantren-600"><?= e(HijriDate::toHijri()) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active visitors -->
            <div class="card mb-6 animate-fade-up animate-delay-2 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-pesantren-50/50 to-white">
                    <div class="flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5 text-pesantren-600"></i>
                        <h2 class="font-semibold text-gray-800">Tamu Sedang di Dalam</h2>
                        <span class="px-2 py-0.5 bg-pesantren-100 text-pesantren-700 rounded-full text-xs font-bold"><?= count($active) ?></span>
                    </div>
                </div>
                <div class="overflow-x-auto" id="activeTable">
                    <?php if (empty($active)): ?>
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="inbox" class="w-8 h-8 text-gray-300"></i>
                        </div>
                        <p class="text-gray-400 text-sm">Belum ada tamu di dalam pesantren</p>
                    </div>
                    <?php else: ?>
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Antrean</th>
                                <th>Tamu</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($active as $v): ?>
                            <tr>
                                <td><span class="font-mono font-bold text-pesantren-700 text-lg"><?= str_pad($v['queue_number'], 3, '0', STR_PAD_LEFT) ?></span></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar"><?= user_initials($v['nama_lengkap']) ?></div>
                                        <div>
                                            <p class="font-semibold text-gray-800"><?= e($v['nama_lengkap']) ?></p>
                                            <p class="text-gray-400 text-xs"><?= e($v['asal']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-gray-600"><?= e(tujuan_label($v['tujuan_kunjungan'])) ?></span></td>
                                <td><?= status_badge($v['status']) ?></td>
                                <td>
                                    <div class="flex gap-1.5 flex-wrap">
                                        <?php if ($v['status'] === 'pending'): ?>
                                        <form method="POST" action="<?= base_url('/admin/checkin/' . $v['id']) ?>" class="flex flex-wrap items-center gap-1.5">
                                            <?php if (trim($v['no_hp'] ?? '') !== '' && $v['tujuan_kunjungan'] !== 'sowan'): ?>
                                            <input type="datetime-local" name="waktu_temu" required
                                                   value="<?= date('Y-m-d\TH:i', strtotime('+1 hour')) ?>"
                                                   class="text-[10px] border border-gray-200 rounded-lg px-2 py-1.5"
                                                   title="Waktu temu tamu">
                                            <?php endif; ?>
                                            <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition">
                                                <i data-lucide="log-in" class="w-3 h-3"></i> Check-In
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?= base_url('/admin/checkout/' . $v['id']) ?>">
                                            <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 transition">
                                                <i data-lucide="log-out" class="w-3 h-3"></i> Out
                                            </button>
                                        </form>
                                        <a href="<?= base_url('/admin/print/' . $v['id']) ?>" target="_blank"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-pesantren-600 text-white rounded-lg text-xs font-medium hover:bg-pesantren-700 transition">
                                            <i data-lucide="printer" class="w-3 h-3"></i> Stiker
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- All today -->
            <div class="card animate-fade-up animate-delay-3 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-pesantren-600"></i>
                    <h2 class="font-semibold text-gray-800">Semua Tamu Hari Ini</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>HP</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($visitors)): ?>
                            <tr><td colspan="7" class="text-center py-12 text-gray-400">Belum ada tamu hari ini</td></tr>
                            <?php else: ?>
                            <?php foreach ($visitors as $v): ?>
                            <tr>
                                <td class="text-gray-400 font-mono text-xs"><?= date('H:i', strtotime($v['created_at'])) ?></td>
                                <td class="font-mono font-bold text-pesantren-600"><?= str_pad($v['queue_number'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td class="font-medium"><?= e($v['nama_lengkap']) ?></td>
                                <td class="text-gray-500"><?= e($v['no_hp'] ?: '-') ?></td>
                                <td><?= e(tujuan_label($v['tujuan_kunjungan'])) ?></td>
                                <td><?= status_badge($v['status']) ?></td>
                                <td>
                                    <div class="flex gap-1">
                                        <?php if ($v['status'] === 'pending'): ?>
                                        <form method="POST" action="<?= base_url('/admin/checkin/' . $v['id']) ?>" class="flex flex-wrap items-center gap-1">
                                            <?php if (trim($v['no_hp'] ?? '') !== '' && $v['tujuan_kunjungan'] !== 'sowan'): ?>
                                            <input type="datetime-local" name="waktu_temu" required
                                                   value="<?= date('Y-m-d\TH:i', strtotime('+1 hour')) ?>"
                                                   class="text-[10px] border border-gray-200 rounded px-1.5 py-1 w-36">
                                            <?php endif; ?>
                                            <button class="px-2.5 py-1 bg-blue-600 text-white rounded-lg text-xs">In</button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($v['status'] !== 'checked_out'): ?>
                                        <form method="POST" action="<?= base_url('/admin/checkout/' . $v['id']) ?>">
                                            <button class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs">Out</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="<?= base_url('/assets/js/live-poll.js') ?>"></script>
<script>
const baseUrl = '<?= base_url('') ?>';
LivePoll.start(async () => {
    try {
        const res = await fetch(baseUrl + '/admin/api/visitors');
        const data = await res.json();
        LivePoll.setText('statToday', data.stats.today_total);
        LivePoll.setText('statInside', data.stats.currently_inside);
        LivePoll.setLiveLabel('lastUpdate');
    } catch (e) {}
});
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
