<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_login();

$adminName = $_SESSION['admin_name'] ?? 'Admin';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

$menu = [
    ['file' => 'index', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2'],
    ['file' => 'settings-general', 'label' => 'Umum & Navbar', 'icon' => 'bi-gear'],
    ['file' => 'settings-apps', 'label' => 'Layanan Digital', 'icon' => 'bi-phone'],
    ['file' => 'settings-appearance', 'label' => 'Tampilan & Ukuran', 'icon' => 'bi-sliders'],
    ['file' => 'settings-hero', 'label' => 'Hero Section', 'icon' => 'bi-image'],
    ['file' => 'settings-muassis', 'label' => 'Muassis', 'icon' => 'bi-person-badge'],
    ['file' => 'nav', 'label' => 'Menu Navigasi', 'icon' => 'bi-list'],
    ['file' => 'testimonials', 'label' => 'Testimoni Santri', 'icon' => 'bi-chat-quote'],
    ['file' => 'gallery', 'label' => 'Galeri Foto', 'icon' => 'bi-images'],
    ['file' => 'programs', 'label' => 'Program Unggulan', 'icon' => 'bi-journal-bookmark'],
    ['file' => 'stats', 'label' => 'Angka Bicara', 'icon' => 'bi-bar-chart'],
    ['file' => 'articles', 'label' => 'Tausiyah & Berita', 'icon' => 'bi-newspaper'],
    ['file' => 'settings-sections', 'label' => 'Judul Section', 'icon' => 'bi-type'],
    ['file' => 'settings-footer', 'label' => 'Footer & Kontak', 'icon' => 'bi-footer'],
    ['file' => 'footer-links', 'label' => 'Link Akses Sistem', 'icon' => 'bi-link-45deg'],
    ['file' => 'change-password', 'label' => 'Ganti Password', 'icon' => 'bi-shield-lock'],
];

function admin_header(string $title): void
{
    global $menu, $currentPage, $adminName;
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> — Back Office</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/admin.css" rel="stylesheet">
</head>
<body class="admin-body">
<aside class="admin-sidebar" id="sidebar">
    <div class="brand">API <span>Nailul Muna</span><br><small class="text-white-50 fw-normal">Back Office</small></div>
    <nav class="nav flex-column">
        <?php foreach ($menu as $m): ?>
        <a class="nav-link <?= $currentPage === $m['file'] ? 'active' : '' ?>" href="<?= e($m['file']) ?>.php">
            <i class="bi <?= e($m['icon']) ?> me-2"></i><?= e($m['label']) ?>
        </a>
        <?php endforeach; ?>
        <hr class="border-secondary mx-3">
        <a class="nav-link" href="../index.php" target="_blank"><i class="bi bi-eye me-2"></i>Lihat Website</a>
        <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i>Keluar</a>
    </nav>
</aside>
<main class="admin-main">
    <div class="admin-topbar d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="h5 mb-0 d-inline ms-2"><?= e($title) ?></h1>
        </div>
        <span class="text-muted small"><i class="bi bi-person-circle me-1"></i><?= e($adminName) ?></span>
    </div>
    <?php if ($msg = flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php
}

function admin_footer(): void
{
    ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
}

function csrf_field(): void
{
    echo '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}
