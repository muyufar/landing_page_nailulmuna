<?php $title = 'Isi Buku Tamu'; ?>

<div class="min-h-screen bg-pattern">
    <!-- Hero Header -->
    <header class="bg-hero text-white pt-10 pb-16 px-4 relative">
        <div class="max-w-lg mx-auto text-center relative z-10 animate-fade-up">
            <?php
            $brandSize = 'lg';
            $brandIcon = 'book-open';
            $brandMargin = 'mb-4';
            require __DIR__ . '/../partials/pesantren_brand.php';
            ?>
            <h1 class="font-display text-2xl md:text-3xl font-bold tracking-tight"><?= e($app['pesantren_name']) ?></h1>
            <?php if (!empty($app['pesantren_address'])): ?>
            <p class="text-emerald-100/80 text-xs mt-1"><?= e($app['pesantren_address']) ?></p>
            <?php endif; ?>
            <p class="text-emerald-100/90 text-sm mt-2 font-medium">Buku Tamu Digital</p>
            <div class="inline-flex items-center gap-2 mt-4 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur text-xs text-emerald-100 border border-white/10">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span><?= e(HijriDate::toHijri()) ?></span>
                <span class="opacity-50">·</span>
                <span><?= date('d F Y') ?></span>
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 -mt-8 pb-28 relative z-10">
        <?php if (!empty($error)): ?>
        <div class="card bg-red-50 border-red-200 text-red-700 px-4 py-3 mb-4 text-sm flex items-start gap-2 animate-fade-up">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <span><?= e($error) ?></span>
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="card bg-red-50 border-red-200 text-red-700 px-4 py-3 mb-4 text-sm animate-fade-up">
            <div class="flex items-center gap-2 font-semibold mb-1"><i data-lucide="alert-triangle" class="w-4 h-4"></i> Periksa kembali:</div>
            <ul class="list-disc list-inside space-y-0.5 ml-6">
                <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php
        $pengasuhStatus = $pengasuh['status'] ?? 'available';
        $statusConfig = [
            'available' => ['bg-emerald-50 border-emerald-200', 'text-emerald-700', 'check-circle', 'Tersedia'],
            'busy' => ['bg-amber-50 border-amber-200', 'text-amber-700', 'clock', 'Sibuk'],
            'closed' => ['bg-red-50 border-red-200', 'text-red-700', 'x-circle', 'Tidak Menerima'],
        ];
        [$statusBg, $statusText, $statusIcon, $statusShort] = $statusConfig[$pengasuhStatus] ?? $statusConfig['available'];
        ?>
        <div class="card <?= $statusBg ?> border px-4 py-3.5 mb-5 flex items-start gap-3 animate-fade-up animate-delay-1">
            <div class="w-10 h-10 rounded-xl bg-white/80 flex items-center justify-center flex-shrink-0">
                <i data-lucide="<?= $statusIcon ?>" class="w-5 h-5 <?= $statusText ?>"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <p class="font-semibold text-sm text-gray-800">Status Pengasuh</p>
                    <span class="status-pill bg-white/70 <?= $statusText ?> text-xs"><?= $statusShort ?></span>
                </div>
                <p class="text-sm text-gray-600 mt-0.5"><?= e($pengasuhModel->getStatusLabel($pengasuhStatus)) ?></p>
                <?php if (!empty($pengasuh['message'])): ?>
                <p class="text-xs text-gray-500 mt-1"><?= e($pengasuh['message']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php require __DIR__ . '/../partials/guest_jadwal_terima.php'; ?>

        <form action="<?= base_url('/submit') ?>" method="POST" enctype="multipart/form-data" id="guestForm">
            <!-- Section 1: Data Tamu -->
            <div class="card p-5 mb-4 animate-fade-up animate-delay-1">
                <div class="section-header">
                    <div class="section-number">1</div>
                    <div>
                        <h2 class="font-semibold text-gray-800">Data Tamu</h2>
                        <p class="text-xs text-gray-400">Identitas pengunjung</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                        <div class="input-wrap">
                            <i data-lucide="user" class="input-icon w-4 h-4"></i>
                            <input type="text" name="nama_lengkap" required
                                   value="<?= e($old['nama_lengkap'] ?? '') ?>"
                                   placeholder="Nama perwakilan / individu"
                                   class="input-field">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No. HP (WhatsApp) <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <div class="input-wrap">
                            <i data-lucide="phone" class="input-icon w-4 h-4"></i>
                            <input type="tel" name="no_hp"
                                   value="<?= e($old['no_hp'] ?? '') ?>"
                                   placeholder="08xxxxxxxxxx (jika ada)"
                                   class="input-field">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Asal Daerah / Instansi <span class="text-red-400">*</span></label>
                        <div class="input-wrap">
                            <i data-lucide="map-pin" class="input-icon w-4 h-4"></i>
                            <input type="text" name="asal" required
                                   value="<?= e($old['asal'] ?? '') ?>"
                                   placeholder="Contoh: Kudus atau Kemenag Kabupaten"
                                   class="input-field">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Rombongan</label>
                        <div class="input-wrap">
                            <i data-lucide="users" class="input-icon w-4 h-4"></i>
                            <select name="jumlah_rombongan" class="input-field appearance-none cursor-pointer">
                                <?php foreach ($app['rombongan_options'] as $val => $label): ?>
                                <option value="<?= e($val) ?>" <?= ($old['jumlah_rombongan'] ?? '1') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Tujuan -->
            <div class="card p-5 mb-4 animate-fade-up animate-delay-2">
                <div class="section-header">
                    <div class="section-number">2</div>
                    <div>
                        <h2 class="font-semibold text-gray-800">Tujuan Kunjungan</h2>
                        <p class="text-xs text-gray-400">Pilih keperluan Anda</p>
                    </div>
                </div>

                <?php
                $purposeIcons = [
                    'sowan' => 'heart-handshake',
                    'jenguk' => 'graduation-cap',
                    'administrasi' => 'file-text',
                    'kerjasama' => 'building-2',
                    'lainnya' => 'more-horizontal',
                ];
                ?>
                <div class="grid grid-cols-2 gap-2.5 mb-4">
                    <?php foreach ($app['tujuan_options'] as $val => $label):
                        $disabled = ($val === 'sowan' && $pengasuhStatus === 'closed');
                        $checked = ($old['tujuan_kunjungan'] ?? '') === $val;
                    ?>
                    <label class="purpose-card <?= $disabled ? 'disabled' : '' ?> <?= $checked ? 'selected' : '' ?>" data-purpose="<?= e($val) ?>">
                        <input type="radio" name="tujuan_kunjungan" value="<?= e($val) ?>"
                               <?= $checked ? 'checked' : '' ?> <?= $disabled ? 'disabled' : '' ?>>
                        <div class="flex flex-col items-center text-center gap-2 py-1">
                            <div class="w-10 h-10 rounded-xl bg-pesantren-50 flex items-center justify-center">
                                <i data-lucide="<?= $purposeIcons[$val] ?? 'circle' ?>" class="w-5 h-5 text-pesantren-700"></i>
                            </div>
                            <span class="text-xs font-semibold text-gray-700 leading-tight"><?= e($label) ?></span>
                            <?php if ($disabled): ?>
                            <span class="text-[10px] text-red-500 font-medium">Tutup</span>
                            <?php endif; ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>

                <div id="namaSantriField" class="hidden mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Santri yang Dijenguk <span class="text-red-400">*</span></label>
                    <div class="input-wrap">
                        <i data-lucide="graduation-cap" class="input-icon w-4 h-4"></i>
                        <input type="text" name="nama_santri"
                               value="<?= e($old['nama_santri'] ?? '') ?>"
                               placeholder="Nama lengkap santri"
                               class="input-field">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Detail Keperluan</label>
                    <textarea name="detail_keperluan" rows="3"
                              placeholder="Contoh: Konsultasi pernikahan anak / Silahturahmi keluarga"
                              class="input-field no-icon resize-none"><?= e($old['detail_keperluan'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Foto Selfie / KTP <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-pesantren-300 hover:bg-pesantren-50/50 transition-all">
                        <i data-lucide="camera" class="w-8 h-8 text-gray-300 mb-1"></i>
                        <span class="text-xs text-gray-400">Ketuk untuk ambil foto</span>
                        <input type="file" name="foto" accept="image/*" capture="user" class="hidden" id="fotoInput">
                    </label>
                    <p id="fotoName" class="text-xs text-pesantren-600 mt-1 hidden"></p>
                </div>
            </div>

            <!-- Section 3: Waktu Kedatangan -->
            <div class="card p-5 mb-4 animate-fade-up animate-delay-2">
                <div class="section-header">
                    <div class="section-number">3</div>
                    <div>
                        <h2 class="font-semibold text-gray-800">Waktu Kedatangan</h2>
                        <p class="text-xs text-gray-400">Kapan Anda akan masuk pesantren / ndalem</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5 mb-4">
                    <label class="purpose-card kedatangan-option <?= ($old['jenis_kedatangan'] ?? 'sekarang') === 'sekarang' ? 'selected' : '' ?>">
                        <input type="radio" name="jenis_kedatangan" value="sekarang"
                               <?= ($old['jenis_kedatangan'] ?? 'sekarang') === 'sekarang' ? 'checked' : '' ?>>
                        <div class="flex flex-col items-center text-center gap-2 py-1">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <i data-lucide="zap" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">Datang Sekarang</span>
                        </div>
                    </label>
                    <label class="purpose-card kedatangan-option <?= ($old['jenis_kedatangan'] ?? '') === 'jadwal' ? 'selected' : '' ?>">
                        <input type="radio" name="jenis_kedatangan" value="jadwal"
                               <?= ($old['jenis_kedatangan'] ?? '') === 'jadwal' ? 'checked' : '' ?>>
                        <div class="flex flex-col items-center text-center gap-2 py-1">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                                <i data-lucide="calendar-clock" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">Jadwalkan</span>
                        </div>
                    </label>
                </div>

                <div id="jadwalField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal & Waktu Kunjungan <span class="text-red-400">*</span></label>
                    <div class="input-wrap">
                        <i data-lucide="calendar" class="input-icon w-4 h-4"></i>
                        <input type="datetime-local" name="jadwal_kunjungan"
                               value="<?= e($old['jadwal_kunjungan'] ?? '') ?>"
                               min="<?= date('Y-m-d\TH:i') ?>"
                               class="input-field">
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">Petugas akan menerima notifikasi WhatsApp sebelum waktu kunjungan</p>
                </div>

                <div id="areaInfo" class="mt-3 p-3 rounded-xl bg-pesantren-50 border border-pesantren-100 text-xs text-pesantren-800">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 inline"></i>
                    <span id="areaInfoText">Area masuk akan ditentukan dari tujuan kunjungan</span>
                </div>
            </div>

            <!-- Submit -->
            <div class="animate-fade-up animate-delay-3">
                <button type="submit" class="btn-primary w-full py-4 text-base rounded-2xl">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                    Kirim & Dapatkan Tiket
                </button>
                <p class="text-center text-xs text-gray-400 mt-3 flex items-center justify-center gap-1">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    Data Anda aman & terenkripsi
                </p>
            </div>
        </form>
    </main>
</div>

<script>
document.querySelectorAll('.purpose-card:not(.disabled)').forEach(card => {
    card.addEventListener('click', () => {
        if (card.classList.contains('kedatangan-option')) {
            document.querySelectorAll('.kedatangan-option').forEach(c => c.classList.remove('selected'));
        } else {
            document.querySelectorAll('.purpose-card:not(.kedatangan-option)').forEach(c => c.classList.remove('selected'));
        }
        card.classList.add('selected');
        card.querySelector('input').checked = true;
        toggleSantriField();
        toggleJadwalField();
        updateAreaInfo();
        if (typeof updateJadwalDisplay === 'function') updateJadwalDisplay();
    });
});

document.querySelectorAll('input[name="tujuan_kunjungan"]').forEach(radio => {
    radio.addEventListener('change', () => { toggleSantriField(); updateAreaInfo(); updateJadwalDisplay(); });
});

document.querySelectorAll('input[name="jenis_kedatangan"]').forEach(radio => {
    radio.addEventListener('change', toggleJadwalField);
});

function toggleJadwalField() {
    const selected = document.querySelector('input[name="jenis_kedatangan"]:checked');
    const field = document.getElementById('jadwalField');
    const input = field.querySelector('input');
    if (selected && selected.value === 'jadwal') {
        field.classList.remove('hidden');
        input.required = true;
    } else {
        field.classList.add('hidden');
        input.required = false;
    }
}

function updateAreaInfo() {
    const tujuan = document.querySelector('input[name="tujuan_kunjungan"]:checked');
    const el = document.getElementById('areaInfoText');
    if (!tujuan) { el.textContent = 'Pilih tujuan kunjungan terlebih dahulu'; return; }
    el.textContent = tujuan.value === 'sowan'
        ? 'Anda akan masuk ke area Ndalem (Sowan Pengasuh)'
        : 'Anda akan masuk ke area Pesantren (Pintu utama / kantor)';
}

function toggleSantriField() {
    const selected = document.querySelector('input[name="tujuan_kunjungan"]:checked');
    const field = document.getElementById('namaSantriField');
    const input = field.querySelector('input');
    if (selected && selected.value === 'jenguk') {
        field.classList.remove('hidden');
        input.required = true;
    } else {
        field.classList.add('hidden');
        input.required = false;
    }
}

document.getElementById('fotoInput')?.addEventListener('change', function() {
    const el = document.getElementById('fotoName');
    if (this.files[0]) {
        el.textContent = '✓ ' + this.files[0].name;
        el.classList.remove('hidden');
    }
});

toggleSantriField();
toggleJadwalField();
updateAreaInfo();
if (typeof updateJadwalDisplay === 'function') updateJadwalDisplay();
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
