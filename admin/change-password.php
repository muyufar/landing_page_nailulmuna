<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($new !== $confirm || strlen($new) < 6) {
        flash('error', 'Password baru tidak cocok atau terlalu pendek (min. 6 karakter).');
    } else {
        $stmt = db()->prepare('SELECT * FROM admins WHERE id = ? LIMIT 1');
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch() ?: null;
        if ($admin && password_verify($current, $admin['password_hash'])) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([$hash, $_SESSION['admin_id']]);
            flash('success', 'Password berhasil diubah.');
        } else {
            flash('error', 'Password lama salah.');
        }
    }
    redirect('change-password.php');
}

admin_header('Ganti Password');
?>
<div class="card card-admin col-lg-6">
    <div class="card-body">
        <form method="post">
            <?php csrf_field(); ?>
            <div class="mb-3"><label class="form-label">Password Lama</label><input type="password" name="current_password" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Password Baru</label><input type="password" name="new_password" class="form-control" required minlength="6"></div>
            <div class="mb-3"><label class="form-label">Ulangi Password Baru</label><input type="password" name="confirm_password" class="form-control" required></div>
            <button type="submit" class="btn btn-success">Ubah Password</button>
        </form>
    </div>
</div>
<?php admin_footer(); ?>
