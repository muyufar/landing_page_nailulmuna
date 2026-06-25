<?php
$title = 'Identitas Pesantren';
$adminPage = 'identitas';
$s = $settings;
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/admin_mobile_header.php'; ?>

        <main class="p-4 md:p-8 max-w-3xl">
            <div class="mb-6 animate-fade-up">
                <h1 class="text-2xl font-bold text-gray-800">Identitas Pesantren</h1>
                <p class="text-gray-400 text-sm mt-0.5">Nama dan logo tampil di form tamu, login, cetak QR, stiker, dan pesan WhatsApp</p>
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

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="card p-6 animate-fade-up">
                    <div class="flex items-center gap-2 mb-5">
                        <i data-lucide="building-2" class="w-5 h-5 text-pesantren-600"></i>
                        <h2 class="font-semibold text-gray-800">Informasi Umum</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pesantren <span class="text-red-500">*</span></label>
                            <input type="text" name="pesantren_name" required
                                   value="<?= e($s['pesantren_name'] ?? '') ?>"
                                   class="input-field no-icon w-full" placeholder="Contoh: Pesantren Al-Hikmah">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                            <textarea name="pesantren_address" rows="2"
                                      class="input-field no-icon w-full resize-none"
                                      placeholder="Alamat lengkap pesantren"><?= e($s['pesantren_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card p-6 animate-fade-up animate-delay-1">
                    <div class="flex items-center gap-2 mb-5">
                        <i data-lucide="image" class="w-5 h-5 text-pesantren-600"></i>
                        <h2 class="font-semibold text-gray-800">Logo Pesantren</h2>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-6 items-start">
                        <div class="flex-shrink-0">
                            <p class="text-xs text-gray-500 mb-2">Pratinjau</p>
                            <div class="w-48 min-h-[8rem] rounded-2xl bg-pesantren-50 border-2 border-dashed border-pesantren-200 flex items-center justify-center p-4">
                                <?php if (!empty($s['pesantren_logo'])): ?>
                                    <img src="<?= e(base_url($s['pesantren_logo'])) ?>" alt="Logo" id="logoPreview"
                                         class="pesantren-logo pesantren-logo--lg">
                                <?php else: ?>
                                    <div id="logoPreviewPlaceholder" class="text-center text-gray-400">
                                        <i data-lucide="image-off" class="w-8 h-8 mx-auto mb-1 opacity-50"></i>
                                        <span class="text-[10px]">Belum ada logo</span>
                                    </div>
                                    <img src="" alt="Logo" id="logoPreview" class="pesantren-logo pesantren-logo--lg hidden">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex-1 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload logo baru</label>
                                <input type="file" name="pesantren_logo" id="logoInput" accept="image/jpeg,image/png,image/webp"
                                       class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-pesantren-50 file:text-pesantren-700 file:font-medium hover:file:bg-pesantren-100">
                                <p class="text-xs text-gray-400 mt-1.5">PNG/JPG/WebP, maks. 5 MB. Disarankan persegi atau transparan.</p>
                            </div>
                            <?php if (!empty($s['pesantren_logo'])): ?>
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-red-600">
                                <input type="checkbox" name="remove_logo" value="1" class="w-4 h-4 rounded">
                                Hapus logo saat ini
                            </label>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card p-4 bg-blue-50 border-blue-100 text-sm text-blue-800">
                    <p class="font-medium mb-1">Akan tampil di:</p>
                    <p>Form buku tamu · Halaman login · Poster QR · Stiker tamu · Notifikasi WhatsApp</p>
                </div>

                <button type="submit" class="btn-primary py-3 px-8 rounded-xl">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Identitas
                </button>
            </form>
        </main>
    </div>
</div>

<script>
document.getElementById('logoInput')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (ev) {
        const img = document.getElementById('logoPreview');
        const ph = document.getElementById('logoPreviewPlaceholder');
        if (img) {
            img.src = ev.target.result;
            img.classList.remove('hidden');
        }
        if (ph) ph.classList.add('hidden');
    };
    reader.readAsDataURL(file);
});
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
