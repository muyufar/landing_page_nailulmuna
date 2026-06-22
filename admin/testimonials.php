<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if (isset($_GET['delete']) && verify_csrf($_GET['token'] ?? '')) {
    content()->deleteTestimonial((int) $_GET['delete']);
    flash('success', 'Testimoni dihapus.');
    redirect('testimonials.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    try {
        $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
        $old = null;
        if ($id) {
            foreach (content()->allTestimonials() as $r) {
                if ((int) $r['id'] === $id) { $old = $r['avatar']; break; }
            }
        }
        $avatar = handle_upload('avatar_file', 'images', $old);
        content()->saveTestimonial([
            'name' => trim($_POST['name'] ?? ''),
            'role_label' => trim($_POST['role_label'] ?? ''),
            'quote_text' => trim($_POST['quote_text'] ?? ''),
            'avatar' => $avatar,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ], $id);
        flash('success', 'Testimoni disimpan.');
        redirect('testimonials.php');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    foreach (content()->allTestimonials() as $row) {
        if ((int) $row['id'] === (int) $_GET['edit']) { $edit = $row; break; }
    }
}

admin_header('Testimoni Santri');
?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card card-admin"><div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <?php csrf_field(); ?>
                <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
                <div class="mb-2"><label class="form-label">Nama</label><input type="text" name="name" class="form-control" value="<?= e($edit['name'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">Peran / Kelas</label><input type="text" name="role_label" class="form-control" value="<?= e($edit['role_label'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">Kutipan</label><textarea name="quote_text" class="form-control" rows="4" required><?= e($edit['quote_text'] ?? '') ?></textarea></div>
                <div class="mb-2"><label class="form-label">Foto (opsional)</label><input type="file" name="avatar_file" class="form-control" accept="image/*"></div>
                <div class="mb-2"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="<?= (int) ($edit['sort_order'] ?? 0) ?>"></div>
                <div class="form-check mb-2"><input type="checkbox" name="is_active" id="a" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>><label for="a">Aktif</label></div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-7">
        <?php foreach (content()->allTestimonials() as $item): ?>
        <div class="card card-admin mb-2"><div class="card-body py-2 d-flex justify-content-between">
            <div><strong><?= e($item['name']) ?></strong> — <?= e($item['role_label']) ?><br><small><?= e(mb_strimwidth($item['quote_text'], 0, 80, '...')) ?></small></div>
            <div>
                <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="?delete=<?= (int) $item['id'] ?>&token=<?= e(csrf_token()) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</a>
            </div>
        </div></div>
        <?php endforeach; ?>
    </div>
</div>
<?php admin_footer(); ?>
