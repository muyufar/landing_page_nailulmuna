<?php

class SettingsModel
{
    private PDO $db;
    private static array $cache = [];

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function get(string $key, string $default = ''): string
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        $stmt = $this->db->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        self::$cache[$key] = $val !== false ? (string) $val : $default;
        return self::$cache[$key];
    }

    public function set(string $key, string $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        $stmt->execute([$key, $value]);
        self::$cache[$key] = $value;
    }

    public function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            $this->set($key, (string) $value);
        }
    }

    public function isTruthy(string $key): bool
    {
        return in_array($this->get($key, '0'), ['1', 'true', 'yes'], true);
    }

    public function getWhatsAppConfig(): array
    {
        return [
            'enabled' => $this->isTruthy('whatsapp_enabled'),
            'provider' => $this->get('whatsapp_provider', 'fonnte'),
            'token' => $this->get('whatsapp_token'),
            'fonnte_url' => 'https://api.fonnte.com/send',
            'wablas_url' => 'https://wablas.com/api/send-message',
        ];
    }

    public function getStaffRecipients(): array
    {
        return [
            'pengasuh' => [
                'phone' => $this->get('wa_phone_pengasuh'),
                'enabled' => $this->isTruthy('wa_enabled_pengasuh'),
                'on_register' => $this->isTruthy('wa_on_register_pengasuh'),
                'on_checkin' => $this->isTruthy('wa_on_checkin_pengasuh'),
                'on_jadwal' => $this->isTruthy('wa_on_jadwal_pengasuh'),
            ],
            'ndalem' => [
                'phone' => $this->get('wa_phone_ndalem'),
                'enabled' => $this->isTruthy('wa_enabled_ndalem'),
                'on_register' => $this->isTruthy('wa_on_register_ndalem'),
                'on_checkin' => $this->isTruthy('wa_on_checkin_ndalem'),
                'on_jadwal' => $this->isTruthy('wa_on_jadwal_ndalem'),
            ],
            'kantor' => [
                'phone' => $this->get('wa_phone_kantor'),
                'enabled' => $this->isTruthy('wa_enabled_kantor'),
                'on_register' => $this->isTruthy('wa_on_register_kantor'),
                'on_checkin' => $this->isTruthy('wa_on_checkin_kantor'),
                'on_jadwal' => $this->isTruthy('wa_on_jadwal_kantor'),
            ],
        ];
    }

    public function getAllForAdmin(): array
    {
        $keys = [
            'pesantren_name', 'pesantren_address', 'pesantren_logo',
            'whatsapp_enabled', 'whatsapp_provider', 'whatsapp_token',
            'wa_phone_pengasuh', 'wa_phone_ndalem', 'wa_phone_kantor',
            'wa_enabled_pengasuh', 'wa_enabled_ndalem', 'wa_enabled_kantor',
            'wa_on_register_pengasuh', 'wa_on_register_ndalem', 'wa_on_register_kantor',
            'wa_on_checkin_pengasuh', 'wa_on_checkin_ndalem', 'wa_on_checkin_kantor',
            'wa_on_jadwal_pengasuh', 'wa_on_jadwal_ndalem', 'wa_on_jadwal_kantor',
            'wa_on_approve_guest', 'wa_on_register_guest', 'wa_jadwal_reminder_minutes', 'ndalem_ruang',
        ];
        $out = [];
        foreach ($keys as $k) {
            $out[$k] = $this->get($k);
        }
        return $out;
    }

    public function getIdentitySettings(): array
    {
        $app = require __DIR__ . '/../config/app.php';
        return [
            'pesantren_name' => $this->get('pesantren_name', $app['pesantren_name']),
            'pesantren_address' => $this->get('pesantren_address', $app['pesantren_address']),
            'pesantren_logo' => $this->get('pesantren_logo', ''),
        ];
    }
}
