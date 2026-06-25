<?php

class WhatsAppService
{
    private SettingsModel $settings;
    private array $config;

    public function __construct(?SettingsModel $settings = null)
    {
        $this->settings = $settings ?? new SettingsModel();
        $this->config = $this->settings->getWhatsAppConfig();
    }

    public function isEnabled(): bool
    {
        return !empty($this->config['enabled']) && !empty($this->config['token']);
    }

    public function send(string $phone, string $message, ?int $visitorId = null, ?string $recipientType = null): bool
    {
        $phone = $this->normalizePhone($phone);
        if ($phone === '') {
            return false;
        }

        if (!$this->isEnabled()) {
            $this->log($visitorId, $phone, $message, 'failed', 'WhatsApp disabled or token missing', $recipientType);
            return false;
        }

        $provider = $this->config['provider'] ?? 'fonnte';
        $response = $provider === 'wablas'
            ? $this->sendViaWablas($phone, $message)
            : $this->sendViaFonnte($phone, $message);

        $success = $response['success'] ?? false;
        $this->log($visitorId, $phone, $message, $success ? 'sent' : 'failed', $response['body'] ?? '', $recipientType);

        return $success;
    }

    /** Notifikasi ke tamu: jadwal temu ditetapkan petugas/pengasuh */
    public function notifyGuestWaktuTemui(array $visitor): bool
    {
        if (!$this->settings->isTruthy('wa_on_approve_guest')) {
            return false;
        }
        if (trim($visitor['no_hp'] ?? '') === '') {
            return false;
        }
        if (empty($visitor['waktu_temu'])) {
            return false;
        }

        $app = app_config();
        $antrean = str_pad((string) $visitor['queue_number'], 3, '0', STR_PAD_LEFT);
        $waktu = format_waktu_temu_display($visitor['waktu_temu']);
        $tujuan = tujuan_label($visitor['tujuan_kunjungan']);
        $lokasi = visitor_lokasi_temu($visitor);

        $lines = [
            '📅 *Jadwal Pertemuan*',
            "— {$app['pesantren_name']}",
            '',
            "Yth. {$visitor['nama_lengkap']},",
            '',
            'Anda akan ditemui petugas/pengasuh pada:',
            "🕐 *{$waktu}*",
            "Tujuan: {$tujuan}",
            "Lokasi: {$lokasi}",
            "Antrean: *#{$antrean}*",
            '',
            'Mohon hadir tepat waktu dan tunjukkan tiket/antrean ke petugas.',
            'Terima kasih.',
        ];

        return $this->send($visitor['no_hp'], implode("\n", $lines), (int) $visitor['id'], 'tamu');
    }

    /** @deprecated use notifyGuestWaktuTemui */
    public function notifyGuestApproval(array $visitor): bool
    {
        return $this->notifyGuestWaktuTemui($visitor);
    }

    /** Konfirmasi WA ke tamu segera setelah daftar */
    public function notifyGuestRegister(array $visitor): bool
    {
        if (!$this->settings->isTruthy('wa_on_register_guest')) {
            return false;
        }
        $app = app_config();
        $antrean = str_pad($visitor['queue_number'], 3, '0', STR_PAD_LEFT);
        $waktu = visitor_waktu_label($visitor);
        $tujuan = tujuan_label($visitor['tujuan_kunjungan']);

        $lines = [
            "✅ *Pendaftaran Buku Tamu Berhasil*",
            "— {$app['pesantren_name']}",
            '',
            "Yth. {$visitor['nama_lengkap']},",
            '',
            "Terima kasih. Data Anda telah tercatat:",
            "Antrean: *#{$antrean}*",
            "Kode Tiket: *{$visitor['ticket_code']}*",
            "Tujuan: {$tujuan}",
            "Waktu: {$waktu}",
            '',
            'Tunjukkan tiket / QR ke petugas keamanan saat tiba.',
            'Simpan pesan ini sebagai bukti pendaftaran.',
        ];

        return $this->send($visitor['no_hp'], implode("\n", $lines), (int) $visitor['id'], 'tamu');
    }

    /** Kirim semua notifikasi segera saat tamu daftar (blok sampai selesai) */
    public function dispatchOnRegister(array $visitor): array
    {
        $result = ['staff' => 0, 'guest' => false, 'jadwal' => 0];

        $result['staff'] = $this->notifyStaff('register', $visitor);
        $result['guest'] = $this->notifyGuestRegister($visitor);

        if (($visitor['jenis_kedatangan'] ?? '') === 'jadwal') {
            $result['jadwal'] = $this->notifyStaff('jadwal', $visitor);
        }

        $result['reminders'] = $this->processScheduledReminders();

        return $result;
    }

    /** Kirim notifikasi segera saat check-in */
    public function dispatchOnCheckin(array $visitor): array
    {
        return [
            'staff' => $this->notifyStaff('checkin', $visitor),
            'reminders' => $this->processScheduledReminders(),
        ];
    }

    /** Kirim notifikasi segera saat sowan disetujui + jadwal temu */
    public function dispatchOnApprove(array $visitor): bool
    {
        $this->processScheduledReminders();
        return $this->notifyGuestWaktuTemui($visitor);
    }

