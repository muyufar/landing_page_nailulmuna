<?php
/**
 * Jalankan sekali: php database/migrate_v2.php
 */
require __DIR__ . '/../src/Database.php';

$db = Database::connect();

function columnExists(PDO $db, string $table, string $column): bool
{
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?"
    );
    $stmt->execute([$table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

$alters = [
    "ALTER TABLE visitors ADD COLUMN jenis_kedatangan ENUM('sekarang','jadwal') NOT NULL DEFAULT 'sekarang' AFTER foto_path",
    "ALTER TABLE visitors ADD COLUMN jadwal_kunjungan DATETIME NULL AFTER jenis_kedatangan",
    "ALTER TABLE visitors ADD COLUMN area_masuk ENUM('pesantren','ndalem') NOT NULL DEFAULT 'pesantren' AFTER jadwal_kunjungan",
    "ALTER TABLE visitors ADD COLUMN staff_wa_notified TINYINT(1) DEFAULT 0 AFTER whatsapp_sent",
    "ALTER TABLE visitors ADD COLUMN jadwal_wa_notified TINYINT(1) DEFAULT 0 AFTER staff_wa_notified",
];

foreach ($alters as $sql) {
    preg_match('/ADD COLUMN (\w+)/', $sql, $m);
    $col = $m[1] ?? '';
    if ($col && columnExists($db, 'visitors', $col)) {
        echo "Skip column: $col\n";
        continue;
    }
    try {
        $db->exec($sql);
        echo "OK: $col\n";
    } catch (PDOException $e) {
        echo "Error $col: " . $e->getMessage() . "\n";
    }
}

$db->exec("UPDATE visitors SET area_masuk = 'ndalem' WHERE tujuan_kunjungan = 'sowan'");

try {
    $db->exec('ALTER TABLE whatsapp_logs MODIFY visitor_id INT NULL');
} catch (PDOException $e) {
    echo 'whatsapp_logs visitor_id: ' . $e->getMessage() . "\n";
}

if (!columnExists($db, 'whatsapp_logs', 'recipient_type')) {
    try {
        $db->exec('ALTER TABLE whatsapp_logs ADD COLUMN recipient_type VARCHAR(30) NULL AFTER phone');
        echo "OK: recipient_type\n";
    } catch (PDOException $e) {
        echo 'recipient_type: ' . $e->getMessage() . "\n";
    }
}

$settings = [
    'whatsapp_enabled' => '0',
    'whatsapp_provider' => 'fonnte',
    'whatsapp_token' => '',
    'wa_phone_pengasuh' => '',
    'wa_phone_ndalem' => '',
    'wa_phone_kantor' => '',
    'wa_enabled_pengasuh' => '1',
    'wa_enabled_ndalem' => '1',
    'wa_enabled_kantor' => '1',
    'wa_on_register_pengasuh' => '1',
    'wa_on_register_ndalem' => '1',
    'wa_on_register_kantor' => '1',
    'wa_on_checkin_pengasuh' => '1',
    'wa_on_checkin_ndalem' => '1',
    'wa_on_checkin_kantor' => '1',
    'wa_on_jadwal_pengasuh' => '1',
    'wa_on_jadwal_ndalem' => '1',
    'wa_on_jadwal_kantor' => '1',
    'wa_on_approve_guest' => '1',
    'wa_jadwal_reminder_minutes' => '60',
];

$stmt = $db->prepare('INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)');
foreach ($settings as $k => $v) {
    $stmt->execute([$k, $v]);
}

echo "Migrasi v2 selesai.\n";
