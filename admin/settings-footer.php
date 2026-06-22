<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    content()->saveSettings([
        'footer_address' => trim($_POST['footer_address'] ?? ''),
        'footer_phone' => trim($_POST['footer_phone'] ?? ''),
        'footer_email' => trim($_POST['footer_email'] ?? ''),
        'footer_whatsapp' => trim($_POST['footer_whatsapp'] ?? ''),
        'footer_facebook' => trim($_POST['footer_facebook'] ?? ''),
        'footer_instagram' => trim($_POST['footer_instagram'] ?? ''),
        'footer_youtube' => trim($_POST['footer_youtube'] ?? ''),
        'footer_copyright' => trim($_POST['footer_copyright'] ?? ''),
    ]);
    flash('success', 'Footer berhasil disimpan.');
    redirect('settings-footer.php');
}

$s = content()->getSettings();
admin_header('Footer & Kontak');
?>
<div class="card card-admin">
    <div class="card-body">
        <form method="post">
            <?php csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="footer_address" class="form-control" rows="2"><?= e($s['footer_address'] ?? '') ?></textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Telepon</label><input type="text" name="footer_phone" class="form-control" value="<?= e($s['footer_phone'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="footer_email" class="form-control" value="<?= e($s['footer_email'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">WhatsApp URL</label><input type="url" name="footer_whatsapp" class="form-control" value="<?= e($s['footer_whatsapp'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Facebook</label><input type="url" name="footer_facebook" class="form-control" value="<?= e($s['footer_facebook'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Instagram</label><input type="url" name="footer_instagram" class="form-control" value="<?= e($s['footer_instagram'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">YouTube</label><input type="url" name="footer_youtube" class="form-control" value="<?= e($s['footer_youtube'] ?? '') ?>"></div>
                <div class="col-12"><label class="form-label">Teks Copyright</label><input type="text" name="footer_copyright" class="form-control" value="<?= e($s['footer_copyright'] ?? '') ?>"></div>
            </div>
            <button type="submit" class="btn btn-success mt-3">Simpan</button>
        </form>
    </div>
</div>
<?php admin_footer(); ?>
