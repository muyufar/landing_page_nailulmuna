<?php
$title = 'Pengaturan WhatsApp';
$adminPage = 'whatsapp';
$mobileTitle = 'WhatsApp';
$s = $settings;
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/admin_mobile_header.php'; ?>

        <main class="p-4 md:p-8 max-w-3xl">
            <div class="mb-6 animate-fade-up">
                <h1 class="text-2xl font-bold text-gray-800">Notifikasi WhatsApp Otomatis</h1>
                <p class="text-gray-400 text-sm mt-0.5">Kirim pesan ke pengasuh, asisten ndalem, dan petugas kantor — <strong class="text-pesantren-600">langsung saat kejadian</strong></p>
            </div>

            <?php if (!empty($success)): ?>
            <div class="card bg-emerald-50 border-emerald-200 text-emerald-800 px-4 py-3 mb-4 text-sm flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i><?= e($success) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <!-- Gateway -->
                <div class="card p-6 animate-fade-up">
                    <div class="flex items-center gap-2 mb-5">
                        <i data-lucide="message-circle" class="w-5 h-5 text-pesantren-600"></i>
                        <h2 class="font-semibold text-gray-800">Gateway WhatsApp</h2>
                    </div>
                    <label class="flex items-center gap-3 mb-4 cursor-pointer">
                        <input type="checkbox" name="whatsapp_enabled" value="1" <?= ($s['whatsapp_enabled'] ?? '0') === '1' ? 'checked' : '' ?>
                               class="w-5 h-5 rounded text-pesantren-600">
                        <span class="text-sm font-medium">Aktifkan notifikasi WhatsApp</span>
                    </label>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Provider</label>
                            <select name="whatsapp_provider" class="input-field no-icon w-full">
                                <option value="fonnte" <?= ($s['whatsapp_provider'] ?? '') === 'fonnte' ? 'selected' : '' ?>>Fonnte</option>
                                <option value="wablas" <?= ($s['whatsapp_provider'] ?? '') === 'wablas' ? 'selected' : '' ?>>Wablas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">API Token</label>
                            <input type="text" name="whatsapp_token" value="<?= e($s['whatsapp_token'] ?? '') ?>"
                                   class="input-field no-icon w-full" placeholder="Token dari Fonnte/Wablas">
                        </div>
                    </div>
                </div>

                <!-- Recipients -->
                <?php
                $roles = [
                    'pengasuh' => ['label' => 'Pengasuh', 'icon' => 'heart-handshake', 'desc' => 'Notifikasi tamu sowan ke ndalem'],
                    'ndalem' => ['label' => 'Asisten Ndalem', 'icon' => 'home', 'desc' => 'Antrean & tamu masuk ndalem'],
                    'kantor' => ['label' => 'Petugas Kantor', 'icon' => 'building-2', 'desc' => 'Tamu masuk area pesantren (non-sowan)'],
                ];
                foreach ($roles as $key => $meta):
                ?>
                <div class="card p-6 animate-fade-up">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-pesantren-50 flex items-center justify-center">
                            <i data-lucide="<?= $meta['icon'] ?>" class="w-5 h-5 text-pesantren-700"></i>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-800"><?= e($meta['label']) ?></h2>
                            <p class="text-xs text-gray-400"><?= e($meta['desc']) ?></p>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 mb-3 cursor-pointer">
                        <input type="checkbox" name="wa_enabled_<?= $key ?>" value="1"
                               <?= ($s["wa_enabled_$key"] ?? '1') === '1' ? 'checked' : '' ?> class="rounded text-pesantren-600">
                        <span class="text-sm">Aktifkan notifikasi</span>
                    </label>
                    <div class="input-wrap mb-4">
                        <i data-lucide="phone" class="input-icon w-4 h-4"></i>
                        <input type="tel" name="wa_phone_<?= $key ?>" value="<?= e($s["wa_phone_$key"] ?? '') ?>"
                               placeholder="08xxxxxxxxxx (WhatsApp)" class="input-field">
                    </div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Kirim saat:</p>
                    <div class="grid sm:grid-cols-3 gap-2">
                        <label class="flex items-center gap-2 p-2.5 border rounded-lg cursor-pointer hover:bg-gray-50 text-sm">
                            <input type="checkbox" name="wa_on_register_<?= $key ?>" value="1"
                                   <?= ($s["wa_on_register_$key"] ?? '1') === '1' ? 'checked' : '' ?> class="rounded text-pesantren-600">
                            Tamu daftar
                        </label>
                        <label class="flex items-center gap-2 p-2.5 border rounded-lg cursor-pointer hover:bg-gray-50 text-sm">
                            <input type="checkbox" name="wa_on_checkin_<?= $key ?>" value="1"
                                   <?= ($s["wa_on_checkin_$key"] ?? '1') === '1' ? 'checked' : '' ?> class="rounded text-pesantren-600">
                            Tamu check-in
                        </label>
                        <label class="flex items-center gap-2 p-2.5 border rounded-lg cursor-pointer hover:bg-gray-50 text-sm">
                            <input type="checkbox" name="wa_on_jadwal_<?= $key ?>" value="1"
                                   <?= ($s["wa_on_jadwal_$key"] ?? '1') === '1' ? 'checked' : '' ?> class="rounded text-pesantren-600">
                            Pengingat jadwal
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Lainnya -->
                <div class="card p-6 animate-fade-up">
                    <h2 class="font-semibold text-gray-800 mb-4">Pengaturan Lainnya</h2>
                    <div class="space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="wa_on_register_guest" value="1"
                                   <?= ($s['wa_on_register_guest'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded text-pesantren-600">
                            <span class="text-sm">Kirim WA ke <strong>tamu</strong> segera setelah daftar (konfirmasi antrean & tiket)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="wa_on_approve_guest" value="1"
                                   <?= ($s['wa_on_approve_guest'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded text-pesantren-600">
                            <span class="text-sm">Kirim WA ke <strong>tamu</strong> saat jadwal temu ditetapkan (berisi waktu & lokasi pertemuan)</span>
                        </label>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pengingat jadwal (menit sebelum waktu kunjungan)</label>
                            <input type="number" name="wa_jadwal_reminder_minutes" min="5" max="1440"
                                   value="<?= e($s['wa_jadwal_reminder_minutes'] ?? '60') ?>"
                                   class="input-field no-icon w-32">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Ruang tunggu ndalem (pesan ke tamu)</label>
                            <input type="text" name="ndalem_ruang" value="<?= e($s['ndalem_ruang'] ?? '') ?>"
                                   class="input-field no-icon w-full">
                        </div>
                    </div>
                </div>

                <div class="card p-4 bg-blue-50 border-blue-100 text-sm text-blue-800">
                    <p class="font-semibold mb-1 flex items-center gap-2"><i data-lucide="info" class="w-4 h-4"></i> Cron pengingat jadwal</p>
                    <p class="text-blue-700/80">Jalankan setiap 5–15 menit via Task Scheduler:</p>
                    <code class="block mt-2 text-xs bg-white/80 p-2 rounded-lg break-all">C:\xampp\php\php.exe C:\xampp\htdocs\BUKU_TAMU\public\cron\wa-jadwal.php</code>
                </div>

                <button type="submit" class="btn-primary w-full py-3.5 rounded-xl">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
                </button>
            </form>
        </main>
    </div>
</div>
<script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
