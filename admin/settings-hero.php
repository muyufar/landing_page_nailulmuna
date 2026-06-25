<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

$s = content()->getSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    try {
        if (has_file_upload('hero_bg_file')) {
            $bg = handle_upload('hero_bg_file', 'images', $s['hero_bg_image'] ?? null);
        } elseif (!empty($_POST['hero_bg_url'])) {
            $bg = trim($_POST['hero_bg_url']);
        } else {
            $bg = $s['hero_bg_image'] ?? '';
        }
        content()->saveSettings([
            'hero_tagline' => trim($_POST['hero_tagline'] ?? ''),
            'hero_bg_image' => $bg ?? ($s['hero_bg_image'] ?? ''),
            'hero_cta_text' => trim($_POST['hero_cta_text'] ?? ''),
            'hero_cta_link' => trim($_POST['hero_cta_link'] ?? ''),
        ]);
        flash('success', 'Hero section berhasil disimpan.');
        redirect('settings-hero.php');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }
}

$s = content()->getSettings();
$bgPreview = asset_url($s['hero_bg_image'] ?? '');
admin_header('Hero Section');
?>
<div class="card card-admin">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <?php csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Tagline Hero (Headline Utama)</label>
                <textarea name="hero_tagline" class="form-control" rows="3" required><?= e($s['hero_tagline'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Background — URL Gambar (Unsplash/dll)</label>
                <input type="url" name="hero_bg_url" class="form-control" placeholder="https://..." value="<?= preg_match('#^https?://#', $s['hero_bg_image'] ?? '') ? e($s['hero_bg_image']) : '' ?>">
                <small class="text-muted">Kosongkan jika ingin pakai upload di bawah.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Atau Upload Gambar Background</label>
                <input type="file" name="hero_bg_file" class="form-control" accept="image/*">
                <?php if ($bgPreview): ?>
                <img src="<?= e($bgPreview) ?>" class="preview-thumb mt-2" alt="Preview">
                <?php endif; ?>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Teks Tombol CTA</label>
                    <input type="text" name="hero_cta_text" class="form-control" value="<?= e($s['hero_cta_text'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Link Tombol CTA</label>
                    <input type="text" name="hero_cta_link" class="form-control" value="<?= e($s['hero_cta_link'] ?? '#profil') ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-3">Simpan</button>
        </form>
    </div>
</div>
<?php admin_footer(); ?>
