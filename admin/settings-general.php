<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    try {
        $s = content()->getSettings();
        $logo = $s['site_logo'] ?? 'assets/images/logo-nme.png';
        if (has_file_upload('site_logo_file')) {
            $logo = handle_upload('site_logo_file', 'images', $logo);
        } elseif (!empty($_POST['site_logo_path'])) {
            $logo = trim($_POST['site_logo_path']);
        }
        content()->saveSettings([
            'site_name' => trim($_POST['site_name'] ?? ''),
            'site_logo' => $logo,
            'site_tagline' => trim($_POST['site_tagline'] ?? ''),
            'portal_url' => trim($_POST['portal_url'] ?? ''),
            'portal_button_text' => trim($_POST['portal_button_text'] ?? ''),
            'psb_url' => trim($_POST['psb_url'] ?? ''),
            'psb_button_text' => trim($_POST['psb_button_text'] ?? ''),
            'fasilitas_text' => trim($_POST['fasilitas_text'] ?? ''),
        ]);
        flash('success', 'Pengaturan umum berhasil disimpan.');
        redirect('settings-general.php');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        flash('error', 'Token keamanan tidak valid.');
    }
}

$s = content()->getSettings();
admin_header('Umum & Tombol Navbar');
?>
<div class="card card-admin">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <?php csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Logo Navbar</label>
                    <input type="text" name="site_logo_path" class="form-control mb-2" placeholder="assets/images/logo-nme.png" value="<?= e($s['site_logo'] ?? 'assets/images/logo-nme.png') ?>">
                    <input type="file" name="site_logo_file" class="form-control" accept="image/*">
                    <?php $logoPreview = asset_url($s['site_logo'] ?? 'assets/images/logo-nme.png'); ?>
                    <img src="<?= e($logoPreview) ?>" alt="Logo" class="preview-thumb mt-2" style="max-height:64px;width:auto;">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Pesantren (teks di samping logo)</label>
                    <input type="text" name="site_name" class="form-control" value="<?= e($s['site_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tagline Kecil (di Hero)</label>
                    <input type="text" name="site_tagline" class="form-control" value="<?= e($s['site_tagline'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL Portal Masuk</label>
                    <input type="url" name="portal_url" class="form-control" value="<?= e($s['portal_url'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teks Tombol Portal</label>
                    <input type="text" name="portal_button_text" class="form-control" value="<?= e($s['portal_button_text'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL Daftar PSB</label>
                    <input type="text" name="psb_url" class="form-control" value="<?= e($s['psb_url'] ?? '#') ?>" placeholder="# atau URL lengkap">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teks Tombol PSB</label>
                    <input type="text" name="psb_button_text" class="form-control" value="<?= e($s['psb_button_text'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Teks Section Fasilitas</label>
                    <textarea name="fasilitas_text" class="form-control" rows="3"><?= e($s['fasilitas_text'] ?? '') ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-3">Simpan</button>
        </form>
    </div>
</div>
<?php admin_footer(); ?>
