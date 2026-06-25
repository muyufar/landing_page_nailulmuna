<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/form.php';
requireLogin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = collectEventData($_POST);
    $errors = validateEventData($data);
    if (empty($errors)) {
        $result = saveEvent($data, null, $_FILES);
        if ($result['success']) {
            flash('success', 'Undangan berhasil dibuat.');
            header('Location: ' . app_url('admin/events/edit.php?id=' . $result['id']));
            exit;
        }
        $errors = $result['errors'];
    }
}

$pageTitle = 'Buat Undangan Baru';
$currentPage = 'events';
ob_start();
?>
<div class="page-header">
    <h1>Buat Undangan Baru</h1>
    <p>Isi konten, tema visual, dan pengaturan audio</p>
</div>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card">
    <?php renderEventForm($_POST ?? [], '', 'Buat Undangan'); ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
