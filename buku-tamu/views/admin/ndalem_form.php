<?php
$title = ($mode === 'edit' ? 'Edit' : 'Tambah') . ' Akun Ndalem';
$adminPage = 'ndalem';
$mobileTitle = ($mode === 'edit' ? 'Edit' : 'Tambah') . ' Akun';
$mobileBackUrl = base_url('/admin/pengaturan/ndalem');
$isEdit = $mode === 'edit';
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/admin_mobile_header.php'; ?>

        <main class="p-4 md:p-8 max-w-2xl">
            <div class="mb-6 animate-fade-up">
                <a href="<?= base_url('/admin/pengaturan/ndalem') ?>" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-pesantren-700 mb-3 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
                <h1 class="text-2xl font-bold text-gray-800"><?= $isEdit ? 'Edit Akun Ndalem' : 'Tambah Akun Ndalem' ?></h1>
                <p class="text-gray-400 text-sm mt-0.5"><?= $isEdit ? 'Perbarui data asisten pengasuh' : 'Buat akun baru untuk asisten pengasuh' ?></p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="card bg-red-50 border-red-200 text-red-700 px-4 py-3 mb-4 text-sm animate-fade-up">
                <ul class="list-disc list-inside space-y-0.5">
                    <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="card p-6 md:p-8 animate-fade-up animate-delay-1">
                <form method="POST" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                        <div class="input-wrap">
                            <i data-lucide="user" class="input-icon w-4 h-4"></i>
                            <input type="text" name="name" required
                                   value="<?= e($old['name'] ?? '') ?>"
                                   placeholder="Contoh: Ustadz Ahmad"
                                   class="input-field">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Username <span class="text-red-400">*</span></label>
                        <div class="input-wrap">
                            <i data-lucide="at-sign" class="input-icon w-4 h-4"></i>
                            <input type="text" name="username" required
                                   value="<?= e($old['username'] ?? '') ?>"
                                   placeholder="Contoh: asisten_ndalem"
                                   pattern="[a-zA-Z0-9_]+"
                                   class="input-field">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Hanya huruf, angka, dan underscore</p>
                    </div>

                    <?php if (!$isEdit): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-red-400">*</span></label>
                        <div class="input-wrap">
                            <i data-lucide="lock" class="input-icon w-4 h-4"></i>
                            <input type="password" name="password" required minlength="6"
                                   placeholder="Min. 6 karakter"
                                   class="input-field">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password <span class="text-red-400">*</span></label>
                        <div class="input-wrap">
                            <i data-lucide="lock" class="input-icon w-4 h-4"></i>
                            <input type="password" name="password_confirm" required minlength="6"
                                   placeholder="Ulangi password"
                                   class="input-field">
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="flex gap-3 pt-2">
                        <a href="<?= base_url('/admin/pengaturan/ndalem') ?>" class="flex-1 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 text-center transition">Batal</a>
                        <button type="submit" class="btn-primary flex-1 py-3 text-sm rounded-xl">
                            <i data-lucide="<?= $isEdit ? 'save' : 'user-plus' ?>" class="w-4 h-4"></i>
                            <?= $isEdit ? 'Simpan Perubahan' : 'Buat Akun' ?>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
