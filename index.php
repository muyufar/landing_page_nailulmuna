<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);

if (!file_exists(BASE_PATH . '/config/database.php')) {
    die('Konfigurasi belum ada. Jalankan <a href="install.php">install.php</a> terlebih dahulu.');
}

require_once BASE_PATH . '/includes/bootstrap.php';

try {
    $s = content()->getSettings();
    $nav = content()->getNavItems();
    $programs = content()->getPrograms();
    $stats = content()->getStats();
    $testimonials = content()->getTestimonials();
    $galleryPrestasi = content()->getGallery('prestasi');
    $galleryDaily = content()->getGallery('daily');
    $galleryFasilitas = content()->getGallery('fasilitas');
    $articles = content()->getArticles(3);
    $footerLinks = content()->getFooterLinks();
    $appButtons = content()->getLandingAppButtons();
} catch (Throwable $e) {
    die('Database belum terpasang. <a href="install.php">Klik di sini untuk instalasi</a>.');
}

$siteName = $s['site_name'] ?? 'A.P.I Nailul Muna';
$siteLogo = asset_url($s['site_logo'] ?? '', 'assets/images/logo-nme.png');
$siteTagline = $s['site_tagline'] ?? '';
$heroBg = asset_url($s['hero_bg_image'] ?? '', 'https://images.unsplash.com/photo-1564760055775-d63b17a55c44?auto=format&fit=crop&w=1920&q=80');
$muassisImg = asset_url($s['muassis_image'] ?? '', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=800&q=80');

$logoSize = max(32, min(120, (int) ($s['navbar_logo_size'] ?? 58)));
$brandFont = max(0.9, min(2.5, (float) ($s['navbar_brand_font_size'] ?? 1.35)));
$heroFont = max(1.5, min(4, (float) ($s['hero_tagline_font_size'] ?? 2.75)));
$sectionFont = max(1.25, min(3, (float) ($s['section_title_font_size'] ?? 2)));
$navFont = max(0.75, min(1.25, (float) ($s['nav_link_font_size'] ?? 0.95)));
$galleryThumbH = max(80, min(250, (int) ($s['gallery_thumb_height'] ?? 130)));
$galleryFasH = max(120, min(350, (int) ($s['gallery_fasilitas_height'] ?? 200)));
$blogImgH = max(120, min(400, (int) ($s['blog_image_height'] ?? 200)));
$muassisMaxW = max(60, min(100, (int) ($s['muassis_photo_max_width'] ?? 100)));

function statCounterValue(string $text): array
{
    if (preg_match('/^(\d+)\+?$/', $text, $m)) {
        return [(int) $m[1], strpos($text, '+') !== false ? '+' : ''];
    }
    return [0, $text];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($siteName) ?> — <?= e($s['hero_tagline'] ?? '') ?>">
    <title><?= e($siteName) ?> | Pondok Pesantren Salafiyah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --navbar-logo-size: <?= $logoSize ?>px;
            --navbar-brand-font: <?= $brandFont ?>rem;
            --hero-tagline-font: <?= $heroFont ?>rem;
            --section-title-font: <?= $sectionFont ?>rem;
            --nav-link-font: <?= $navFont ?>rem;
            --gallery-thumb-height: <?= $galleryThumbH ?>px;
            --gallery-fasilitas-height: <?= $galleryFasH ?>px;
            --blog-image-height: <?= $blogImgH ?>px;
            --muassis-photo-max-width: <?= $muassisMaxW ?>%;
        }
    </style>
</head>
<body>

<!-- ========== NAVBAR ========== -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-landing fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#beranda">
            <img src="<?= e($siteLogo) ?>" alt="Logo <?= e($siteName) ?>" class="navbar-logo" width="<?= $logoSize ?>" height="<?= $logoSize ?>">
            <span class="navbar-brand-text"><?= e($siteName) ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto navbar-nav-center">
                <?php foreach ($nav as $item): ?>
                <li class="nav-item">
                    <a class="nav-link nav-link-landing" href="<?= e($item['url_hash']) ?>"><?= e($item['label']) ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-center">
                <a href="<?= e($s['portal_url'] ?? '#') ?>" class="btn-portal" target="_blank" rel="noopener">
                    <i class="bi bi-box-arrow-in-right me-1"></i><?= e($s['portal_button_text'] ?? 'Portal Masuk') ?>
                </a>
                <a href="<?= e($s['psb_url'] ?? '#') ?>" class="btn btn-cta-gold btn-sm">
                    <?= e($s['psb_button_text'] ?? 'Daftar Santri Baru (PSB)') ?>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ========== HERO ========== -->
<section class="hero-section" id="beranda" style="background-image: url('<?= e($heroBg) ?>');">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-10">
                <?php if ($siteTagline): ?>
                <span class="hero-badge"><i class="bi bi-moon-stars me-1"></i><?= e($siteTagline) ?></span>
                <?php endif; ?>
                <h1 class="hero-tagline"><?= e($s['hero_tagline'] ?? '') ?></h1>
                <a href="<?= e($s['hero_cta_link'] ?? '#profil') ?>" class="btn btn-hero-cta">
                    <?= e($s['hero_cta_text'] ?? 'Mulai Penjelajahan') ?>
                    <i class="bi bi-arrow-down-circle ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ========== MUASSIS ========== -->
<section class="section-padding section-bg-white" id="profil">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle"><?= e($s['section_muassis_subtitle'] ?? 'The Roots & Legacy') ?></p>
            <h2 class="section-title"><?= e($s['section_muassis_title'] ?? 'Jejak Langkah Muassis') ?></h2>
        </div>
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="muassis-photo-wrap">
                    <img src="<?= e($muassisImg) ?>" alt="<?= e($s['muassis_name'] ?? 'Muassis') ?>" class="muassis-photo img-fluid">
                </div>
            </div>
            <div class="col-lg-7">
                <span class="badge badge-muassis mb-2"><?= e($s['muassis_title'] ?? 'Muassis & Pengasuh') ?></span>
                <h3 class="muassis-name mb-3"><?= e($s['muassis_name'] ?? '') ?></h3>
                <p class="text-muted lh-lg"><?= nl2br(e($s['muassis_bio'] ?? '')) ?></p>
                <div class="muassis-info">
                <?php if (!empty($s['muassis_sanad'])): ?>
                <p class="mb-2"><strong><i class="bi bi-mortarboard me-2"></i>Sanad Keilmuan:</strong> <?= e($s['muassis_sanad']) ?></p>
                <?php endif; ?>
                <?php if (!empty($s['muassis_vision'])): ?>
                <p class="mb-0"><strong><i class="bi bi-stars me-2"></i>Cita-cita Awal:</strong> <?= e($s['muassis_vision']) ?></p>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if (!empty($s['muassis_quote'])): ?>
        <blockquote class="muassis-quote card-wasiat">
            <p>&ldquo;<?= e($s['muassis_quote']) ?>&rdquo;</p>
            <cite>— <?= e($s['muassis_name'] ?? 'Muassis') ?></cite>
        </blockquote>
        <?php endif; ?>
    </div>
</section>

<!-- ========== WAJAH SANTRI & PRESTASI ========== -->
<section class="section-padding section-bg-mint" id="alumni">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle"><?= e($s['section_stories_subtitle'] ?? 'The Success Stories') ?></p>
            <h2 class="section-title"><?= e($s['section_stories_title'] ?? 'Wajah Santri & Prestasi') ?></h2>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div id="testimonialCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner h-100">
                        <?php foreach ($testimonials as $i => $t): ?>
                        <div class="carousel-item h-100 <?= $i === 0 ? 'active' : '' ?>">
                            <div class="testimonial-card h-100">
                                <div class="quote-icon mb-3">&ldquo;</div>
                                <p class="mb-4"><?= e($t['quote_text']) ?></p>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($t['avatar'])): ?>
                                    <img src="<?= e(asset_url($t['avatar'])) ?>" alt="" class="testimonial-avatar">
                                    <?php else: ?>
                                    <div class="testimonial-avatar d-flex align-items-center justify-content-center bg-success text-white fw-bold">
                                        <?= e(mb_substr($t['name'], 0, 1)) ?>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong class="d-block testimonial-name"><?= e($t['name']) ?></strong>
                                        <small class="text-muted"><?= e($t['role_label']) ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="gallery-box card-wasiat">
                        <p class="gallery-label"><i class="bi bi-trophy-fill"></i> Galeri Prestasi</p>
                        <div class="gallery-mini">
                            <?php foreach ($galleryPrestasi as $g): ?>
                            <figure class="gallery-mini-item card-galeri-putih">
                                <img src="<?= e(asset_url($g['image_path'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
                                <figcaption><?= e($g['title']) ?></figcaption>
                            </figure>
                            <?php endforeach; ?>
                        </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="gallery-box card-wasiat">
                        <p class="gallery-label"><i class="bi bi-camera-fill"></i> Daily Life Santri</p>
                        <div class="gallery-mini">
                            <?php foreach ($galleryDaily as $g): ?>
                            <figure class="gallery-mini-item card-galeri-putih">
                                <img src="<?= e(asset_url($g['image_path'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
                                <figcaption><?= e($g['title']) ?></figcaption>
                            </figure>
                            <?php endforeach; ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== PROGRAM UNGGULAN ========== -->
