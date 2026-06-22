<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if (isset($_GET['delete']) && verify_csrf($_GET['token'] ?? '')) {
    content()->deleteProgram((int) $_GET['delete']);
    flash('success', 'Program dihapus.');
    redirect('programs.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
    content()->saveProgram([
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'icon_class' => trim($_POST['icon_class'] ?? 'bi-book'),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ], $id);
    flash('success', 'Program disimpan.');
    redirect('programs.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    foreach (content()->allPrograms() as $row) {
        if ((int) $row['id'] === (int) $_GET['edit']) { $edit = $row; break; }
    }
}

$items = content()->allPrograms();
admin_header('Program Unggulan');
?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card card-admin"><div class="card-body">
            <form method="post">
                <?php csrf_field(); ?>
                <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
                <div class="mb-2"><label class="form-label">Judul</label><input type="text" name="title" class="form-control" value="<?= e($edit['title'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3" required><?= e($edit['description'] ?? '') ?></textarea></div>
                <div class="mb-2"><label class="form-label">Icon Bootstrap (contoh: bi-book)</label><input type="text" name="icon_class" class="form-control" value="<?= e($edit['icon_class'] ?? 'bi-book') ?>"><small class="text-muted"><a href="https://icons.getbootstrap.com/" target="_blank">Daftar icon</a></small></div>
                <div class="mb-2"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="<?= (int) ($edit['sort_order'] ?? 0) ?>"></div>
                <div class="form-check mb-2"><input type="checkbox" name="is_active" class="form-check-input" id="a" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>><label for="a" class="form-check-label">Aktif</label></div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                <?php if ($edit): ?><a href="programs.php" class="btn btn-outline-secondary btn-sm">Batal</a><?php endif; ?>
            </form>
        </div></div>
    </div>
    <div class="col-lg-7">
        <div class="card card-admin"><div class="card-body p-0">
            <table class="table mb-0"><thead><tr><th>Judul</th><th>Icon</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['title']) ?><br><small class="text-muted"><?= e(mb_strimwidth($item['description'], 0, 60, '...')) ?></small></td>
                <td><i class="bi <?= e($item['icon_class']) ?>"></i></td>
                <td class="text-end">
                    <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <a href="?delete=<?= (int) $item['id'] ?>&token=<?= e(csrf_token()) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div></div>
    </div>
</div>
<?php admin_footer(); ?>
