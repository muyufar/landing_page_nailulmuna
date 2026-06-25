<?php
/**
 * Skrip instalasi satu kali — jalankan via browser:
 * http://localhost/landing%20page/undangan/install.php
 * Hapus file ini setelah instalasi berhasil.
 */

require_once __DIR__ . '/config/database.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET,
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $sql = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec($sql);
        $success = true;
        $message = 'Database berhasil diinstal! Silakan login ke Back Office.';
    } catch (PDOException $e) {
        $message = 'Gagal: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi — Haflah Undangan</title>
    <link rel="stylesheet" href="<?= app_url('assets/css/admin.css') ?>">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <span class="login-icon">🕌</span>
            <h1>Instalasi Database</h1>
            <p>Haflah Multi-Event Undangan Digital</p>
        </div>
        <?php if ($message): ?>
            <div class="alert alert-<?= $success ? 'success' : 'error' ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <a href="<?= app_url('admin/login.php') ?>" class="btn btn-primary btn-block">Ke Login Admin</a>
        <?php else: ?>
            <p style="margin-bottom:1rem;font-size:.9rem;color:#6b7c77;">
                Pastikan MySQL di XAMPP sudah berjalan, lalu klik tombol di bawah.
            </p>
            <form method="POST">
                <button type="submit" class="btn btn-primary btn-block">Install Database</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
