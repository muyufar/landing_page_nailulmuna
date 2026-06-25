<?php
require_once __DIR__ . '/config/database.php';
header('Content-Type: text/html; charset=utf-8');

$columns = [
    'ADD COLUMN scroll_speed INT NOT NULL DEFAULT 800 AFTER scroll_interval',
    'ADD COLUMN invitation_pages TEXT NULL AFTER section_animations',
];

$db = getDB();
echo '<h2>Migrasi Halaman & Kecepatan Gulir</h2><ul>';
foreach ($columns as $col) {
    try {
        $db->exec("ALTER TABLE events {$col}");
        echo '<li style="color:green">[ok] ' . htmlspecialchars($col) . '</li>';
    } catch (PDOException $e) {
        $status = str_contains($e->getMessage(), 'Duplicate column') ? 'skip' : 'err';
        $color  = $status === 'skip' ? 'gray' : 'red';
        echo '<li style="color:' . $color . '">[' . $status . '] ' . htmlspecialchars($col) . ' — ' . htmlspecialchars($e->getMessage()) . '</li>';
    }
}
echo '</ul>';
