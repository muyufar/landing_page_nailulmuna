<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

admin_header('Dashboard');

$counts = [
    'Artikel' => count(content()->allArticles()),
    'Program' => count(content()->allPrograms()),
    'Testimoni' => count(content()->allTestimonials()),
    'Galeri Prestasi' => count(content()->allGallery('prestasi')),
    'Galeri Daily' => count(content()->allGallery('daily')),
    'Galeri Fasilitas' => count(content()->allGallery('fasilitas')),
];
?>
<div class="row g-3 mb-4">
    <?php foreach ($counts as $label => $count): ?>
    <div class="col-md-4 col-lg">
        <div class="card card-admin">
            <div class="card-body">
                <h6 class="text-muted small mb-1"><?= e($label) ?></h6>
                <h3 class="mb-0 fw-bold" style="color:#0d6e4f;"><?= $count ?></h3>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php
$panelBase = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$panelPath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/landing page/admin'), '/admin') . '/panel.php';
$panelUrl = $panelBase . $panelPath;
?>
<div class="card card-admin mb-4 border-success">
    <div class="card-body">
        <h6 class="text-success mb-2"><i class="bi bi-link-45deg me-1"></i>Link Akses Back Office</h6>
        <p class="text-muted small mb-2">Tidak ditampilkan di landing page. Bagikan hanya kepada pengurus:</p>
        <code class="d-block p-2 bg-light rounded user-select-all"><?= e($panelUrl) ?></code>
        <a href="<?= e($panelUrl) ?>" target="_blank" class="btn btn-outline-success btn-sm mt-2">Buka panel.php</a>
    </div>
</div>
<div class="card card-admin">
    <div class="card-body">
        <h5 class="card-title">Selamat datang di Back Office</h5>
        <p class="text-muted mb-3">Kelola seluruh konten landing page tanpa mengubah kode. Atur ukuran logo, teks, dan foto di <strong>Tampilan & Ukuran</strong>.</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="settings-appearance.php" class="btn btn-success btn-sm">Tampilan & Ukuran</a>            <a href="settings-hero.php" class="btn btn-success btn-sm">Edit Hero</a>
            <a href="settings-muassis.php" class="btn btn-success btn-sm">Edit Muassis</a>
            <a href="articles.php" class="btn btn-outline-success btn-sm">Kelola Artikel</a>
            <a href="../index.php" class="btn btn-outline-secondary btn-sm" target="_blank">Preview Website</a>
        </div>
    </div>
</div>
<?php admin_footer(); ?>
