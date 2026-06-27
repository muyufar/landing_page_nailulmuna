<?php
/**
 * Template konfigurasi database — Undangan Digital
 *
 * Salin bagian DB ke database.php dan sesuaikan environment Anda.
 * APP_BASE sesuaikan dengan path folder undangan di server.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'haflah_undangan');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Path web ke folder undangan (spasi → %20 di URL otomatis via app_url)
define('APP_BASE', '/landing page/undangan');
