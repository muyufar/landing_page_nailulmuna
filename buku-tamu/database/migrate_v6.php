<?php
require __DIR__ . '/../src/Database.php';
$db = Database::connect();

$cols = $db->query("SHOW COLUMNS FROM visitors LIKE 'waktu_temu'")->fetch();
if (!$cols) {
    $db->exec('ALTER TABLE visitors ADD COLUMN waktu_temu DATETIME DEFAULT NULL AFTER jadwal_kunjungan');
    echo "Column waktu_temu added\n";
} else {
    echo "Column waktu_temu already exists\n";
}
