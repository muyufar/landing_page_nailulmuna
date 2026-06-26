<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    content()->saveSettings([
        'show_buku_tamu' => isset($_POST['show_buku_tamu']) ? '1' : '0',
        'show_undangan' => isset($_POST['show_undangan']) ? '1' : '0',
        'section_apps_title' => trim($_POST['section_apps_title'] ?? ''),
        'section_apps_subtitle' => trim($_POST['section_apps_subtitle'] ?? ''),
        'buku_tamu_button_text' => trim($_POST['buku_tamu_button_text'] ?? ''),
        'undangan_button_text' => trim($_POST['undangan_button_text'] ?? ''),
        'buku_tamu_desc' => trim($_POST['buku_tamu_desc'] ?? ''),
        'undangan_desc' => trim($_POST['undangan_desc'] ?? ''),
    ]);
    flash('success', 'Pengaturan layanan digital disimpan.');
    redirect('settings-apps.php');
}

$s = content()->getSettings();
$showBukuTamu = ($s['show_buku_tamu'] ?? '0') === '1';
$showUndangan = ($s['show_undangan'] ?? '0') === '1';

admin_header('Layanan Digital');
?>
<div class="card card-admin">
    <div class="card-body">
        <p class="text-muted mb-4">
            Buku Tamu dan Undangan adalah bagian dari website ini. Centang <strong>Tampilkan</strong>
            jika ingin tombolnya muncul di landing page. Default: <strong>tidak ditampilkan</strong>.
        </p>
        <form method="post">
            <?php csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Judul Section</label>
                    <input type="text" name="section_apps_title" class="form-control"
                           value="<?= e($s['section_apps_title'] ?? 'Layanan Digital') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Subjudul Section</label>
                    <input type="text" name="section_apps_subtitle" class="form-control"
                           value="<?= e($s['section_apps_subtitle'] ?? 'Akses cepat ke aplikasi pesantren') ?>">
                </div>

                <div class="col-12"><hr></div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   name="show_buku_tamu" id="show_buku_tamu" value="1" <?= $showBukuTamu ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="show_buku_tamu">Tampilkan Buku Tamu</label>
                        </div>
                        <label class="form-label">Teks Tombol</label>
                        <input type="text" name="buku_tamu_button_text" class="form-control mb-2"
                               value="<?= e($s['buku_tamu_button_text'] ?? 'Buku Tamu Digital') ?>">
                        <label class="form-label">Deskripsi singkat</label>
                        <textarea name="buku_tamu_desc" class="form-control" rows="2"><?= e($s['buku_tamu_desc'] ?? 'Isi buku tamu digital untuk tamu pesantren.') ?></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   name="show_undangan" id="show_undangan" value="1" <?= $showUndangan ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="show_undangan">Tampilkan Undangan</label>
                        </div>
                        <label class="form-label">Teks Tombol</label>
                        <input type="text" name="undangan_button_text" class="form-control mb-2"
                               value="<?= e($s['undangan_button_text'] ?? 'Undangan Digital') ?>">
                        <label class="form-label">Deskripsi singkat</label>
                        <textarea name="undangan_desc" class="form-control" rows="2"><?= e($s['undangan_desc'] ?? 'Undangan digital acara haflah dan kegiatan pesantren.') ?></textarea>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-4">Simpan</button>
        </form>
    </div>
</div>
<?php admin_footer(); ?>
