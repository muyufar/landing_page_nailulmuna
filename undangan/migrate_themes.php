<?php
/**
 * Jalankan sekali: http://localhost/landing%20page/undangan/migrate_themes.php
 * Hapus file ini setelah migrasi berhasil.
 */
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

$columns = [
    "ADD COLUMN theme_preset VARCHAR(30) NOT NULL DEFAULT 'hijau_emas' AFTER color_accent",
    'ADD COLUMN ornament_top TEXT NULL AFTER logo_haflah',
    'ADD COLUMN ornament_divider TEXT NULL AFTER ornament_top',
    'ADD COLUMN ornament_bottom TEXT NULL AFTER ornament_divider',
    'ADD COLUMN bg_image TEXT NULL AFTER ornament_bottom',
];

$db = getDB();
$results = [];

foreach ($columns as $col) {
    try {
        $db->exec("ALTER TABLE events {$col}");
        $results[] = ['ok', $col];
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column')) {
            $results[] = ['skip', $col];
        } else {
            $results[] = ['err', $col . ' — ' . $e->getMessage()];
        }
    }
}

echo '<h2>Migrasi Tema & Ornamen</h2><ul>';
foreach ($results as [$status, $msg]) {
    $color = $status === 'ok' ? 'green' : ($status === 'skip' ? 'gray' : 'red');
    echo "<li style=\"color:{$color}\">[{$status}] " . htmlspecialchars($msg) . '</li>';
}
echo '</ul><p><a href="<?= app_url('admin/') ?>">Ke Back Office</a></p>';
