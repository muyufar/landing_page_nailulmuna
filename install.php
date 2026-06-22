<?php
/**
 * Installer satu kali — buat database, tabel, dan data awal.
 * Akses: http://localhost/landing%20page/install.php
 * Hapus atau rename file ini setelah instalasi berhasil.
 */
declare(strict_types=1);

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/includes/Database.php';

$cfg = require BASE_PATH . '/config/database.php';
$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = Database::connectWithoutDb();
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$cfg['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$cfg['dbname']}`");

        $schema = file_get_contents(BASE_PATH . '/database/schema.sql');
        $pdo->exec($schema);

        $seed = file_get_contents(BASE_PATH . '/database/seed.sql');
        foreach (array_filter(array_map('trim', explode(';', $seed))) as $sql) {
            if ($sql !== '') {
                $pdo->exec($sql);
            }
        }

        $username = trim($_POST['admin_username'] ?? 'admin');
        $password = $_POST['admin_password'] ?? 'admin123';
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash, full_name) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)');
        $stmt->execute([$username, $hash, 'Administrator']);

        if (!is_dir(BASE_PATH . '/uploads')) {
            mkdir(BASE_PATH . '/uploads/images', 0755, true);
        }

        $success = true;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi — Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Instalasi Landing Page</h1>
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            Instalasi berhasil! <a href="index.php">Lihat landing page</a> atau
                            <a href="admin/login.php">masuk ke back office</a>.
                            <hr>
                            <strong>Penting:</strong> Hapus atau rename file <code>install.php</code> demi keamanan.
                        </div>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <p class="text-muted small">Pastikan MySQL di XAMPP sudah berjalan. Database: <strong><?= htmlspecialchars($cfg['dbname']) ?></strong></p>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Username Admin</label>
                                <input type="text" name="admin_username" class="form-control" value="admin" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Admin</label>
                                <input type="password" name="admin_password" class="form-control" value="admin123" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Jalankan Instalasi</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
