<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$events = getDB()->query('SELECT e.*, 
    (SELECT COUNT(*) FROM guestbook_rsvp g WHERE g.event_id = e.id) AS rsvp_count
    FROM events e ORDER BY e.created_at DESC')->fetchAll();

$pageTitle = 'Manajemen Undangan';
$currentPage = 'events';
ob_start();
?>
<div class="page-header">
    <div>
        <h1>Manajemen Undangan</h1>
        <p>Kelola draf undangan Haflah — buat, duplikasi, arsipkan</p>
    </div>
    <a href="<?= app_url('admin/events/create.php') ?>" class="btn btn-primary">+ Buat Undangan Baru</a>
</div>

<div class="card">
  <?php if (empty($events)): ?>
    <p class="empty-state">Belum ada undangan. <a href="<?= app_url('admin/events/create.php') ?>">Buat undangan pertama</a></p>
  <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Judul Acara</th>
                <th>Pesantren</th>
                <th>Slug</th>
                <th>Status</th>
                <th>RSVP</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $ev): ?>
            <tr>
                <td><strong><?= e($ev['title']) ?></strong></td>
                <td><?= e($ev['pesantren_name']) ?></td>
                <td><code><?= e($ev['slug']) ?></code></td>
                <td>
                    <span class="badge badge-<?= $ev['status'] === 'aktif' ? 'success' : 'muted' ?>">
                        <?= e(ucfirst($ev['status'])) ?>
                    </span>
                </td>
                <td><?= (int) $ev['rsvp_count'] ?></td>
                <td><?= date('d M Y', strtotime($ev['created_at'])) ?></td>
                <td class="actions-cell">
                    <a href="<?= app_url('admin/events/edit.php?id=' . $ev['id']) ?>" class="btn btn-sm">Edit</a>
                    <a href="<?= app_url('admin/events/duplicate.php?id=' . $ev['id']) ?>" class="btn btn-sm btn-outline" onclick="return confirm('Duplikasi draf undangan ini?')">Salin</a>
                    <?php if ($ev['status'] === 'aktif'): ?>
                        <a href="<?= e(getInvitationBaseUrl($ev['slug'])) ?>" target="_blank" class="btn btn-sm btn-outline">Lihat</a>
                    <?php endif; ?>
                    <a href="<?= app_url('admin/events/toggle.php?id=' . $ev['id']) ?>" class="btn btn-sm btn-warning">
                        <?= $ev['status'] === 'aktif' ? 'Arsipkan' : 'Aktifkan' ?>
                    </a>
                    <a href="<?= app_url('admin/events/delete.php?id=' . $ev['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus permanen undangan ini beserta semua RSVP?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
