<?php
require_once __DIR__ . '/config/database.php';
header('Content-Type: text/html; charset=utf-8');

$columns = [
    "ADD COLUMN invitation_greeting VARCHAR(150) NOT NULL DEFAULT 'Kepada Yth. Bapak/Ibu/Saudara/i' AFTER theme_statement",
    'ADD COLUMN event_schedule TEXT NULL AFTER special_rules',
    'ADD COLUMN auto_scroll TINYINT(1) NOT NULL DEFAULT 1 AFTER bg_image',
    'ADD COLUMN animated_ornaments TINYINT(1) NOT NULL DEFAULT 1 AFTER auto_scroll',
];

$db = getDB();
echo '<h2>Migrasi Fitur Undangan</h2><ul>';
foreach ($columns as $col) {
    try {
        $db->exec("ALTER TABLE events {$col}");
        echo '<li style="color:green">[ok] ' . htmlspecialchars($col) . '</li>';
    } catch (PDOException $e) {
        $msg = str_contains($e->getMessage(), 'Duplicate column') ? 'skip' : 'err';
        $color = $msg === 'skip' ? 'gray' : 'red';
        echo '<li style="color:' . $color . '">[' . $msg . '] ' . htmlspecialchars($col) . '</li>';
    }
}

// Demo susunan acara untuk undangan haflah-2026
$demoSchedule = json_encode([
    ['time' => '08.00', 'title' => 'Pembukaan & Qori\'ah', 'desc' => 'Bacaan ayat suci Al-Qur\'an'],
    ['time' => '08.30', 'title' => 'Sambutan Ketua Panitia', 'desc' => ''],
    ['time' => '09.00', 'title' => 'Pembacaan Mukaddimah', 'desc' => 'Sambutan pihak pesantren'],
    ['time' => '09.30', 'title' => 'Acara Inti Haflah', 'desc' => 'Pentas & penghargaan santri'],
    ['time' => '11.00', 'title' => 'Pengukuhan & Doa', 'desc' => 'Penutup acara'],
], JSON_UNESCAPED_UNICODE);

$stmt = $db->prepare('UPDATE events SET event_schedule = ? WHERE slug = ? AND (event_schedule IS NULL OR event_schedule = ?)');
$stmt->execute([$demoSchedule, 'haflah-2026', '']);
echo '<li style="color:green">[ok] Demo susunan acara haflah-2026</li>';
echo '</ul><p><a href="<?= app_url('admin/') ?>">Ke Back Office</a></p>';
