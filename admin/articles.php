<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if (isset($_GET['delete']) && verify_csrf($_GET['token'] ?? '')) {
    content()->deleteArticle((int) $_GET['delete']);
    flash('success', 'Artikel dihapus.');
    redirect('articles.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    try {
        $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
        $old = null;
        if ($id) {
            foreach (content()->allArticles() as $r) {
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
        content()->saveArticle([
            'title' => trim($_POST['title'] ?? ''),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'category' => in_array($_POST['category'] ?? '', ['tausiyah', 'berita'], true) ? $_POST['category'] : 'berita',
            'image_path' => $img ?? $old,
            'link_url' => trim($_POST['link_url'] ?? '#'),
            'published_at' => $_POST['published_at'] ?: null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ], $id);
        flash('success', 'Artikel disimpan.');
        redirect('articles.php');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    foreach (content()->allArticles() as $row) {
        if ((int) $row['id'] === (int) $_GET['edit']) { $edit = $row; break; }
    }
}

admin_header('Tausiyah & Berita');
?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card card-admin"><div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <?php csrf_field(); ?>
                <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
                <div class="mb-2"><label class="form-label">Judul</label><input type="text" name="title" class="form-control" value="<?= e($edit['title'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">Ringkasan</label><textarea name="excerpt" class="form-control" rows="3" required><?= e($edit['excerpt'] ?? '') ?></textarea></div>
                <div class="mb-2"><label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="tausiyah" <?= ($edit['category'] ?? '') === 'tausiyah' ? 'selected' : '' ?>>Tausiyah Masyayikh</option>
                        <option value="berita" <?= ($edit['category'] ?? 'berita') === 'berita' ? 'selected' : '' ?>>Berita Kegiatan</option>
                    </select>
                </div>
                <div class="mb-2"><label class="form-label">URL Gambar</label><input type="url" name="image_url" class="form-control" value="<?= ($edit && preg_match('#^https?://#', $edit['image_path'] ?? '')) ? e($edit['image_path']) : '' ?>"></div>
                <div class="mb-2"><label class="form-label">Upload Gambar</label><input type="file" name="image_file" class="form-control" accept="image/*"></div>
                <div class="mb-2"><label class="form-label">Link Baca Selengkapnya</label><input type="text" name="link_url" class="form-control" value="<?= e($edit['link_url'] ?? '#') ?>"></div>
                <div class="mb-2"><label class="form-label">Tanggal Terbit</label><input type="date" name="published_at" class="form-control" value="<?= e($edit['published_at'] ?? date('Y-m-d')) ?>"></div>
                <div class="form-check mb-2"><input type="checkbox" name="is_active" id="a" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>><label for="a">Aktif</label></div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-7">
        <?php foreach (content()->allArticles() as $item): ?>
        <div class="card card-admin mb-2"><div class="card-body d-flex gap-3">
            <img src="<?= e(asset_url($item['image_path'] ?? '')) ?>" class="preview-thumb" alt="">
            <div class="flex-grow-1">
                <span class="badge bg-secondary"><?= e($item['category']) ?></span>
                <strong class="d-block"><?= e($item['title']) ?></strong>
                <small class="text-muted"><?= e($item['published_at'] ?? '') ?></small>
            </div>
            <div>
                <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="?delete=<?= (int) $item['id'] ?>&token=<?= e(csrf_token()) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</a>
            </div>
        </div></div>
        <?php endforeach; ?>
    </div>
</div>
<?php admin_footer(); ?>
