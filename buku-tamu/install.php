<?php
/**
 * Installer - Jalankan sekali: http://localhost/landing%20page/buku-tamu/install.php
 * Hapus file ini setelah instalasi berhasil!
 */

$config = require __DIR__ . '/config/database.php';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $config['host'], $config['port']);
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $sql = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec($sql);

        $uploadDir = __DIR__ . '/public/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $success = true;
    } catch (PDOException $e) {
        $errors[] = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi Buku Tamu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-xl font-bold mb-4">Instalasi Buku Tamu Online</h1>

        <?php if ($success): ?>
        <div class="bg-green-50 text-green-800 p-4 rounded-xl mb-4">
            <p class="font-semibold">Instalasi berhasil!</p>
            <ul class="text-sm mt-2 space-y-1">
                <li>Form Tamu: <a href="public/" class="underline">/landing page/buku-tamu/public/</a></li>
                <li>Admin: <a href="public/admin/login" class="underline">admin / admin123</a></li>
                <li>Ndalem: <a href="public/ndalem/login" class="underline">ndalem / ndalem123</a></li>
            </ul>
            <p class="text-xs mt-3 text-red-600 font-semibold">⚠ Hapus file install.php demi keamanan!</p>
        </div>
        <?php else: ?>
        <?php foreach ($errors as $err): ?>
        <div class="bg-red-50 text-red-700 p-3 rounded-xl mb-3 text-sm"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <p class="text-sm text-gray-600 mb-4">Pastikan MySQL/XAMPP sudah berjalan, lalu klik tombol di bawah untuk membuat database dan tabel.</p>
        <form method="POST">
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl">
                Install Database
            </button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
