<?php
$title = 'Pengaturan Akun Ndalem';
$adminPage = 'ndalem';
$mobileTitle = 'Akun Ndalem';
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/admin_mobile_header.php'; ?>

        <main class="p-4 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 animate-fade-up">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Pengaturan Login Ndalem</h1>
                    <p class="text-gray-400 text-sm mt-0.5">Kelola akun asisten pengasuh untuk dashboard ndalem</p>
                </div>
                <a href="<?= base_url('/admin/pengaturan/ndalem/baru') ?>" class="btn-primary mt-4 sm:mt-0 py-2.5 px-5 text-sm rounded-xl">
                    <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Akun
                </a>
            </div>

            <?php if (!empty($success)): ?>
            <div class="card bg-emerald-50 border-emerald-200 text-emerald-800 px-4 py-3 mb-4 text-sm flex items-center gap-2 animate-fade-up">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <?= e($success) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="card bg-red-50 border-red-200 text-red-700 px-4 py-3 mb-4 text-sm flex items-center gap-2 animate-fade-up">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <?= e($error) ?>
            </div>
            <?php endif; ?>

            <!-- Info login URL -->
            <div class="card p-5 mb-6 bg-gradient-to-r from-pesantren-50 to-white animate-fade-up animate-delay-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-pesantren-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="link" class="w-6 h-6 text-pesantren-700"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm">URL Login Ndalem</p>
                        <p class="text-pesantren-700 font-mono text-sm mt-0.5 break-all"><?= e(base_url('/ndalem/login')) ?></p>
                        <p class="text-gray-400 text-xs mt-1">Bagikan URL ini kepada asisten pengasuh untuk masuk ke dashboard ndalem</p>
                    </div>
                    <button type="button" onclick="copyLoginUrl()" class="px-4 py-2 bg-white border border-pesantren-200 text-pesantren-700 rounded-xl text-sm font-medium hover:bg-pesantren-50 transition flex items-center gap-2 flex-shrink-0">
                        <i data-lucide="copy" class="w-4 h-4"></i> Salin
                    </button>
                </div>
            </div>

            <!-- Users table -->
            <div class="card overflow-hidden animate-fade-up animate-delay-2">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-pesantren-600"></i>
                        <h2 class="font-semibold text-gray-800">Daftar Akun Ndalem</h2>
                        <span class="px-2 py-0.5 bg-pesantren-100 text-pesantren-700 rounded-full text-xs font-bold"><?= count($users) ?></span>
                    </div>
                </div>

                <?php if (empty($users)): ?>
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="user-x" class="w-8 h-8 text-gray-300"></i>
                    </div>
                    <p class="text-gray-500">Belum ada akun ndalem</p>
                    <a href="<?= base_url('/admin/pengaturan/ndalem/baru') ?>" class="inline-block mt-3 text-pesantren-600 text-sm font-medium">+ Tambah akun pertama</a>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Asisten</th>
                                <th>Username</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar"><?= user_initials($u['name']) ?></div>
                                        <span class="font-semibold text-gray-800"><?= e($u['name']) ?></span>
                                    </div>
                                </td>
                                <td><code class="px-2 py-1 bg-gray-100 rounded-lg text-sm text-gray-700"><?= e($u['username']) ?></code></td>
                                <td class="text-gray-500 text-sm"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <div class="flex gap-1.5 flex-wrap">
                                        <a href="<?= base_url('/admin/pengaturan/ndalem/edit/' . $u['id']) ?>"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-pesantren-50 text-pesantren-700 rounded-lg text-xs font-medium hover:bg-pesantren-100 transition">
                                            <i data-lucide="pencil" class="w-3 h-3"></i> Edit
                                        </a>
                                        <button type="button" onclick="openPasswordModal(<?= (int) $u['id'] ?>, '<?= e(addslashes($u['name'])) ?>')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-medium hover:bg-amber-100 transition">
                                            <i data-lucide="key" class="w-3 h-3"></i> Password
                                        </button>
                                        <?php if (count($users) > 1): ?>
                                        <form method="POST" action="<?= base_url('/admin/pengaturan/ndalem/hapus/' . $u['id']) ?>"
                                              onsubmit="return confirm('Hapus akun <?= e(addslashes($u['name'])) ?>?')">
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition">
                                                <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="passwordModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closePasswordModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-md p-6 relative animate-fade-up">
            <button type="button" onclick="closePasswordModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i data-lucide="key" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Reset Password</h3>
                    <p class="text-sm text-gray-500" id="passwordModalName"></p>
                </div>
            </div>
            <form method="POST" id="passwordForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                    <div class="input-wrap">
                        <i data-lucide="lock" class="input-icon w-4 h-4"></i>
                        <input type="password" name="password" required minlength="6" class="input-field" placeholder="Min. 6 karakter">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <i data-lucide="lock" class="input-icon w-4 h-4"></i>
                        <input type="password" name="password_confirm" required minlength="6" class="input-field" placeholder="Ulangi password">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closePasswordModal()" class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="btn-primary flex-1 py-2.5 text-sm rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const loginUrl = '<?= e(base_url('/ndalem/login')) ?>';

function copyLoginUrl() {
    navigator.clipboard.writeText(loginUrl).then(() => alert('URL login ndalem disalin!'));
}

function openPasswordModal(id, name) {
    document.getElementById('passwordModalName').textContent = name;
    document.getElementById('passwordForm').action = '<?= base_url('/admin/pengaturan/ndalem/password/') ?>' + id;
    document.getElementById('passwordModal').classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
}

if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
