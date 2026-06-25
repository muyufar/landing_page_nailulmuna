<?php
require_once __DIR__ . '/config/database.php';
header('Content-Type: text/html; charset=utf-8');

$columns = [
    "ADD COLUMN ornament_animation VARCHAR(30) NOT NULL DEFAULT 'melayang' AFTER animated_ornaments",
    "ADD COLUMN font_preset VARCHAR(30) NOT NULL DEFAULT 'klasik' AFTER theme_preset",
    'ADD COLUMN font_custom_title TEXT NULL AFTER font_preset',
    'ADD COLUMN font_custom_body TEXT NULL AFTER font_custom_title',
];

$db = getDB();
echo '<h2>Migrasi Font & Animasi</h2><ul>';
foreach ($columns as $col) {
    try {
        $db->exec("ALTER TABLE events {$col}");
        echo '<li style="color:green">[ok] ' . htmlspecialchars($col) . '</li>';
    } catch (PDOException $e) {
        $color = str_contains($e->getMessage(), 'Duplicate column') ? 'gray' : 'red';
        $status = str_contains($e->getMessage(), 'Duplicate column') ? 'skip' : 'err';
        echo '<li style="color:' . $color . '">[' . $status . '] ' . htmlspecialchars($col) . '</li>';
    }
}
$db->exec("UPDATE events SET ornament_animation = 'melayang' WHERE ornament_animation IS NULL OR ornament_animation = ''");
$db->exec("UPDATE events SET font_preset = 'klasik' WHERE font_preset IS NULL OR font_preset = ''");
echo '</ul><p><a href="<?= app_url('admin/') ?>">Ke Back Office</a></p>';
