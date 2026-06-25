<?php
$title = 'Jadwal Terima Tamu';
$adminPage = 'jadwal';
$mobileTitle = 'Jadwal Tamu';
$area = $area ?? 'ndalem';
$areaLabel = $area === 'ndalem' ? 'Ndalem (Sowan Pengasuh)' : 'Pesantren (Kantor)';
$hariOptions = jadwal_terima_hari_options();
?>

<div class="flex min-h-screen bg-gray-50">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <div class="flex-1 md:ml-64">
        <?php require __DIR__ . '/../partials/admin_mobile_header.php'; ?>

        <main class="p-4 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 animate-fade-up">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Jadwal Terima Tamu</h1>
                    <p class="text-gray-400 text-sm mt-0.5">Informasi rekap — ditampilkan ke tamu saat mendaftar</p>
                </div>
            </div>

            <?php if (!empty($success)): ?>
            <div class="card bg-emerald-50 border-emerald-200 text-emerald-800 px-4 py-3 mb-4 text-sm flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i><?= e($success) ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="card bg-red-50 border-red-200 text-red-700 px-4 py-3 mb-4 text-sm"><?= e($error) ?></div>
            <?php endif; ?>

            <!-- Tab area -->
            <div class="inline-flex bg-white rounded-xl border p-1 mb-6">
                <a href="?area=ndalem" class="px-5 py-2 rounded-lg text-sm font-medium transition <?= $area === 'ndalem' ? 'bg-pesantren-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                    <i data-lucide="home" class="w-4 h-4 inline -mt-0.5"></i> Ndalem
                </a>
                <a href="?area=pesantren" class="px-5 py-2 rounded-lg text-sm font-medium transition <?= $area === 'pesantren' ? 'bg-pesantren-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                    <i data-lucide="building-2" class="w-4 h-4 inline -mt-0.5"></i> Pesantren
                </a>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <!-- Form tambah -->
                <div class="card p-6 animate-fade-up">
                    <h2 class="font-semibold text-gray-800 mb-1">Tambah Jadwal — <?= e($areaLabel) ?></h2>
                    <p class="text-xs text-gray-400 mb-4">Hanya untuk catatan & informasi tamu, tidak memblokir pendaftaran</p>
                    <form method="POST" action="<?= base_url('/admin/pengaturan/jadwal/tambah') ?>" class="space-y-4">
                        <input type="hidden" name="area" value="<?= e($area) ?>">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Hari</label>
                            <select name="hari" required class="input-field no-icon w-full">
                                <?php foreach ($hariOptions as $i => $label): ?>
                                <option value="<?= $i ?>"><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jam Mulai</label>
                                <input type="time" name="jam_mulai" required class="input-field no-icon w-full">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jam Selesai</label>
                                <input type="time" name="jam_selesai" required class="input-field no-icon w-full">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan <span class="text-gray-400">(opsional)</span></label>
                            <input type="text" name="keterangan" placeholder="Contoh: Sowan pagi"
                                   class="input-field no-icon w-full">
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded text-pesantren-600">
                            <span class="text-sm">Tampilkan ke tamu</span>
                        </label>
                        <button type="submit" class="btn-primary w-full py-2.5 text-sm rounded-xl">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Jadwal
                        </button>
                    </form>
                </div>

                <!-- Preview seperti di form tamu -->
                <div class="card p-6 bg-gradient-to-br from-pesantren-50/80 to-white animate-fade-up animate-delay-1">
                    <h2 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i data-lucide="eye" class="w-4 h-4 text-pesantren-600"></i> Preview di Form Tamu
                    </h2>
                    <div class="rounded-xl border border-pesantren-100 bg-white p-4">
                        <p class="text-xs font-semibold text-pesantren-700 uppercase tracking-wide mb-3">Jadwal Terima Tamu — <?= e($areaLabel) ?></p>
                        <?= render_jadwal_terima_html($grouped, $areaLabel) ?>
                        <p class="text-[10px] text-gray-400 mt-3 italic">*Informasi rekap. Pendaftaran tetap dapat dilakukan di luar jadwal.</p>
                    </div>
                </div>
            </div>

            <!-- Daftar jadwal -->
            <div class="card overflow-hidden mt-6 animate-fade-up animate-delay-2">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800">Daftar Jadwal — <?= e($areaLabel) ?></h2>
                    <span class="text-xs text-gray-400"><?= count($items) ?> slot</span>
                </div>
                <?php if (empty($items)): ?>
                <p class="text-center text-gray-400 py-12">Belum ada jadwal. Tambahkan slot waktu di form kiri.</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr class="<?= !$item['is_active'] ? 'opacity-50' : '' ?>">
                                <td class="font-medium"><?= e($jadwalModel->hariLabel((int) $item['hari'])) ?></td>
                                <td class="font-mono text-sm"><?= e($jadwalModel->formatSlot($item)) ?></td>
                                <td class="text-gray-500"><?= e($item['keterangan'] ?? '—') ?></td>
                                <td>
                                    <?php if ($item['is_active']): ?>
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">Aktif</span>
                                    <?php else: ?>
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <button type="button" onclick="editJadwal(<?= htmlspecialchars(json_encode($item), ENT_QUOTES) ?>)"
                                                class="px-2.5 py-1 bg-pesantren-50 text-pesantren-700 rounded-lg text-xs font-medium">Edit</button>
                                        <form method="POST" action="<?= base_url('/admin/pengaturan/jadwal/hapus/' . $item['id']) ?>"
                                              onsubmit="return confirm('Hapus jadwal ini?')">
                                            <button type="submit" class="px-2.5 py-1 bg-red-50 text-red-600 rounded-lg text-xs">Hapus</button>
                                        </form>
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

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-md p-6 relative">
            <h3 class="font-bold text-gray-800 mb-4">Edit Jadwal</h3>
            <form method="POST" id="editForm" class="space-y-4">
                <input type="hidden" name="area" id="edit_area">
                <div>
                    <label class="block text-sm font-medium mb-1">Hari</label>
                    <select name="hari" id="edit_hari" required class="input-field no-icon w-full">
                        <?php foreach ($hariOptions as $i => $label): ?>
                        <option value="<?= $i ?>"><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="edit_mulai" required class="input-field no-icon w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="edit_selesai" required class="input-field no-icon w-full">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Keterangan</label>
                    <input type="text" name="keterangan" id="edit_ket" class="input-field no-icon w-full">
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="edit_active" value="1" class="rounded text-pesantren-600">
                    <span class="text-sm">Tampilkan ke tamu</span>
                </label>
                <div class="flex gap-3">
                    <button type="button" onclick="closeEditModal()" class="flex-1 py-2.5 border rounded-xl text-sm">Batal</button>
                    <button type="submit" class="btn-primary flex-1 py-2.5 text-sm rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editJadwal(item) {
    document.getElementById('editForm').action = '<?= base_url('/admin/pengaturan/jadwal/edit/') ?>' + item.id;
    document.getElementById('edit_area').value = item.area;
    document.getElementById('edit_hari').value = item.hari;
    document.getElementById('edit_mulai').value = item.jam_mulai.substring(0, 5);
    document.getElementById('edit_selesai').value = item.jam_selesai.substring(0, 5);
    document.getElementById('edit_ket').value = item.keterangan || '';
    document.getElementById('edit_active').checked = item.is_active == 1;
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
