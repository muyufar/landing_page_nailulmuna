<?php
$title = 'Statistik Kunjungan';
$user = Auth::user();
$adminPage = 'statistik';
$mobileTitle = 'Statistik';
$mobileBackUrl = base_url('/admin');
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/admin_mobile_header.php'; ?>

        <main class="p-4 md:p-8">
            <div class="mb-6 animate-fade-up">
                <h1 class="text-2xl font-bold text-gray-800">Statistik Kunjungan</h1>
                <p class="text-gray-400 text-sm">Analisis pola kunjungan tamu pesantren</p>
            </div>

            <div class="inline-flex bg-white rounded-xl border p-1 mb-6 animate-fade-up">
                <a href="?period=week" class="px-5 py-2 rounded-lg text-sm font-medium transition <?= $period === 'week' ? 'bg-pesantren-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                    7 Hari
                </a>
                <a href="?period=month" class="px-5 py-2 rounded-lg text-sm font-medium transition <?= $period === 'month' ? 'bg-pesantren-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                    30 Hari
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="stat-card">
                    <p class="text-gray-400 text-xs font-medium uppercase">Tamu Hari Ini</p>
                    <p class="text-3xl font-extrabold text-pesantren-700 mt-1"><?= $stats['today_total'] ?></p>
                </div>
                <div class="stat-card">
                    <p class="text-gray-400 text-xs font-medium uppercase">Sedang di Dalam</p>
                    <p class="text-3xl font-extrabold text-blue-600 mt-1"><?= $stats['currently_inside'] ?></p>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div class="card p-6 animate-fade-up animate-delay-1">
                    <div class="flex items-center gap-2 mb-5">
                        <i data-lucide="trending-up" class="w-5 h-5 text-pesantren-600"></i>
                        <h2 class="font-semibold text-gray-800">Grafik Kunjungan Harian</h2>
                    </div>
                    <canvas id="dailyChart" height="220"></canvas>
                </div>
                <div class="card p-6 animate-fade-up animate-delay-2">
                    <div class="flex items-center gap-2 mb-5">
                        <i data-lucide="pie-chart" class="w-5 h-5 text-pesantren-600"></i>
                        <h2 class="font-semibold text-gray-800">Berdasarkan Tujuan</h2>
                    </div>
                    <canvas id="purposeChart" height="220"></canvas>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dailyData = <?= json_encode($stats['daily']) ?>;
const purposeData = <?= json_encode($stats['by_purpose']) ?>;
const tujuanLabels = <?= json_encode($app['tujuan_options']) ?>;

Chart.defaults.font.family = 'Plus Jakarta Sans';

new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: dailyData.map(d => { const dt = new Date(d.tanggal); return dt.toLocaleDateString('id-ID', {day:'numeric', month:'short'}); }),
        datasets: [{
            label: 'Jumlah Tamu',
            data: dailyData.map(d => d.total),
            backgroundColor: 'rgba(4, 120, 87, 0.8)',
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
    }
});

new Chart(document.getElementById('purposeChart'), {
    type: 'doughnut',
    data: {
        labels: purposeData.map(d => tujuanLabels[d.tujuan_kunjungan] || d.tujuan_kunjungan),
        datasets: [{
            data: purposeData.map(d => d.total),
            backgroundColor: ['#047857','#2563eb','#9333ea','#d97706','#6b7280'],
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } }
    }
});

if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
