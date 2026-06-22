<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function asset_url(?string $path, string $fallback = ''): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return $fallback;
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if (str_starts_with($path, 'assets/')) {
        return $path;
    }
    return UPLOAD_URL . '/' . ltrim($path, '/');
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

function handle_upload(string $field, string $subdir = 'images', ?string $oldFile = null): ?string
{
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldFile;
    }

    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Gagal mengunggah file.');
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $mime = mime_content_type($_FILES[$field]['tmp_name']);
    if (!in_array($mime, $allowed, true)) {
        throw new RuntimeException('Format gambar tidak didukung (JPG, PNG, WEBP, GIF).');
    }

    $dir = UPLOAD_PATH . '/' . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION) ?: 'jpg';
    $filename = uniqid('img_', true) . '.' . strtolower($ext);
    $target = $dir . '/' . $filename;

    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        throw new RuntimeException('Tidak dapat menyimpan file.');
    }

    if ($oldFile && !preg_match('#^https?://#i', $oldFile)) {
        $oldPath = UPLOAD_PATH . '/' . ltrim($oldFile, '/');
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    return $subdir . '/' . $filename;
}

function delete_upload(?string $relativePath): void
{
    if (!$relativePath || preg_match('#^https?://#i', $relativePath)) {
        return;
    }
    $full = UPLOAD_PATH . '/' . ltrim($relativePath, '/');
    if (is_file($full)) {
        @unlink($full);
    }
}