<section class="section-padding section-bg-cream" id="program">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle"><?= e($s['section_programs_subtitle'] ?? '') ?></p>
            <h2 class="section-title"><?= e($s['section_programs_title'] ?? 'Program Unggulan') ?></h2>
        </div>
        <div class="row g-4">
            <?php foreach ($programs as $p): ?>
            <div class="col-md-4">
                <div class="program-card card-wasiat">
                    <div class="program-icon">
                        <i class="bi <?= e($p['icon_class']) ?>"></i>
                    </div>
                    <h5><?= e($p['title']) ?></h5>
                    <p class="text-muted mb-0 small"><?= e($p['description']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========== FASILITAS (anchor dari menu) ========== -->
<section class="section-padding section-bg-white" id="fasilitas">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle"><?= e($s['section_fasilitas_subtitle'] ?? 'Fasilitas Pesantren') ?></p>
            <h2 class="section-title mb-3"><?= e($s['section_fasilitas_title'] ?? 'Sarana Pendukung Pendidikan') ?></h2>
            <?php if (!empty($s['fasilitas_text'])): ?>
            <p class="text-muted col-lg-8 mx-auto"><?= e($s['fasilitas_text']) ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($galleryFasilitas)): ?>
        <p class="gallery-label text-center"><i class="bi bi-building"></i>Galeri Fasilitas</p>
        <div class="gallery-fasilitas">
            <?php foreach ($galleryFasilitas as $g): ?>
            <figure class="gallery-fasilitas-item card-galeri-testimoni">
                <img src="<?= e(asset_url($g['image_path'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
                <figcaption><?= e($g['title']) ?></figcaption>
            </figure>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========== ANGKA BICARA ========== -->
