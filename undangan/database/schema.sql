-- Haflah Multi-Event Undangan Digital
-- Jalankan via phpMyAdmin atau: mysql -u root < database/schema.sql

CREATE DATABASE IF NOT EXISTS haflah_undangan
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE haflah_undangan;

CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('super_admin', 'panitia') NOT NULL DEFAULT 'panitia',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  slug             VARCHAR(100) NOT NULL UNIQUE,
  title            VARCHAR(150) NOT NULL,
  pesantren_name   VARCHAR(150) NOT NULL DEFAULT '',
  theme_statement  TEXT,
  invitation_greeting VARCHAR(150) NOT NULL DEFAULT 'Kepada Yth. Bapak/Ibu/Saudara/i',
  mukaddimah       TEXT,
  quran_quote      TEXT,
  date_masehi      VARCHAR(100) DEFAULT '',
  date_hijriah     VARCHAR(100) DEFAULT '',
  event_time       VARCHAR(100) DEFAULT '',
  countdown_target DATETIME DEFAULT NULL,
  location_name    VARCHAR(150) DEFAULT '',
  location_address TEXT,
  maps_url         TEXT,
  speaker_name     VARCHAR(150) DEFAULT '',
  speaker_origin   VARCHAR(150) DEFAULT '',
  dresscode_pria   VARCHAR(255) DEFAULT '',
  dresscode_wanita VARCHAR(255) DEFAULT '',
  special_rules    TEXT,
  event_schedule   TEXT,
  color_primary    VARCHAR(7)  NOT NULL DEFAULT '#064E3B',
  color_accent     VARCHAR(7)  NOT NULL DEFAULT '#D4AF37',
  theme_preset     VARCHAR(30) NOT NULL DEFAULT 'hijau_emas',
  font_preset      VARCHAR(30) NOT NULL DEFAULT 'klasik',
  font_custom_title TEXT,
  font_custom_body  TEXT,
  logo_pesantren   TEXT,
  logo_haflah      TEXT,
  ornament_top     TEXT,
  ornament_divider TEXT,
  ornament_bottom  TEXT,
  bg_image         TEXT,
  auto_scroll      TINYINT(1) NOT NULL DEFAULT 1,
  scroll_interval  INT NOT NULL DEFAULT 5,
  scroll_speed     INT NOT NULL DEFAULT 800,
  scroll_snap      TINYINT(1) NOT NULL DEFAULT 1,
  animated_ornaments TINYINT(1) NOT NULL DEFAULT 1,
  ornament_animation VARCHAR(30) NOT NULL DEFAULT 'melayang',
  section_animations TEXT NULL,
  invitation_pages TEXT NULL,
  audio_mode       ENUM('synth', 'url') NOT NULL DEFAULT 'synth',
  audio_url        TEXT,
  seat_capacity    INT DEFAULT 500,
  status           ENUM('aktif', 'arsip') NOT NULL DEFAULT 'aktif',
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS guestbook_rsvp (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  event_id      INT NOT NULL,
  guest_name    VARCHAR(150) NOT NULL,
  status        ENUM('hadir', 'absen') NOT NULL DEFAULT 'hadir',
  pax_count     INT NOT NULL DEFAULT 1,
  greeting_note TEXT,
  submitted_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  INDEX idx_event_id (event_id)
) ENGINE=InnoDB;

-- Akun default: admin / admin123 (ganti setelah login pertama)
INSERT INTO users (username, password_hash, role) VALUES
  ('admin', '$2y$10$iYpoy8ff1yiAyROJP1PR.OxqWHeA02BtZlCGK5LOzJzK5XXjAYgkG', 'super_admin');

-- Contoh undangan demo
INSERT INTO events (
  slug, title, pesantren_name, theme_statement, mukaddimah, quran_quote,
  date_masehi, date_hijriah, event_time, countdown_target,
  location_name, location_address, maps_url,
  speaker_name, speaker_origin,
  dresscode_pria, dresscode_wanita, special_rules,
  color_primary, color_accent, audio_mode, status
) VALUES (
  'haflah-2026',
  'Haflah Akhirussanah 2026',
  'Pondok Pesantren Al-Ikhlas',
  'Menjadi Generasi Qur''ani yang Berakhlak Mulia',
  'Dengan memohon rahmat dan ridha Allah Subhanahu wa Ta''ala, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk hadir dalam acara Haflah Akhirussanah tahun ajaran 2025/2026.',
  'وَمَن يَتَّقِ اللَّهَ يَجْعَل لَّهُ مَخْرَجًا',
  'Ahad, 15 Juni 2026',
  '28 Dzulqa''dah 1447 H',
  '08.00 WIB – selesai',
  '2026-06-15 08:00:00',
  'Aula Utama Pesantren',
  'Jl. Pesantren No. 1, Kecamatan Sukamaju, Jawa Barat',
  'https://maps.google.com',
  'KH. Ahmad Fauzi, Lc.',
  'Pondok Pesantren Al-Ikhlas',
  'Baju koko/sarung batik, peci hitam',
  'Gamis/kerudung syar''i, warna sopan',
  'Mohon datang tepat waktu. Anak-anak di bawah 5 tahun tidak diperkenankan masuk aula utama.',
  '#064E3B', '#D4AF37', 'synth', 'aktif'
);
