<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$stats = getDashboardStats();
$events = getDB()->query("
    SELECT e.*,
        (SELECT COUNT(*) FROM guestbook_rsvp g WHERE g.event_id = e.id) AS rsvp_count,
        (SELECT COALESCE(SUM(pax_count), 0) FROM guestbook_rsvp g WHERE g.event_id = e.id AND g.status = 'hadir') AS pax_hadir
    FROM events e
    WHERE e.status = 'aktif'
    ORDER BY e.created_at DESC
    LIMIT 5
")->fetchAll();

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
ob_start();
?>
<div class="page-header">
    <h1>Dashboard Utama</h1>
    <p>Ringkasan undangan aktif dan kapasitas aula</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📋</div>
        <div class="stat-value"><?= $stats['active_events'] ?></div>
        <div class="stat-label">Undangan Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-value"><?= $stats['total_rsvp'] ?></div>
        <div class="stat-label">Total RSVP</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value"><?= $stats['total_hadir'] ?></div>
        <div class="stat-label">Tamu Hadir (Pax)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🪑</div>
        <div class="stat-value"><?= $stats['seat_capacity'] ?></div>
        <div class="stat-label">Kapasitas Kursi Aula</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Undangan Aktif Terbaru</h2>
        <a href="<?= app_url('admin/events/create.php') ?>" class="btn btn-primary btn-sm">+ Buat Undangan</a>
    </div>
  <?php if (empty($events)): ?>
    <p class="empty-state">Belum ada undangan aktif. <a href="<?= app_url('admin/events/create.php') ?>">Buat undangan pertama</a></p>
  <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Slug / URL</th>
                <th>RSVP</th>
                <th>Pax Hadir</th>
                <th>Kapasitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $ev): ?>
            <tr>
                <td><strong><?= e($ev['title']) ?></strong></td>
                <td><code>/<?= e($ev['slug']) ?></code></td>
                <td><?= (int) $ev['rsvp_count'] ?></td>
                <td><?= (int) $ev['pax_hadir'] ?></td>
                <td>
                    <?php
                    $pct = $ev['seat_capacity'] > 0 ? min(100, round($ev['pax_hadir'] / $ev['seat_capacity'] * 100)) : 0;
                    ?>
                    <div class="capacity-bar">
                        <div class="capacity-fill" style="width:<?= $pct ?>%"></div>
                    </div>
                    <small><?= (int) $ev['pax_hadir'] ?> / <?= (int) $ev['seat_capacity'] ?> (<?= $pct ?>%)</small>
                </td>
                <td>
                    <a href="<?= app_url('admin/events/edit.php?id=' . $ev['id']) ?>" class="btn btn-sm">Edit</a>
                    <a href="<?= e(getInvitationBaseUrl($ev['slug'])) ?>" target="_blank" class="btn btn-sm btn-outline">Lihat</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
