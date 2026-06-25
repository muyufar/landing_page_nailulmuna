<?php
/**
 * Migrasi v3: Jadwal terima tamu (informasi rekap)
 */
require __DIR__ . '/../src/Database.php';

$db = Database::connect();

$db->exec("CREATE TABLE IF NOT EXISTS jadwal_terima_tamu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area ENUM('pesantren', 'ndalem') NOT NULL,
    hari TINYINT NOT NULL COMMENT '0=Minggu .. 6=Sabtu',
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    keterangan VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_area_hari (area, hari)
)");

$count = (int) $db->query('SELECT COUNT(*) FROM jadwal_terima_tamu')->fetchColumn();
if ($count === 0) {
    $defaults = [
        ['ndalem', 1, '08:00:00', '11:00:00', 'Sowan pagi'],
        ['ndalem', 1, '14:00:00', '16:00:00', 'Sowan sore'],
        ['ndalem', 4, '08:00:00', '11:00:00', 'Sowan pagi'],
        ['ndalem', 4, '14:00:00', '16:00:00', 'Sowan sore'],
        ['pesantren', 1, '07:00:00', '12:00:00', 'Kantor pagi'],
        ['pesantren', 1, '13:00:00', '15:00:00', 'Kantor sore'],
        ['pesantren', 2, '07:00:00', '12:00:00', 'Kantor pagi'],
        ['pesantren', 3, '07:00:00', '12:00:00', 'Kantor pagi'],
        ['pesantren', 4, '07:00:00', '12:00:00', 'Kantor pagi'],
        ['pesantren', 5, '07:00:00', '12:00:00', 'Kantor pagi'],
    ];
    $stmt = $db->prepare(
        'INSERT INTO jadwal_terima_tamu (area, hari, jam_mulai, jam_selesai, keterangan) VALUES (?, ?, ?, ?, ?)'
    );
    foreach ($defaults as $row) {
        $stmt->execute($row);
    }
    echo "Seed jadwal default ditambahkan.\n";
}

echo "Migrasi v3 selesai.\n";
