<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$events = getDB()->query('SELECT id, title, slug FROM events ORDER BY title')->fetchAll();
$eventId = (int) ($_GET['event_id'] ?? ($events[0]['id'] ?? 0));
$search  = trim($_GET['q'] ?? '');
$filter  = $_GET['status'] ?? '';

$rsvps = [];
$stats = ['total' => 0, 'hadir_count' => 0, 'absen_count' => 0, 'total_pax' => 0];
$selectedEvent = null;

if ($eventId) {
    $selectedEvent = getEventById($eventId);
    $stats = getEventRsvpStats($eventId);

    $sql = 'SELECT * FROM guestbook_rsvp WHERE event_id = ?';
    $params = [$eventId];

    if ($search !== '') {
        $sql .= ' AND guest_name LIKE ?';
        $params[] = '%' . $search . '%';
    }
    if (in_array($filter, ['hadir', 'absen'], true)) {
        $sql .= ' AND status = ?';
        $params[] = $filter;
    }
    $sql .= ' ORDER BY submitted_at DESC';

    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    $rsvps = $stmt->fetchAll();
}

$pageTitle = 'Rekap RSVP & Guestbook';
$currentPage = 'rsvp';
ob_start();
?>
<div class="page-header">
    <div>
        <h1>Rekap RSVP & Guestbook</h1>
        <p>Pantau konfirmasi kehadiran dan ucapan tamu undangan</p>
    </div>
    <?php if ($eventId): ?>
        <a href="<?= app_url('admin/rsvp/export.php?event_id=' . $eventId) ?>" class="btn btn-primary">⬇ Ekspor CSV</a>
    <?php endif; ?>
</div>

<div class="card">
    <form method="GET" class="filter-bar">
        <label>
            Pilih Acara
            <select name="event_id" onchange="this.form.submit()">
                <?php foreach ($events as $ev): ?>
                    <option value="<?= $ev['id'] ?>" <?= $eventId === (int)$ev['id'] ? 'selected' : '' ?>>
                        <?= e($ev['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Cari Nama
            <input type="text" name="q" value="<?= e($search) ?>" placeholder="Nama wali santri...">
        </label>
        <label>
            Status
            <select name="status">
                <option value="">Semua</option>
                <option value="hadir" <?= $filter === 'hadir' ? 'selected' : '' ?>>Hadir</option>
                <option value="absen" <?= $filter === 'absen' ? 'selected' : '' ?>>Tidak Hadir</option>
            </select>
        </label>
        <button type="submit" class="btn btn-outline">Filter</button>
    </form>
</div>

<?php if ($selectedEvent): ?>
<div class="stats-grid stats-compact">
    <div class="stat-card mini">
        <div class="stat-value"><?= (int) $stats['total'] ?></div>
        <div class="stat-label">Total Respons</div>
    </div>
    <div class="stat-card mini">
        <div class="stat-value"><?= (int) $stats['hadir_count'] ?></div>
        <div class="stat-label">Konfirmasi Hadir</div>
    </div>
    <div class="stat-card mini">
        <div class="stat-value"><?= (int) $stats['absen_count'] ?></div>
        <div class="stat-label">Tidak Hadir</div>
    </div>
    <div class="stat-card mini">
        <div class="stat-value"><?= (int) $stats['total_pax'] ?></div>
        <div class="stat-label">Total Pax Hadir</div>
    </div>
</div>

<div class="card">
  <?php if (empty($rsvps)): ?>
    <p class="empty-state">Belum ada respons RSVP untuk acara ini.</p>
  <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Tamu</th>
                <th>Status</th>
                <th>Jumlah Pax</th>
                <th>Ucapan / Doa</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rsvps as $i => $r): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><strong><?= e($r['guest_name']) ?></strong></td>
                <td>
                    <span class="badge badge-<?= $r['status'] === 'hadir' ? 'success' : 'danger' ?>">
                        <?= $r['status'] === 'hadir' ? 'Hadir' : 'Tidak Hadir' ?>
                    </span>
                </td>
                <td><?= (int) $r['pax_count'] ?></td>
                <td><?= e($r['greeting_note'] ?: '—') ?></td>
                <td><?= date('d M Y H:i', strtotime($r['submitted_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
  <?php endif; ?>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
