<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if (isset($_GET['delete']) && verify_csrf($_GET['token'] ?? '')) {
    content()->deleteStat((int) $_GET['delete']);
    flash('success', 'Statistik dihapus.');
    redirect('stats.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
    content()->saveStat([
        'value_text' => trim($_POST['value_text'] ?? ''),
        'label' => trim($_POST['label'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ], $id);
    flash('success', 'Statistik disimpan.');
    redirect('stats.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    foreach (content()->allStats() as $row) {
        if ((int) $row['id'] === (int) $_GET['edit']) { $edit = $row; break; }
    }
}

admin_header('Angka Bicara');
?>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-admin"><div class="card-body">
            <form method="post">
                <?php csrf_field(); ?>
                <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
                <div class="mb-2"><label class="form-label">Nilai (contoh: 125+ atau 1998)</label><input type="text" name="value_text" class="form-control" value="<?= e($edit['value_text'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">Label</label><input type="text" name="label" class="form-control" value="<?= e($edit['label'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="<?= (int) ($edit['sort_order'] ?? 0) ?>"></div>
                <div class="form-check mb-2"><input type="checkbox" name="is_active" id="a" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>><label for="a">Aktif</label></div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card card-admin"><div class="card-body p-0">
            <table class="table mb-0"><thead><tr><th>Nilai</th><th>Label</th><th></th></tr></thead>
            <tbody>
            <?php foreach (content()->allStats() as $item): ?>
            <tr>
                <td class="fw-bold"><?= e($item['value_text']) ?></td>
                <td><?= e($item['label']) ?></td>
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
