<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

$s = content()->getSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    try {
        $img = handle_upload('muassis_file', 'images', $s['muassis_image'] ?? null);
        if (!empty($_POST['muassis_image_url'])) {
            $img = trim($_POST['muassis_image_url']);
        }
        content()->saveSettings([
            'muassis_name' => trim($_POST['muassis_name'] ?? ''),
            'muassis_title' => trim($_POST['muassis_title'] ?? ''),
            'muassis_image' => $img ?? ($s['muassis_image'] ?? ''),
            'muassis_bio' => trim($_POST['muassis_bio'] ?? ''),
            'muassis_sanad' => trim($_POST['muassis_sanad'] ?? ''),
            'muassis_vision' => trim($_POST['muassis_vision'] ?? ''),
            'muassis_quote' => trim($_POST['muassis_quote'] ?? ''),
        ]);
        flash('success', 'Data Muassis berhasil disimpan.');
        redirect('settings-muassis.php');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }
}

$s = content()->getSettings();
admin_header('Jejak Langkah Muassis');
?>
<div class="card card-admin">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <?php csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Muassis</label>
                    <input type="text" name="muassis_name" class="form-control" value="<?= e($s['muassis_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan / Label</label>
                    <input type="text" name="muassis_title" class="form-control" value="<?= e($s['muassis_title'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL Foto (hitam-putih disarankan)</label>
                    <input type="url" name="muassis_image_url" class="form-control" value="<?= preg_match('#^https?://#', $s['muassis_image'] ?? '') ? e($s['muassis_image']) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Upload Foto</label>
                    <input type="file" name="muassis_file" class="form-control" accept="image/*">
                    <?php if (!empty($s['muassis_image'])): ?>
                    <img src="<?= e(asset_url($s['muassis_image'])) ?>" class="preview-thumb mt-2">
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <label class="form-label">Biografi Singkat</label>
                    <textarea name="muassis_bio" class="form-control" rows="4"><?= e($s['muassis_bio'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Sanad Keilmuan</label>
                    <textarea name="muassis_sanad" class="form-control" rows="2"><?= e($s['muassis_sanad'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Cita-cita Awal</label>
                    <textarea name="muassis_vision" class="form-control" rows="2"><?= e($s['muassis_vision'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Wasiat / Kutipan Hikmah (Blockquote)</label>
                    <textarea name="muassis_quote" class="form-control" rows="4"><?= e($s['muassis_quote'] ?? '') ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-3">Simpan</button>
        </form>
    </div>
</div>
<?php admin_footer(); ?>
