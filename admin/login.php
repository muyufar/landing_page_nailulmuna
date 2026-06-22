<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $admin = content()->getAdminByUsername($username);
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            redirect('index.php');
        }
        $error = 'Username atau password salah.';
    } catch (Throwable $e) {
        $error = 'Database belum terpasang. Jalankan install.php terlebih dahulu.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Back Office</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Poppins, sans-serif; background: linear-gradient(135deg, #0a1628, #0d6e4f); min-height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 400px; border-radius: 12px; }
        .btn-gold { background: #d4a017; color: #0a1628; font-weight: 600; border: none; }
        .btn-gold:hover { background: #e8b422; color: #0a1628; }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card shadow-lg mx-auto">
        <div class="card-body p-4">
            <h1 class="h4 text-center mb-1">Back Office</h1>
            <p class="text-center text-muted small mb-4">API Nailul Muna</p>
            <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-gold w-100">Masuk</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
