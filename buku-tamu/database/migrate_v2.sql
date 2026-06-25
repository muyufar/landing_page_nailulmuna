-- Migrasi v2: Jadwal kunjungan + Notifikasi WhatsApp staff
USE buku_tamu;

ALTER TABLE visitors
    ADD COLUMN IF NOT EXISTS jenis_kedatangan ENUM('sekarang', 'jadwal') NOT NULL DEFAULT 'sekarang' AFTER foto_path,
    ADD COLUMN IF NOT EXISTS jadwal_kunjungan DATETIME NULL AFTER jenis_kedatangan,
    ADD COLUMN IF NOT EXISTS area_masuk ENUM('pesantren', 'ndalem') NOT NULL DEFAULT 'pesantren' AFTER jadwal_kunjungan,
    ADD COLUMN IF NOT EXISTS staff_wa_notified TINYINT(1) DEFAULT 0 AFTER whatsapp_sent,
    ADD COLUMN IF NOT EXISTS jadwal_wa_notified TINYINT(1) DEFAULT 0 AFTER staff_wa_notified;

UPDATE visitors SET area_masuk = 'ndalem' WHERE tujuan_kunjungan = 'sowan' AND area_masuk = 'pesantren';

ALTER TABLE whatsapp_logs
    MODIFY visitor_id INT NULL,
    ADD COLUMN IF NOT EXISTS recipient_type VARCHAR(30) NULL AFTER phone;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('whatsapp_enabled', '0'),
('whatsapp_provider', 'fonnte'),
('whatsapp_token', ''),
('wa_phone_pengasuh', ''),
('wa_phone_ndalem', ''),
('wa_phone_kantor', ''),
('wa_enabled_pengasuh', '1'),
('wa_enabled_ndalem', '1'),
('wa_enabled_kantor', '1'),
('wa_on_register_pengasuh', '1'),
('wa_on_register_ndalem', '1'),
('wa_on_register_kantor', '1'),
('wa_on_checkin_pengasuh', '1'),
('wa_on_checkin_ndalem', '1'),
('wa_on_checkin_kantor', '1'),
('wa_on_jadwal_pengasuh', '1'),
('wa_on_jadwal_ndalem', '1'),
('wa_on_jadwal_kantor', '1'),
('wa_on_approve_guest', '1'),
('wa_jadwal_reminder_minutes', '60');
