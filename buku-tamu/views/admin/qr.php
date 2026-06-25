<?php
$title = 'Cetak QR Buku Tamu';
$adminPage = 'qr';
$mobileTitle = 'Cetak QR';
$scanUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . base_url('/');
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/admin_mobile_header.php'; ?>

        <main class="p-4 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 animate-fade-up">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Cetak QR Isi Buku Tamu</h1>
                    <p class="text-gray-400 text-sm mt-0.5">Tempel di pintu masuk agar tamu scan & isi data via HP</p>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0 no-print">
                    <button type="button" onclick="window.print()" class="btn-primary py-2.5 px-5 text-sm rounded-xl">
                        <i data-lucide="printer" class="w-4 h-4"></i> Cetak
                    </button>
                    <a href="<?= e($scanUrl) ?>" target="_blank" class="px-4 py-2.5 border border-pesantren-200 text-pesantren-700 rounded-xl text-sm font-medium hover:bg-pesantren-50 flex items-center gap-2">
                        <i data-lucide="external-link" class="w-4 h-4"></i> Buka Form
                    </a>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6 no-print mb-6">
                <div class="card p-5">
                    <h2 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                        <i data-lucide="link" class="w-4 h-4 text-pesantren-600"></i> Link Form Tamu
                    </h2>
                    <p class="text-sm font-mono text-pesantren-700 bg-pesantren-50 p-3 rounded-xl break-all"><?= e($scanUrl) ?></p>
                    <button type="button" onclick="copyUrl()" class="mt-3 text-sm text-pesantren-600 font-medium hover:underline">Salin link</button>
                </div>
                <div class="card p-5">
                    <h2 class="font-semibold text-gray-800 mb-2">Ukuran Cetak</h2>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setPrintSize('poster')" class="px-4 py-2 rounded-lg border text-sm font-medium print-size-btn active-size">Poster A4</button>
                        <button type="button" onclick="setPrintSize('medium')" class="px-4 py-2 rounded-lg border text-sm font-medium print-size-btn">Kartu A5</button>
                        <button type="button" onclick="setPrintSize('small')" class="px-4 py-2 rounded-lg border text-sm font-medium print-size-btn">Stiker Meja</button>
                    </div>
                </div>
            </div>

            <!-- Printable area -->
            <div class="flex justify-center print-area-wrapper">
                <div id="printArea" class="print-area size-poster bg-white rounded-2xl shadow-xl overflow-hidden border" data-size="poster">
                    <div class="bg-hero text-white text-center py-8 px-6 relative">
                        <div class="relative z-10">
                            <?php
                            $brandSize = 'md';
                            $brandIcon = 'book-open';
                            $brandMargin = 'mb-3';
                            require __DIR__ . '/../partials/pesantren_brand.php';
                            ?>
                            <h1 class="font-display text-2xl font-bold print-title"><?= e($app['pesantren_name']) ?></h1>
                            <p class="text-emerald-200 text-xs mt-1 tracking-widest uppercase">Buku Tamu Digital</p>
                        </div>
                    </div>
                    <div class="p-8 text-center">
                        <div class="inline-block p-3 rounded-2xl border-2 border-pesantren-100 mb-4 qr-wrap">
                            <img src="<?= qr_url($scanUrl, 280) ?>" alt="QR Code" class="qr-img mx-auto" id="qrImage">
                        </div>
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Scan untuk Isi Buku Tamu</h2>
                        <p class="text-gray-500 text-sm mb-4">Arahkan kamera HP ke QR Code</p>
                        <div class="flex justify-center gap-4 text-[10px] text-gray-400">
                            <span>Tanpa login</span><span>·</span><span>Cepat & mudah</span><span>·</span><span>Aman</span>
                        </div>
                    </div>
                    <div class="bg-pesantren-800 text-white text-center py-2 text-[10px]">
                        <?= e(HijriDate::toHijri()) ?> · <?= date('d F Y') ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.print-size-btn.active-size { background: #047857; color: white; border-color: #047857; }
.print-area.size-poster { max-width: 420px; width: 100%; }
.print-area.size-poster .qr-img { width: 260px; height: 260px; }
.print-area.size-medium { max-width: 320px; }
.print-area.size-medium .qr-img { width: 200px; height: 200px; }
.print-area.size-medium .print-title { font-size: 1.25rem; }
.print-area.size-small { max-width: 220px; }
.print-area.size-small .qr-img { width: 140px; height: 140px; }
.print-area.size-small .print-title { font-size: 1rem; }
.print-area.size-small h2 { font-size: 0.875rem; }
.print-area.size-poster .pesantren-logo { max-height: 3rem; max-width: 5.5rem; }
.print-area.size-medium .pesantren-logo { max-height: 2.75rem; max-width: 5rem; }
.print-area.size-small .pesantren-logo { max-height: 2.5rem; max-width: 4.5rem; }
@media print {
    body * { visibility: hidden; }
    .print-area-wrapper, .print-area-wrapper * { visibility: visible; }
    .print-area-wrapper { position: absolute; left: 0; top: 0; width: 100%; display: flex; justify-content: center; }
    .no-print { display: none !important; }
    aside, header, nav { display: none !important; }
    .print-area { box-shadow: none !important; border: 1px solid #ccc !important; }
}
</style>

<script>
const scanUrl = <?= json_encode($scanUrl) ?>;
const qrSizes = { poster: 280, medium: 200, small: 140 };

function copyUrl() {
    navigator.clipboard.writeText(scanUrl).then(() => alert('Link form tamu disalin!'));
}

function setPrintSize(size) {
    const area = document.getElementById('printArea');
    area.className = 'print-area bg-white rounded-2xl shadow-xl overflow-hidden border size-' + size;
    area.dataset.size = size;
    document.getElementById('qrImage').src = 'https://api.qrserver.com/v1/create-qr-code/?size=' + qrSizes[size] + 'x' + qrSizes[size] + '&data=' + encodeURIComponent(scanUrl);
    document.querySelectorAll('.print-size-btn').forEach(b => b.classList.remove('active-size'));
    event.target.classList.add('active-size');
}

if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