<section class="stats-section" id="statistik">
    <div class="container">
        <div class="section-header mb-4">
            <p class="section-subtitle">Statistik</p>
            <h2 class="section-title text-white"><?= e($s['section_stats_title'] ?? 'Angka Bicara') ?></h2>
        </div>
        <div class="row text-center g-4">
            <?php foreach ($stats as $st): ?>
            <?php [$num, $suffix] = statCounterValue($st['value_text']); ?>
            <div class="col-6 col-lg-3 stat-item">
                <?php if ($num > 0): ?>
                <h3 data-counter="<?= $num ?>" data-suffix="<?= e($suffix) ?>">0<?= e($suffix) ?></h3>
                <?php else: ?>
                <h3><?= e($st['value_text']) ?></h3>
                <?php endif; ?>
                <p><?= e($st['label']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========== BLOG & TAUSIYAH ========== -->
<section class="section-padding section-bg-mint" id="berita">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle"><?= e($s['section_blog_subtitle'] ?? '') ?></p>
            <h2 class="section-title"><?= e($s['section_blog_title'] ?? 'Blog Tausiyah & Berita') ?></h2>
        </div>
        <div class="row g-4">
            <?php foreach ($articles as $art): ?>
            <div class="col-md-4">
                <div class="card blog-card card-wasiat">
                    <img src="<?= e(asset_url($art['image_path'] ?? '', 'https://via.placeholder.com/800x400?text=Artikel')) ?>" class="card-img-top" alt="<?= e($art['title']) ?>">
                    <div class="card-body">
                        <span class="badge blog-badge <?= $art['category'] === 'tausiyah' ? 'badge-tausiyah' : 'badge-berita' ?> mb-2">
                            <?= $art['category'] === 'tausiyah' ? 'Tausiyah Masyayikh' : 'Berita Kegiatan' ?>
                        </span>
                        <h5 class="card-title fw-bold"><?= e($art['title']) ?></h5>
                        <p class="card-text text-muted small"><?= e($art['excerpt']) ?></p>
                        <?php if (!empty($art['published_at'])): ?>
                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($art['published_at'])) ?></small>
                        <?php endif; ?>
                        <a href="<?= e($art['link_url'] ?: '#') ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($appButtons)): ?>
