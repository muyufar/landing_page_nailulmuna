<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/HijriDate.php';
require_once __DIR__ . '/../src/SettingsModel.php';
require_once __DIR__ . '/../src/helpers.php';

$app = app_config();
$scanUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . base_url('/');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Buku Tamu — <?= htmlspecialchars($app['pesantren_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('/assets/css/app.css') ?>">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>@media print { .no-print { display: none !important; } body { background: white !important; } }</style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-6 md:p-12">
    <div class="max-w-lg w-full animate-fade-up">
        <div class="card overflow-hidden shadow-2xl">
            <!-- Header -->
            <div class="bg-hero text-white text-center py-10 px-8 relative">
                <div class="relative z-10 qr-poster-brand">
                    <?php
                    $brandSize = 'md';
                    $brandIcon = 'book-open';
                    $brandMargin = 'mb-4';
                    require __DIR__ . '/../views/partials/pesantren_brand.php';
                    ?>
                    <h1 class="font-display text-2xl md:text-3xl font-bold"><?= htmlspecialchars($app['pesantren_name']) ?></h1>
                    <p class="text-emerald-200 text-sm mt-2 font-medium tracking-widest uppercase">Buku Tamu Digital</p>
                </div>
            </div>

            <!-- QR Section -->
            <div class="p-8 md:p-10 text-center bg-white">
                <div class="inline-block p-4 rounded-2xl border-2 border-pesantren-100 shadow-lg mb-6">
                    <img src="<?= qr_url($scanUrl, 260) ?>" alt="Scan QR" class="rounded-lg">
                </div>

                <div class="flex items-center justify-center gap-2 mb-2">
                    <i data-lucide="scan-line" class="w-5 h-5 text-pesantren-600"></i>
                    <h2 class="text-xl font-bold text-gray-800">Scan untuk Isi Buku Tamu</h2>
                </div>
                <p class="text-gray-500 text-sm">Arahkan kamera HP ke QR Code di atas</p>

                <div class="mt-6 flex items-center justify-center gap-6 text-xs text-gray-400">
                    <span class="flex items-center gap-1"><i data-lucide="smartphone" class="w-3.5 h-3.5"></i> Tanpa login</span>
                    <span class="flex items-center gap-1"><i data-lucide="zap" class="w-3.5 h-3.5"></i> Cepat & mudah</span>
                    <span class="flex items-center gap-1"><i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Aman</span>
                </div>

                <p class="text-[10px] text-gray-300 mt-6 break-all"><?= htmlspecialchars($scanUrl) ?></p>
            </div>

            <!-- Footer strip -->
            <div class="bg-gradient-to-r from-pesantren-800 to-pesantren-700 text-white text-center py-3 text-xs">
                <?= e(HijriDate::toHijri()) ?> · <?= date('d F Y') ?>
            </div>
        </div>

        <button onclick="window.print()" class="no-print btn-primary w-full mt-6 py-3.5 rounded-2xl">
            <i data-lucide="printer" class="w-4 h-4"></i>
            Cetak Poster QR
        </button>
    </div>

    <script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
</body>
</html>
