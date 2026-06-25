<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$user = currentUser();
$flash = getFlash();
$currentPage = $currentPage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Back Office') ?> — Haflah Undangan</title>
    <link rel="stylesheet" href="<?= app_url('assets/css/admin.css') ?>">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="brand-icon">🕌</span>
                <div>
                    <strong>Haflah Undangan</strong>
                    <small>Back Office</small>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= app_url('admin/index.php') ?>" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <span>📊</span> Dashboard
                </a>
                <a href="<?= app_url('admin/events/index.php') ?>" class="nav-item <?= $currentPage === 'events' ? 'active' : '' ?>">
                    <span>📋</span> Manajemen Undangan
                </a>
                <a href="<?= app_url('admin/rsvp/index.php') ?>" class="nav-item <?= $currentPage === 'rsvp' ? 'active' : '' ?>">
                    <span>💬</span> Rekap RSVP
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <strong><?= e($user['username']) ?></strong>
                    <small><?= e($user['role']) ?></small>
                </div>
                <a href="<?= app_url('admin/logout.php') ?>" class="btn-logout">Keluar</a>
            </div>
        </aside>
        <main class="main-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>
            <?= $content ?? '' ?>
        </main>
    </div>
    <script src="<?= app_url('assets/js/admin.js') ?>"></script>
</body>
</html>
