<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if (isset($_GET['delete']) && verify_csrf($_GET['token'] ?? '')) {
    content()->deleteFooterLink((int) $_GET['delete']);
    flash('success', 'Link dihapus.');
    redirect('footer-links.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
    content()->saveFooterLink([
        'label' => trim($_POST['label'] ?? ''),
        'url' => trim($_POST['url'] ?? '#'),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ], $id);
    flash('success', 'Link disimpan.');
    redirect('footer-links.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    foreach (content()->allFooterLinks() as $row) {
        if ((int) $row['id'] === (int) $_GET['edit']) { $edit = $row; break; }
    }
}

admin_header('Link Akses Sistem');
?>
<p class="text-muted small mb-3">
    Untuk Buku Tamu &amp; Undangan, kelola tampilan di menu
    <a href="settings-apps.php">Layanan Digital</a>.
</p>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-admin"><div class="card-body">
            <form method="post">
                <?php csrf_field(); ?>
                <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
                <div class="mb-2"><label class="form-label">Label</label><input type="text" name="label" class="form-control" value="<?= e($edit['label'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">URL</label><input type="text" name="url" class="form-control" value="<?= e($edit['url'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="<?= (int) ($edit['sort_order'] ?? 0) ?>"></div>
                <div class="form-check mb-2">
                    <input type="checkbox" name="is_active" id="a" <?= ($edit && (int) $edit['is_active']) || !$edit ? 'checked' : '' ?>>
                    <label for="a">Aktif (tampil di footer)</label>
                </div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card card-admin"><div class="card-body p-0">
            <table class="table mb-0"><thead><tr><th>Label</th><th>URL</th><th></th></tr></thead>
            <tbody>
            <?php foreach (content()->allFooterLinks() as $item): ?>
            <?php if (in_array($item['url'], ['tamu.php', 'undangan-portal.php'], true)) { continue; } ?>
            <tr>
                <td>
                    <?= e($item['label']) ?>
                    <?php if (!(int) $item['is_active']): ?>
                        <span class="badge bg-warning text-dark ms-1">Tersembunyi</span>
                    <?php endif; ?>
                </td>
                <td><code class="small"><?= e($item['url']) ?></code></td>
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
