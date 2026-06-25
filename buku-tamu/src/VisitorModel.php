<?php

class VisitorModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getNextQueueNumber(): int
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $stmt->execute(['daily_queue_date']);
        $storedDate = $stmt->fetchColumn();

        if ($storedDate !== $today) {
            $this->db->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?')->execute([$today, 'daily_queue_date']);
            $this->db->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?')->execute(['0', 'daily_queue_counter']);
            return 1;
        }

        $stmt->execute(['daily_queue_counter']);
        $counter = (int) $stmt->fetchColumn();
        $next = $counter + 1;
        $this->db->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?')->execute([(string) $next, 'daily_queue_counter']);
        return $next;
    }

    public function generateTicketCode(): string
    {
        return strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
    }

    public function create(array $data): array
    {
        $queueNumber = $this->getNextQueueNumber();
        $ticketCode = $this->generateTicketCode();
        $hijri = HijriDate::toHijri();
        $area = $data['area_masuk'] ?? area_masuk_from_tujuan($data['tujuan_kunjungan']);
        $jenis = $data['jenis_kedatangan'] ?? 'sekarang';
        $jadwal = ($jenis === 'jadwal' && !empty($data['jadwal_kunjungan'])) ? $data['jadwal_kunjungan'] : null;

        $stmt = $this->db->prepare(
            'INSERT INTO visitors (ticket_code, queue_number, nama_lengkap, no_hp, asal, jumlah_rombongan,
             tujuan_kunjungan, detail_keperluan, nama_santri, foto_path, jenis_kedatangan, jadwal_kunjungan,
             area_masuk, hijri_date, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $ticketCode,
            $queueNumber,
            $data['nama_lengkap'],
            $data['no_hp'],
            $data['asal'],
            $data['jumlah_rombongan'],
            $data['tujuan_kunjungan'],
            $data['detail_keperluan'] ?? '',
            $data['nama_santri'] ?? null,
            $data['foto_path'] ?? null,
            $jenis,
            $jadwal,
            $area,
            $hijri,
            'pending',
        ]);

        return $this->findById((int) $this->db->lastInsertId());
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM visitors WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByTicket(string $code): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM visitors WHERE ticket_code = ?');
        $stmt->execute([$code]);
        return $stmt->fetch() ?: null;
    }

    public function getActiveInside(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM visitors WHERE status IN ('checked_in', 'in_queue', 'approved', 'called')
             ORDER BY checked_in_at DESC, created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getTodayVisitors(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM visitors WHERE DATE(created_at) = CURDATE() ORDER BY created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getSowanQueue(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM visitors WHERE area_masuk = 'ndalem'
             AND status IN ('checked_in', 'in_queue', 'approved', 'called')
             AND (
                jenis_kedatangan = 'sekarang' AND DATE(COALESCE(checked_in_at, created_at)) = CURDATE()
                OR jenis_kedatangan = 'jadwal' AND DATE(jadwal_kunjungan) <= CURDATE()
             )
             ORDER BY queue_number ASC"
        );
        return $stmt->fetchAll();
    }

    /** Tamu yang akan masuk ndalem (sekarang hari ini + jadwal mendatang) */
    public function getNdalemUpcoming(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM visitors
             WHERE area_masuk = 'ndalem'
             AND status IN ('pending', 'checked_in', 'in_queue', 'approved')
             AND (
                (jenis_kedatangan = 'sekarang' AND DATE(created_at) = CURDATE())
                OR (jenis_kedatangan = 'jadwal' AND DATE(jadwal_kunjungan) >= CURDATE())
             )
             ORDER BY
                CASE WHEN jenis_kedatangan = 'jadwal' THEN jadwal_kunjungan ELSE COALESCE(checked_in_at, created_at) END ASC,
                queue_number ASC"
        );
        return $stmt->fetchAll();
    }

    /** Tamu yang sudah masuk ndalem */
    public function getNdalemEntered(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM visitors
             WHERE area_masuk = 'ndalem'
             AND status IN ('called', 'completed')
             AND (
                (jenis_kedatangan = 'sekarang' AND DATE(COALESCE(checked_in_at, created_at)) = CURDATE())
                OR (jenis_kedatangan = 'jadwal' AND DATE(jadwal_kunjungan) >= CURDATE())
             )
             ORDER BY CASE status WHEN 'called' THEN 0 ELSE 1 END, approved_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getNdalemRecapSummary(): array
    {
        $stmt = $this->db->query(
            "SELECT
                SUM(CASE WHEN status IN ('pending','checked_in','in_queue','approved') THEN 1 ELSE 0 END) AS upcoming,
                SUM(CASE WHEN status = 'called' THEN 1 ELSE 0 END) AS in_meeting,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN status IN ('called','completed') THEN 1 ELSE 0 END) AS entered,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
                SUM(CASE WHEN jenis_kedatangan = 'jadwal' AND status = 'pending' THEN 1 ELSE 0 END) AS jadwal_mendatang,
                COUNT(*) AS total
             FROM visitors
             WHERE area_masuk = 'ndalem'
             AND (
                (jenis_kedatangan = 'sekarang' AND DATE(created_at) = CURDATE())
                OR (jenis_kedatangan = 'jadwal' AND DATE(jadwal_kunjungan) >= CURDATE())
             )"
        );
        $row = $stmt->fetch() ?: [];
        return [
            'upcoming' => (int) ($row['upcoming'] ?? 0),
            'in_meeting' => (int) ($row['in_meeting'] ?? 0),
            'completed' => (int) ($row['completed'] ?? 0),
            'entered' => (int) ($row['entered'] ?? 0),
            'rejected' => (int) ($row['rejected'] ?? 0),
            'jadwal_mendatang' => (int) ($row['jadwal_mendatang'] ?? 0),
            'total' => (int) ($row['total'] ?? 0),
        ];
    }

    public function getDueScheduledReminders(int $minutesAhead): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM visitors
             WHERE jenis_kedatangan = 'jadwal'
             AND jadwal_wa_notified = 0
             AND status = 'pending'
             AND jadwal_kunjungan IS NOT NULL
             AND jadwal_kunjungan BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? MINUTE)
             ORDER BY jadwal_kunjungan ASC"
        );
        $stmt->execute([$minutesAhead]);
        return $stmt->fetchAll();
    }

    public function markJadwalWaNotified(int $id): void
    {
        $this->db->prepare('UPDATE visitors SET jadwal_wa_notified = 1 WHERE id = ?')->execute([$id]);
    }

    /** @deprecated use getNdalemUpcoming */
    public function getSowanUpcomingToday(): array { return $this->getNdalemUpcoming(); }

    /** @deprecated use getNdalemEntered */
    public function getSowanEnteredToday(): array { return $this->getNdalemEntered(); }

    /** @deprecated use getNdalemRecapSummary */
    public function getSowanRecapSummary(): array { return $this->getNdalemRecapSummary(); }

    public function updateStatus(int $id, string $status, ?int $userId = null): bool
    {
        $fields = ['status = ?'];
        $params = [$status];

        if ($status === 'checked_in') {
            $fields[] = 'checked_in_at = NOW()';
        } elseif ($status === 'checked_out') {
            $fields[] = 'checked_out_at = NOW()';
        } elseif ($status === 'approved') {
            $fields[] = 'approved_at = NOW()';
            $fields[] = 'approved_by = ?';
            $params[] = $userId;
        }

        $params[] = $id;
        $sql = 'UPDATE visitors SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function setWaktuTemui(int $id, string $waktuTemui): bool
    {
        $stmt = $this->db->prepare('UPDATE visitors SET waktu_temu = ? WHERE id = ?');
        return $stmt->execute([$waktuTemui, $id]);
    }

    public function getStatistics(string $period = 'week'): array
    {
        if ($period === 'month') {
            $stmt = $this->db->query(
                "SELECT DATE(created_at) as tanggal, COUNT(*) as total
                 FROM visitors WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                 GROUP BY DATE(created_at) ORDER BY tanggal"
            );
        } else {
            $stmt = $this->db->query(
                "SELECT DATE(created_at) as tanggal, COUNT(*) as total
                 FROM visitors WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                 GROUP BY DATE(created_at) ORDER BY tanggal"
            );
        }
        $daily = $stmt->fetchAll();

        $stmt = $this->db->query(
            "SELECT tujuan_kunjungan, COUNT(*) as total FROM visitors
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY tujuan_kunjungan"
        );
        $byPurpose = $stmt->fetchAll();

        $stmt = $this->db->query('SELECT COUNT(*) FROM visitors WHERE DATE(created_at) = CURDATE()');
        $todayTotal = (int) $stmt->fetchColumn();

        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM visitors WHERE status IN ('checked_in','in_queue','approved','called')"
        );
        $currentlyInside = (int) $stmt->fetchColumn();

        return [
            'daily' => $daily,
            'by_purpose' => $byPurpose,
            'today_total' => $todayTotal,
            'currently_inside' => $currentlyInside,
        ];
    }
}
