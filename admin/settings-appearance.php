<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

$defaults = [
    'navbar_logo_size' => '58',
    'navbar_brand_font_size' => '1.35',
    'hero_tagline_font_size' => '2.75',
    'section_title_font_size' => '2',
    'nav_link_font_size' => '0.95',
    'gallery_thumb_height' => '130',
    'gallery_fasilitas_height' => '200',
    'blog_image_height' => '200',
    'muassis_photo_max_width' => '100',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $data = [];
    foreach (array_keys($defaults) as $key) {
        $val = trim($_POST[$key] ?? $defaults[$key]);
        $data[$key] = $val;
    }
    content()->saveSettings($data);
    flash('success', 'Pengaturan tampilan berhasil disimpan.');
    redirect('settings-appearance.php');
}

$s = array_merge($defaults, content()->getSettings());
$panelUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
    . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/landing page/admin'), '/admin')
    . '/panel.php';

admin_header('Tampilan & Ukuran');
?>
<div class="alert alert-info small mb-4">
    <strong>Link akses Back Office</strong> (tidak muncul di website publik):<br>
    <a href="<?= e($panelUrl) ?>" target="_blank" class="fw-semibold"><?= e($panelUrl) ?></a>
    <span class="text-muted d-block mt-1">Simpan link ini untuk pengurus. File: <code>panel.php</code> → mengarah ke halaman login admin.</span>
</div>

<div class="card card-admin">
    <div class="card-body">
        <form method="post">
            <?php csrf_field(); ?>
            <h6 class="text-success border-bottom pb-2 mb-3">Navbar (Logo & Nama Pesantren)</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Ukuran logo (px)</label>
                    <input type="number" name="navbar_logo_size" class="form-control" min="32" max="120" value="<?= e($s['navbar_logo_size']) ?>">
                    <small class="text-muted">Default: 58px — disarankan 52–72</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ukuran teks nama pesantren (rem)</label>
                    <input type="number" name="navbar_brand_font_size" class="form-control" min="0.9" max="2.5" step="0.05" value="<?= e($s['navbar_brand_font_size']) ?>">
                    <small class="text-muted">Default: 1.35rem</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ukuran menu navigasi (rem)</label>
                    <input type="number" name="nav_link_font_size" class="form-control" min="0.75" max="1.25" step="0.05" value="<?= e($s['nav_link_font_size']) ?>">
                </div>
            </div>

            <h6 class="text-success border-bottom pb-2 mb-3">Hero & Judul Section</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Ukuran tagline hero (rem)</label>
                    <input type="number" name="hero_tagline_font_size" class="form-control" min="1.5" max="4" step="0.05" value="<?= e($s['hero_tagline_font_size']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ukuran judul section (rem)</label>
                    <input type="number" name="section_title_font_size" class="form-control" min="1.25" max="3" step="0.05" value="<?= e($s['section_title_font_size']) ?>">
                </div>
            </div>

            <h6 class="text-success border-bottom pb-2 mb-3">Galeri & Foto Konten</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tinggi thumbnail galeri prestasi/daily (px)</label>
                    <input type="number" name="gallery_thumb_height" class="form-control" min="80" max="250" value="<?= e($s['gallery_thumb_height']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tinggi foto galeri fasilitas (px)</label>
                    <input type="number" name="gallery_fasilitas_height" class="form-control" min="120" max="350" value="<?= e($s['gallery_fasilitas_height']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tinggi gambar kartu berita/tausiyah (px)</label>
                    <input type="number" name="blog_image_height" class="form-control" min="120" max="400" value="<?= e($s['blog_image_height']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lebar maks. foto Muassis (%)</label>
                    <input type="number" name="muassis_photo_max_width" class="form-control" min="60" max="100" value="<?= e($s['muassis_photo_max_width']) ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-success">Simpan Tampilan</button>
            <a href="../index.php" target="_blank" class="btn btn-outline-secondary ms-2">Preview Landing Page</a>
        </form>
    </div>
</div>
<?php admin_footer(); ?>
