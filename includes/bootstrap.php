<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('UPLOAD_URL', 'uploads');

require_once BASE_PATH . '/includes/Database.php';
require_once BASE_PATH . '/includes/ContentRepository.php';
require_once BASE_PATH . '/includes/helpers.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = Database::connect();
    }
    return $pdo;
}

function content(): ContentRepository
{
    static $repo = null;
    if ($repo === null) {
        $repo = new ContentRepository(db());
    }
    return $repo;
}
