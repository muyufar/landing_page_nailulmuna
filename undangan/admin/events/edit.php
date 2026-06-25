<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/form.php';
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$event = getEventById($id);
if (!$event) {
    flash('error', 'Undangan tidak ditemukan.');
    header('Location: ' . app_url('admin/events/index.php'));
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = collectEventData($_POST);
    $errors = validateEventData($data);
    if (empty($errors)) {
        $result = saveEvent($data, $id, $_FILES);
        if ($result['success']) {
            flash('success', 'Perubahan berhasil disimpan.');
            $tab = $_POST['tab'] ?? 'konten';
            header('Location: ' . app_url('admin/events/edit.php?id=' . $id . '&tab=' . urlencode($tab)));
            exit;
        }
        $errors = $result['errors'];
    }
    $event = array_merge($event, collectEventData($_POST));
}

$pageTitle = 'Edit Undangan';
$currentPage = 'events';
ob_start();
?>
<div class="page-header">
    <div>
        <h1>Editor Isi & Visual</h1>
        <p><?= e($event['title']) ?> — <code>/<?= e($event['slug']) ?></code></p>
    </div>
    <a href="<?= e(getPersonalizedInvitationUrl($event['slug'], 'Nama Tamu')) ?>" target="_blank" class="btn btn-outline">Pratinjau Undangan</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card">
    <?php renderEventForm($event, '', 'Simpan Perubahan'); ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
