<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . app_url('admin/index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password && login($username, $password)) {
        header('Location: ' . app_url('admin/index.php'));
        exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Haflah Undangan</title>
    <link rel="stylesheet" href="<?= app_url('assets/css/admin.css') ?>">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <span class="login-icon">🕌</span>
            <h1>Back Office</h1>
            <p>Panel Admin Undangan Haflah</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <label>
                Username
                <input type="text" name="username" required autofocus autocomplete="username">
            </label>
            <label>
                Password
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
        </form>
        <p class="login-hint">Default: <code>admin</code> / <code>admin123</code></p>
    </div>
</body>
</html>