    /** Notifikasi staff saat tamu daftar / check-in / jadwal */
    public function notifyStaff(string $event, array $visitor): int
    {
        $recipients = $this->settings->getStaffRecipients();
        $area = $visitor['area_masuk'] ?? area_masuk_from_tujuan($visitor['tujuan_kunjungan']);
        $sent = 0;

        foreach ($this->resolveStaffTargets($recipients, $event, $area) as $role => $phone) {
            if ($this->send($phone, $this->buildStaffMessage($event, $visitor, $role), (int) $visitor['id'], $role)) {
                $sent++;
            }
        }

        if ($sent > 0 && in_array($event, ['register', 'checkin'], true)) {
            $db = Database::connect();
            $db->prepare('UPDATE visitors SET staff_wa_notified = 1 WHERE id = ?')->execute([(int) $visitor['id']]);
        }

        return $sent;
    }

    /** Kirim pengingat jadwal kunjungan mendatang */
    public function processScheduledReminders(): int
    {
        $minutes = max(5, (int) $this->settings->get('wa_jadwal_reminder_minutes', '60'));
        $visitorModel = new VisitorModel();
        $pending = $visitorModel->getDueScheduledReminders($minutes);
        $sent = 0;

        foreach ($pending as $visitor) {
            $count = $this->notifyStaff('jadwal', $visitor);
            if ($count > 0) {
                $visitorModel->markJadwalWaNotified((int) $visitor['id']);
                $sent += $count;
            }
        }

        return $sent;
    }

    private function resolveStaffTargets(array $recipients, string $event, string $area): array
    {
        $eventKey = match ($event) {
            'register' => 'on_register',
            'checkin' => 'on_checkin',
            'jadwal' => 'on_jadwal',
            default => null,
        };
        if (!$eventKey) {
            return [];
        }

        $targets = [];
        $rolesForArea = $area === 'ndalem' ? ['pengasuh', 'ndalem'] : ['kantor'];

        foreach ($rolesForArea as $role) {
            $cfg = $recipients[$role] ?? [];
            if (empty($cfg['enabled']) || empty($cfg[$eventKey]) || empty($cfg['phone'])) {
                continue;
            }
            $targets[$role] = $cfg['phone'];
        }

        return $targets;
    }

    private function buildStaffMessage(string $event, array $visitor, string $role): string
    {
        $app = app_config();
        $areaLabel = ($visitor['area_masuk'] ?? '') === 'ndalem' ? 'Ndalem (Sowan)' : 'Pesantren';
        $tujuan = tujuan_label($visitor['tujuan_kunjungan']);
        $waktu = visitor_waktu_label($visitor);
        $antrean = str_pad($visitor['queue_number'], 3, '0', STR_PAD_LEFT);

        $roleLabel = match ($role) {
            'pengasuh' => 'Pengasuh',
            'ndalem' => 'Asisten Ndalem',
            'kantor' => 'Petugas Kantor',
            default => 'Petugas',
        };

        $eventLabel = match ($event) {
            'register' => '📋 *Tamu Baru Mendaftar*',
            'checkin' => '🚪 *Tamu Masuk Pesantren*',
            'jadwal' => '⏰ *Pengingat Jadwal Kunjungan*',
            default => '📢 *Notifikasi Tamu*',
        };

        $lines = [
            $eventLabel,
            "— {$app['pesantren_name']}",
            '',
            "Yth. {$roleLabel},",
            '',
            "Nama: *{$visitor['nama_lengkap']}*",
            "Tujuan: {$tujuan}",
            "Area masuk: {$areaLabel}",
            "Waktu: {$waktu}",
            "Antrean: #{$antrean}",
            "Asal: {$visitor['asal']}",
            "Rombongan: " . ($app['rombongan_options'][$visitor['jumlah_rombongan']] ?? $visitor['jumlah_rombongan']),
            "HP Tamu: " . (trim($visitor['no_hp'] ?? '') !== '' ? $visitor['no_hp'] : '-'),
        ];

        if (!empty($visitor['detail_keperluan'])) {
            $lines[] = "Keperluan: {$visitor['detail_keperluan']}";
        }
        if ($event === 'checkin' && ($visitor['area_masuk'] ?? '') === 'ndalem') {
            $lines[] = '';
            $lines[] = '_Tamu siap diproses di dashboard Ndalem._';
        }
        if ($event === 'jadwal') {
            $lines[] = '';
            $lines[] = '_Tamu dijadwalkan masuk sekitar waktu di atas._';
        }

        return implode("\n", $lines);
    }

    private function sendViaFonnte(string $phone, string $message): array
    {
        return $this->curlPost($this->config['fonnte_url'], [
            'target' => $phone,
            'message' => $message,
        ], ['Authorization: ' . $this->config['token']]);
    }

    private function sendViaWablas(string $phone, string $message): array
    {
        $url = $this->config['wablas_url'] . '?token=' . urlencode($this->config['token']);
        return $this->curlPost($url, [
            'phone' => $phone,
            'message' => $message,
        ]);
    }

    private function curlPost(string $url, array $data, array $headers = []): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array_merge(['Content-Type: application/x-www-form-urlencoded'], $headers),
        ]);
        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'body' => $body ?: '',
            'http_code' => $httpCode,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if ($phone === '') {
            return '';
        }
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    private function log(?int $visitorId, string $phone, string $message, string $status, string $response, ?string $recipientType = null): void
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            'INSERT INTO whatsapp_logs (visitor_id, phone, recipient_type, message, status, response) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$visitorId, $phone, $recipientType, $message, $status, $response]);
    }
}
