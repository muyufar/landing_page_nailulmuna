<?php

class JadwalTerimaTamuModel
{
    private PDO $db;

    public const HARI = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll(?string $area = null): array
    {
        if ($area) {
            $stmt = $this->db->prepare(
                'SELECT * FROM jadwal_terima_tamu WHERE area = ? ORDER BY hari ASC, jam_mulai ASC'
            );
            $stmt->execute([$area]);
        } else {
            $stmt = $this->db->query(
                'SELECT * FROM jadwal_terima_tamu ORDER BY area ASC, hari ASC, jam_mulai ASC'
            );
        }
        return $stmt->fetchAll();
    }

    public function getActiveByArea(string $area): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM jadwal_terima_tamu WHERE area = ? AND is_active = 1
             ORDER BY hari ASC, jam_mulai ASC'
        );
        $stmt->execute([$area]);
        return $stmt->fetchAll();
    }

    /** Grouped by day for display */
    public function getGroupedByArea(string $area): array
    {
        $rows = $this->getActiveByArea($area);
        $grouped = [];
        foreach ($rows as $row) {
            $hari = (int) $row['hari'];
            if (!isset($grouped[$hari])) {
                $grouped[$hari] = [];
            }
            $grouped[$hari][] = $row;
        }
        ksort($grouped);
        return $grouped;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM jadwal_terima_tamu WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO jadwal_terima_tamu (area, hari, jam_mulai, jam_selesai, keterangan, is_active)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['area'],
            (int) $data['hari'],
            $data['jam_mulai'],
            $data['jam_selesai'],
            $data['keterangan'] ?? null,
            !empty($data['is_active']) ? 1 : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE jadwal_terima_tamu SET area = ?, hari = ?, jam_mulai = ?, jam_selesai = ?,
             keterangan = ?, is_active = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['area'],
            (int) $data['hari'],
            $data['jam_mulai'],
            $data['jam_selesai'],
            $data['keterangan'] ?? null,
            !empty($data['is_active']) ? 1 : 0,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM jadwal_terima_tamu WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function hariLabel(int $hari): string
    {
        return self::HARI[$hari] ?? (string) $hari;
    }

    public function formatSlot(array $row): string
    {
        $mulai = substr($row['jam_mulai'], 0, 5);
        $selesai = substr($row['jam_selesai'], 0, 5);
        $text = "{$mulai} – {$selesai}";
        if (!empty($row['keterangan'])) {
            $text .= ' (' . $row['keterangan'] . ')';
        }
        return $text;
    }
}
