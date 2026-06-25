<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

$type = in_array($_GET['type'] ?? '', ['prestasi', 'daily', 'fasilitas'], true) ? $_GET['type'] : 'prestasi';

if (isset($_GET['delete']) && verify_csrf($_GET['token'] ?? '')) {
    content()->deleteGallery((int) $_GET['delete']);
    flash('success', 'Foto dihapus.');
    redirect('gallery.php?type=' . $type);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    try {
        $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
        $old = null;
        if ($id) {
            foreach (content()->allGallery($type) as $r) {
                if ((int) $r['id'] === $id) { $old = $r['image_path']; break; }
            }
        }
        if (has_file_upload('image_file')) {
            $img = handle_upload('image_file', 'images', $old);
        } elseif (!empty($_POST['image_url'])) {
            $img = trim($_POST['image_url']);
        } else {
            $img = $old;
        }
        if (!$img && !$id) {
            throw new RuntimeException('Gambar wajib diisi (upload atau URL).');
        }
        content()->saveGallery([
            'gallery_type' => $_POST['gallery_type'] ?? $type,
            'title' => trim($_POST['title'] ?? ''),
            'image_path' => $img ?? $old,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ], $id);
        flash('success', 'Galeri disimpan.');
        redirect('gallery.php?type=' . ($_POST['gallery_type'] ?? $type));
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    foreach (content()->allGallery($type) as $row) {
        if ((int) $row['id'] === (int) $_GET['edit']) { $edit = $row; break; }
    }
}

admin_header('Galeri Foto');
?>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= $type === 'prestasi' ? 'active' : '' ?>" href="?type=prestasi">Prestasi</a></li>
    <li class="nav-item"><a class="nav-link <?= $type === 'daily' ? 'active' : '' ?>" href="?type=daily">Daily Life</a></li>
    <li class="nav-item"><a class="nav-link <?= $type === 'fasilitas' ? 'active' : '' ?>" href="?type=fasilitas">Fasilitas</a></li>
</ul>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card card-admin"><div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <?php csrf_field(); ?>
                <input type="hidden" name="gallery_type" value="<?= e($type) ?>">
                <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
                <div class="mb-2"><label class="form-label">Judul Foto</label><input type="text" name="title" class="form-control" value="<?= e($edit['title'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">URL Gambar</label><input type="url" name="image_url" class="form-control" value="<?= ($edit && preg_match('#^https?://#', $edit['image_path'])) ? e($edit['image_path']) : '' ?>"></div>
                <div class="mb-2"><label class="form-label">Upload</label><input type="file" name="image_file" class="form-control" accept="image/*"></div>
                <div class="mb-2"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="<?= (int) ($edit['sort_order'] ?? 0) ?>"></div>
                <div class="form-check mb-2"><input type="checkbox" name="is_active" id="a" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>><label for="a">Aktif</label></div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="row g-2">
        <?php foreach (content()->allGallery($type) as $item): ?>
            <div class="col-md-4">
                <div class="card card-admin">
                    <img src="<?= e(asset_url($item['image_path'])) ?>" class="card-img-top" style="height:120px;object-fit:cover" alt="">
                    <div class="card-body py-2">
                        <small class="fw-bold"><?= e($item['title']) ?></small>
                        <div class="mt-1">
                            <a href="?type=<?= e($type) ?>&edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="?type=<?= e($type) ?>&delete=<?= (int) $item['id'] ?>&token=<?= e(csrf_token()) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php admin_footer(); ?>
