<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Path web ke root proyek, mis. "/landing page" */
function public_base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $dir = dirname($script);
    if (str_ends_with($dir, '/admin')) {
        $dir = dirname($dir);
    }

    $base = ($dir === '/' || $dir === '.' || $dir === '\\') ? '' : rtrim($dir, '/');
    return $base;
}

function public_url(string $relativePath): string
{
    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
    $base = public_base_path();

    if ($base === '') {
        return '/' . $relativePath;
    }

    return $base . '/' . $relativePath;
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
        return public_url($path);
    }
    return public_url(UPLOAD_URL . '/' . ltrim($path, '/'));
}

function has_file_upload(string $field): bool
{
    return !empty($_FILES[$field]['name'])
        && (int) ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
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

function upload_error_message(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran file terlalu besar. Maksimal sesuai batas server (upload_max_filesize).',
        UPLOAD_ERR_PARTIAL => 'File hanya terunggah sebagian. Coba lagi.',
        UPLOAD_ERR_NO_TMP_DIR => 'Folder temp server tidak tersedia.',
        UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis file ke disk.',
        UPLOAD_ERR_EXTENSION => 'Upload diblokir oleh ekstensi PHP.',
        default => 'Gagal mengunggah file (kode error: ' . $code . ').',
    };
}

function detect_image_mime(string $tmpPath, string $originalName): ?string
{
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $mime = null;

    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo !== false) {
            $mime = finfo_file($finfo, $tmpPath) ?: null;
            finfo_close($finfo);
        }
    }

    if (!$mime && function_exists('mime_content_type')) {
        $mime = mime_content_type($tmpPath) ?: null;
    }

    if ($mime && in_array($mime, $allowed, true)) {
        return $mime;
    }

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $byExt = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
    ];

    return $byExt[$ext] ?? null;
}

function handle_upload(string $field, string $subdir = 'images', ?string $oldFile = null): ?string
{
    if (!has_file_upload($field)) {
        return $oldFile;
    }

    $error = (int) ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException(upload_error_message($error));
    }

    $mime = detect_image_mime($_FILES[$field]['tmp_name'], $_FILES[$field]['name']);
    if ($mime === null) {
        throw new RuntimeException('Format gambar tidak didukung (JPG, PNG, WEBP, GIF).');
    }

    $dir = UPLOAD_PATH . DIRECTORY_SEPARATOR . $subdir;
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Folder upload tidak dapat dibuat: uploads/' . $subdir);
    }

    if (!is_writable($dir)) {
        throw new RuntimeException('Folder uploads tidak bisa ditulis. Periksa izin folder uploads/images.');
    }

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        default => 'jpg',
    };
    $filename = uniqid('img_', true) . '.' . $ext;
    $target = $dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        throw new RuntimeException('Tidak dapat menyimpan file ke uploads/' . $subdir . '.');
    }

    if ($oldFile && !preg_match('#^https?://#i', $oldFile) && !str_starts_with($oldFile, 'assets/')) {
        $oldPath = UPLOAD_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($oldFile, '/'));
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
