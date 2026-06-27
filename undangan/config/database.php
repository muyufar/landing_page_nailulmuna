<?php

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'u700125577_santri');
define('DB_USER', 'u700125577_santri');
define('DB_PASS', 'Landingpage@1990');
define('DB_CHARSET', 'utf8mb4');

define('APP_BASE', '/landing page/undangan');

function app_url(string $path = ''): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    $url = rtrim(APP_BASE, '/') . ($path !== '' ? '/' . $path : '');

    return str_replace(' ', '%20', $url);
}

define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_DIR', BASE_PATH . '/assets/uploads/logos/');
define('UPLOAD_URL', APP_BASE . '/assets/uploads/logos/');
define('ORNAMENT_UPLOAD_DIR', BASE_PATH . '/assets/uploads/ornaments/');
define('ORNAMENT_UPLOAD_URL', APP_BASE . '/assets/uploads/ornaments/');
define('FONT_UPLOAD_DIR', BASE_PATH . '/assets/uploads/fonts/');
define('FONT_UPLOAD_URL', APP_BASE . '/assets/uploads/fonts/');
define('MAX_LOGO_SIZE', 1048576); // 1 MB
define('MAX_ORNAMENT_SIZE', 2097152); // 2 MB
define('MAX_FONT_SIZE', 3145728); // 3 MB
define('ALLOWED_LOGO_TYPES', ['image/png', 'image/jpeg', 'image/jpg']);
define('ALLOWED_LOGO_EXT', ['png', 'jpg', 'jpeg']);
define('ALLOWED_FONT_EXT', ['woff2', 'woff', 'ttf']);
define('ALLOWED_FONT_TYPES', [
    'font/woff2', 'font/woff', 'application/font-woff2', 'application/font-woff',
    'application/x-font-woff', 'application/x-font-ttf', 'font/ttf', 'application/octet-stream',
]);

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
