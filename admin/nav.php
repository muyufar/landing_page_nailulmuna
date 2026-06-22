<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if (isset($_GET['delete']) && verify_csrf($_GET['token'] ?? '')) {
    content()->deleteNavItem((int) $_GET['delete']);
    flash('success', 'Menu dihapus.');
    redirect('nav.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
    content()->saveNavItem([
        'label' => trim($_POST['label'] ?? ''),
        'url_hash' => trim($_POST['url_hash'] ?? '#'),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ], $id);
    flash('success', $id ? 'Menu diperbarui.' : 'Menu ditambahkan.');
    redirect('nav.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    foreach (content()->allNavItems() as $row) {
        if ((int) $row['id'] === (int) $_GET['edit']) {
            $edit = $row;
            break;
        }
    }
}

$items = content()->allNavItems();
admin_header('Menu Navigasi');
?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card card-admin">
            <div class="card-body">
                <h6><?= $edit ? 'Edit' : 'Tambah' ?> Menu</h6>
                <form method="post">
                    <?php csrf_field(); ?>
                    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
                    <div class="mb-2"><label class="form-label">Label</label><input type="text" name="label" class="form-control" value="<?= e($edit['label'] ?? '') ?>" required></div>
                    <div class="mb-2"><label class="form-label">Link (#section)</label><input type="text" name="url_hash" class="form-control" value="<?= e($edit['url_hash'] ?? '#') ?>" required></div>
                    <div class="mb-2"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="<?= (int) ($edit['sort_order'] ?? 0) ?>"></div>
                    <div class="form-check mb-2"><input type="checkbox" name="is_active" class="form-check-input" id="active" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>><label class="form-check-label" for="active">Aktif</label></div>
                    <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                    <?php if ($edit): ?><a href="nav.php" class="btn btn-outline-secondary btn-sm">Batal</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-admin"><div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Label</th><th>Link</th><th>Urut</th><th>Aktif</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['label']) ?></td>
                    <td><code><?= e($item['url_hash']) ?></code></td>
                    <td><?= (int) $item['sort_order'] ?></td>
                    <td><?= $item['is_active'] ? 'Ya' : 'Tidak' ?></td>
                    <td class="text-end">
                        <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="?delete=<?= (int) $item['id'] ?>&token=<?= e(csrf_token()) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus menu ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div></div>
    </div>
</div>
<?php admin_footer(); ?>