<!-- ========== LAYANAN DIGITAL ========== -->
<section class="section-padding section-bg-cream" id="layanan-digital">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle"><?= e($s['section_apps_subtitle'] ?? 'Akses cepat ke aplikasi pesantren') ?></p>
            <h2 class="section-title"><?= e($s['section_apps_title'] ?? 'Layanan Digital') ?></h2>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($appButtons as $app): ?>
            <div class="col-md-6 col-lg-5">
                <div class="app-card h-100">
                    <div class="app-card-icon"><i class="bi <?= e($app['icon']) ?>"></i></div>
                    <h3 class="app-card-title"><?= e($app['label']) ?></h3>
                    <p class="app-card-desc"><?= e($app['desc']) ?></p>
                    <a href="<?= e(public_url($app['url'])) ?>" class="btn btn-cta-gold app-card-btn">
                        Buka <?= e($app['label']) ?>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== FOOTER ========== -->
<footer class="footer-landing">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <h5><i class="bi bi-geo-alt me-2"></i>Kontak & Alamat</h5>
                <p class="mb-2"><i class="bi bi-pin-map me-2"></i><?= e($s['footer_address'] ?? '') ?></p>
                <p class="mb-2"><i class="bi bi-telephone me-2"></i><a href="tel:<?= e(preg_replace('/\s+/', '', $s['footer_phone'] ?? '')) ?>"><?= e($s['footer_phone'] ?? '') ?></a></p>
                <p class="mb-3"><i class="bi bi-envelope me-2"></i><a href="mailto:<?= e($s['footer_email'] ?? '') ?>"><?= e($s['footer_email'] ?? '') ?></a></p>
                <div class="footer-social">
                    <?php if (!empty($s['footer_whatsapp'])): ?>
                    <a href="<?= e($s['footer_whatsapp']) ?>" target="_blank" rel="noopener" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($s['footer_facebook'])): ?>
                    <a href="<?= e($s['footer_facebook']) ?>" target="_blank" rel="noopener" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($s['footer_instagram'])): ?>
                    <a href="<?= e($s['footer_instagram']) ?>" target="_blank" rel="noopener" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($s['footer_youtube'])): ?>
                    <a href="<?= e($s['footer_youtube']) ?>" target="_blank" rel="noopener" title="YouTube"><i class="bi bi-youtube"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <h5><i class="bi bi-grid me-2"></i>Akses Sistem</h5>
                <ul class="list-unstyled">
                    <?php foreach ($footerLinks as $link): ?>
                    <li class="mb-2">
                        <a href="<?= e($link['url']) ?>" target="_blank" rel="noopener">
                            <i class="bi bi-chevron-right me-1"></i><?= e($link['label']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <?php foreach ($appButtons as $app): ?>
                    <li class="mb-2">
                        <a href="<?= e(public_url($app['url'])) ?>" rel="noopener">
                            <i class="bi bi-chevron-right me-1"></i><?= e($app['label']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li class="mb-2">
                        <a href="<?= e($s['portal_url'] ?? '#') ?>" target="_blank" rel="noopener">
                            <i class="bi bi-chevron-right me-1"></i><?= e($s['portal_button_text'] ?? 'Portal Masuk') ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <?= e($s['footer_copyright'] ?? '') ?>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
