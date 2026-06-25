<?php $title = 'Tiket Kunjungan'; ?>
<?php
$ticketUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . base_url('/ticket/' . $visitor['ticket_code']);
?>

<div class="min-h-screen bg-pattern flex items-center justify-center px-4 py-8">
    <div class="max-w-md w-full animate-fade-up">
        <div class="ticket-card">
            <!-- Header -->
            <div class="bg-gradient-to-r from-pesantren-800 via-pesantren-700 to-pesantren-800 text-white text-center py-8 px-6 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10" style="background: url('data:image/svg+xml,%3Csvg width=%2240%22 height=%2240%22 viewBox=%220 0 40 40%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22%23ffffff%22%3E%3Cpath d=%22M0 0h20v20H0V0zm20 20h20v20H20V20z%22/%3E%3C/g%3E%3C/svg%3E')"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/15 backdrop-blur border border-white/20 mb-3">
                        <i data-lucide="check-circle" class="w-8 h-8 text-gold-400"></i>
                    </div>
                    <h1 class="font-display text-xl font-bold">Pendaftaran Berhasil!</h1>
                    <p class="text-emerald-200 text-sm mt-1">Tunjukkan tiket ini ke petugas keamanan</p>
                </div>
            </div>

            <div class="p-6">
                <!-- Queue Number -->
                <div class="text-center mb-6">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">Nomor Antrean</p>
                    <div class="inline-block relative">
                        <div class="text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-br from-pesantren-700 to-pesantren-500 leading-none">
                            <?= str_pad($visitor['queue_number'], 3, '0', STR_PAD_LEFT) ?>
                        </div>
                    </div>
                </div>

                <!-- Perforation -->
                <div class="ticket-perforation">
                    <div class="ticket-perforation-line"></div>
                </div>

                <!-- QR & Code -->
                <div class="flex flex-col items-center gap-3 mb-6">
                    <div class="p-3 bg-white rounded-2xl shadow-md border-2 border-pesantren-100">
                        <img src="<?= qr_url($ticketUrl, 160) ?>" alt="QR Code Tiket" class="rounded-lg">
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Kode Tiket</p>
                        <p class="text-xl font-mono font-bold tracking-[0.3em] text-pesantren-800"><?= e($visitor['ticket_code']) ?></p>
                    </div>
                </div>

                <!-- Details -->
                <div class="bg-gradient-to-br from-gray-50 to-pesantren-50/30 rounded-2xl p-4 space-y-3 text-sm border border-gray-100">
                    <?php
                    $details = [
                        ['user', 'Nama', $visitor['nama_lengkap']],
                        ['target', 'Tujuan', tujuan_label($visitor['tujuan_kunjungan'])],
                        ['map-pin', 'Area Masuk', area_masuk_label($visitor['area_masuk'] ?? area_masuk_from_tujuan($visitor['tujuan_kunjungan']))],
                        ['clock', 'Waktu', visitor_waktu_label($visitor)],
                        ['users', 'Rombongan', $app['rombongan_options'][$visitor['jumlah_rombongan']] ?? $visitor['jumlah_rombongan']],
                        ['calendar', 'Daftar', date('d/m/Y H:i', strtotime($visitor['created_at']))],
                        ['moon', 'Hijriah', $visitor['hijri_date']],
                    ];
                    foreach ($details as [$icon, $label, $value]):
                    ?>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-gray-500">
                            <i data-lucide="<?= $icon ?>" class="w-3.5 h-3.5"></i>
                            <?= e($label) ?>
                        </span>
                        <span class="font-semibold text-gray-800 text-right max-w-[55%]"><?= e($value) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-5 flex items-center gap-2 justify-center text-xs text-gray-400">
                    <i data-lucide="smartphone" class="w-3.5 h-3.5"></i>
                    Simpan screenshot atau tunjukkan langsung ke petugas
                </div>
            </div>
        </div>

        <a href="<?= base_url('/') ?>" class="flex items-center justify-center gap-2 mt-5 text-pesantren-700 text-sm font-semibold hover:text-pesantren-900 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Formulir
        </a>
    </div>
</div>

<script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
