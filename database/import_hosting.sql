-- IMPORT SATU DATABASE (Hosting) -- u700125577_santri
-- Landing page + Buku Tamu + Undangan (bt_users, inv_users)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `u700125577_santri`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `u700125577_santri`;

-- ############################################################
-- DATABASE 1: landing_page
-- ############################################################

DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `footer_links`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `gallery`;
DROP TABLE IF EXISTS `testimonials`;
DROP TABLE IF EXISTS `stats`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `nav_items`;
DROP TABLE IF EXISTS `site_settings`;

CREATE TABLE `site_settings` (
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` TEXT NOT NULL,
    PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nav_items` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(100) NOT NULL,
    `url_hash` VARCHAR(100) NOT NULL DEFAULT '#',
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `programs` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    `icon_class` VARCHAR(100) NOT NULL DEFAULT 'bi-book',
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stats` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `value_text` VARCHAR(50) NOT NULL,
    `label` VARCHAR(150) NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `testimonials` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `role_label` VARCHAR(150) NOT NULL,
    `quote_text` TEXT NOT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gallery` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `gallery_type` ENUM('prestasi','daily','fasilitas') NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `articles` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `excerpt` TEXT NOT NULL,
    `category` ENUM('tausiyah','berita') NOT NULL DEFAULT 'berita',
    `image_path` VARCHAR(500) DEFAULT NULL,
    `link_url` VARCHAR(500) DEFAULT '#',
    `published_at` DATE DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `footer_links` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(150) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admins` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL DEFAULT 'Administrator',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'A.P.I Nailul Muna'),
('site_logo', 'assets/images/logo-nme.png'),
('navbar_logo_size', '58'),
('navbar_brand_font_size', '1.35'),
('hero_tagline_font_size', '2.75'),
('section_title_font_size', '2'),
('nav_link_font_size', '0.95'),
('gallery_thumb_height', '130'),
('gallery_fasilitas_height', '200'),
('blog_image_height', '200'),
('muassis_photo_max_width', '100'),
('section_fasilitas_title', 'Sarana Pendukung Pendidikan'),
('section_fasilitas_subtitle', 'Fasilitas Pesantren'),
('site_tagline', 'Pondok Pesantren Salafiyah Syafi''iyah'),
('hero_tagline', 'Mencetak Generasi Tafaqquh Fiddin, Berakhlakul Karimah, dan Melek Teknologi.'),
('hero_bg_image', 'https://images.unsplash.com/photo-1564760055775-d63b17a55c44?auto=format&fit=crop&w=1920&q=80'),
('hero_cta_text', 'Mulai Penjelajahan'),
('hero_cta_link', '#profil'),
('portal_url', 'http://si.pesantren.local/login.php'),
('psb_url', '#'),
('psb_button_text', 'Daftar Santri Baru (PSB)'),
('portal_button_text', 'Portal Masuk'),
('muassis_name', 'K.H. Ahmad Nailul Muna'),
('muassis_title', 'Muassis & Pengasuh'),
('muassis_image', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=800&q=80'),
('muassis_bio', 'Beliau adalah tokoh ulama yang mengabdikan hidupnya untuk dakwah dan pendidikan Islam generasi muda. Dengan sanad keilmuan yang kuat di bidang tafsir, hadits, dan fiqh, beliau mendirikan API Nailul Muna sebagai wadah mencetak santri yang menguasai ilmu agama klasik (kitab kuning) sekaligus mampu beradaptasi dengan perkembangan zaman melalui literasi digital.'),
('muassis_sanad', 'Sanad keilmuan: Studi di pesantren-pesantren besar Jawa dan pernah mendalami ilmu di Timur Tengah. Masyhur sebagai pengajar kitab kuning dan pembina tahfidz.'),
('muassis_vision', 'Cita-cita awal: Membentuk generasi muslim yang tafaqquh fiddin, berakhlak mulia, mandiri, dan siap berkhidmah untuk umat.'),
('muassis_quote', 'Wasiatku kepada para santri: Jadikan ilmu sebagai cahaya, dan adab sebagai pedoman. Tanpa adab, ilmu menjadi bumerang. Tanpa ilmu, adab tidak berbobot. Kuatkan hafalanmu, kuatkan hatimu, dan jangan pernah malu menjadi santri yang taat dan cerdas.'),
('section_muassis_title', 'Jejak Langkah Muassis'),
('section_muassis_subtitle', 'The Roots & Legacy'),
('section_stories_title', 'Wajah Santri & Prestasi'),
('section_stories_subtitle', 'The Success Stories'),
('section_programs_title', 'Program Unggulan'),
('section_programs_subtitle', 'Kurikulum terpadu: Ilmu Diniyah, Tahfidz, dan Kemandirian Digital'),
('section_stats_title', 'Angka Bicara'),
('section_blog_title', 'Blog Tausiyah & Berita'),
('section_blog_subtitle', 'Kajian, tausiyah masyayikh, dan liputan kegiatan pesantren'),
('footer_address', 'Jl. Pesantren No. 1, Desa Muna, Kecamatan Ilmu, Jawa Timur 61234'),
('footer_phone', '+62 812-3456-7890'),
('footer_email', 'info@apinailulmuna.sch.id'),
('footer_whatsapp', 'https://wa.me/6281234567890'),
('footer_facebook', '#'),
('footer_instagram', '#'),
('footer_youtube', '#'),
('footer_copyright', '© 2026 API Nailul Muna. Semua hak cipta dilindungi.'),
('show_buku_tamu', '0'),
('show_undangan', '0'),
('section_apps_title', 'Layanan Digital'),
('section_apps_subtitle', 'Akses cepat ke aplikasi pesantren'),
('buku_tamu_button_text', 'Buku Tamu Digital'),
('undangan_button_text', 'Undangan Digital'),
('buku_tamu_desc', 'Isi buku tamu digital untuk tamu pesantren.'),
('undangan_desc', 'Undangan digital acara haflah dan kegiatan pesantren.'),
('admin_login_url', 'admin/login.php'),
('admin_login_text', 'Back Office');

INSERT INTO `nav_items` (`label`, `url_hash`, `sort_order`) VALUES
('Profil', '#profil', 1),
('Program Pendidikan', '#program', 2),
('Fasilitas', '#fasilitas', 3),
('Berita/Tausiyah', '#berita', 4),
('Alumni', '#alumni', 5);

INSERT INTO `programs` (`title`, `description`, `icon_class`, `sort_order`) VALUES
('Tahfidzul Qur''an', 'Program hafalan mutqin dengan metode talaqqi, muroja''ah terjadwal, dan sanad tahfidz yang terjaga kualitasnya.', 'bi-journal-bookmark-fill', 1),
('Kajian Kitab Kuning', 'Kurikulum diniyah formal berbasis kitab kuning klasik (PDF) dengan sistem evaluasi sorogan dan bandongan.', 'bi-book-half', 2),
('Kemandirian Digital', 'Pelatihan IT, manajemen data paperless, robotik dasar, dan literasi teknologi untuk santri modern.', 'bi-laptop', 3);

INSERT INTO `stats` (`value_text`, `label`, `sort_order`) VALUES
('125+', 'Santri Aktif', 1),
('500+', 'Alumni', 2),
('15+', 'Mudarris', 3),
('1998', 'Tahun Berdiri', 4);

INSERT INTO `testimonials` (`name`, `role_label`, `quote_text`, `sort_order`) VALUES
('Ahmad Fauzi', 'Santri Kelas 3 Diniyah', 'Sistem paperless di pesantren memudahkan saya melacak progress hafalan dan tugas kitab. Guru-guru sangat perhatian dalam muroja''ah harian.', 1),
('Fatimah Azzahra', 'Santri Tahfidz', 'Metode hafalan di sini terstruktur dan menyenangkan. Alhamdulillah sudah mencapai juz 15 dengan mutqin.', 2),
('Umar bin Khattab', 'Santri IT Club', 'Kami belajar ngaji pagi, kitab siang, dan coding sore. Pesantren ini benar-benar merangkai tradisi dan teknologi.', 3);

INSERT INTO `gallery` (`gallery_type`, `title`, `image_path`, `sort_order`) VALUES
('prestasi', 'Juara Musabaqah Kitab', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=600&q=80', 1),
('prestasi', 'Tim Robotik Santri', 'https://images.unsplash.com/photo-1517694712202-14dd953757aa?auto=format&fit=crop&w=600&q=80', 2),
('prestasi', 'Lomba Tahfidz', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0e?auto=format&fit=crop&w=600&q=80', 3),
('daily', 'Belajar Bersama', 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&w=600&q=80', 1),
('daily', 'Makan Bersama', 'https://images.unsplash.com/photo-1529390079861-591de354faf5?auto=format&fit=crop&w=600&q=80', 2),
('daily', 'Gotong Royong', 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=600&q=80', 3),
('fasilitas', 'Masjid & Musholla', 'https://images.unsplash.com/photo-1564760055775-d63b17a55c44?auto=format&fit=crop&w=600&q=80', 1),
('fasilitas', 'Perpustakaan Kitab Kuning', 'https://images.unsplash.com/photo-1521587760476-6c122a7fcee1?auto=format&fit=crop&w=600&q=80', 2),
('fasilitas', 'Laboratorium Komputer', 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=600&q=80', 3),
('fasilitas', 'Asrama Santri', 'https://images.unsplash.com/photo-1555854877-25aa03b922f7?auto=format&fit=crop&w=600&q=80', 4),
('fasilitas', 'Ruang Tahfidz', 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=600&q=80', 5),
('fasilitas', 'Lapangan Olahraga', 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=600&q=80', 6);

INSERT INTO `articles` (`title`, `excerpt`, `category`, `image_path`, `link_url`, `published_at`) VALUES
('Adab Menuntut Ilmu di Zaman Digital', 'Tausiyah singkat tentang menjaga adab guru dan santri ketika belajar memakai perangkat digital di pesantren.', 'tausiyah', 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=800&q=80', '#', '2026-05-15'),
('Peringatan Maulid Nabi di Pesantren', 'Liputan kegiatan sholawat, tausiyah, dan pameran karya santri dalam rangka Maulid Nabi Muhammad SAW.', 'berita', 'https://images.unsplash.com/photo-1542816417-0983c9c9adbc?auto=format&fit=crop&w=800&q=80', '#', '2026-05-01'),
('Tips Muroja''ah Hafalan Efektif', 'Kajian praktis dari mudarris tahfidz tentang teknik muroja''ah pagi dan malam agar hafalan tetap mutqin.', 'tausiyah', 'https://images.unsplash.com/photo-1591604466377-1a63d39d7a98?auto=format&fit=crop&w=800&q=80', '#', '2026-04-20');

INSERT INTO `footer_links` (`label`, `url`, `sort_order`, `is_active`) VALUES
('Portal Wali Santri', '#', 1, 1),
('Dashboard Mudarris', '#', 2, 1),
('Panel Pengurus', '#', 3, 1);

INSERT INTO `admins` (`username`, `password_hash`, `full_name`) VALUES
('admin', '$2y$10$AMXBaaakghxeFIjWtHULNuu/5Fna2mDb/8JfC9YC8XIa14lKqUE7G', 'Administrator');


-- ############################################################
-- DATABASE 2: buku_tamu
-- ############################################################

DROP TABLE IF EXISTS `whatsapp_logs`;
DROP TABLE IF EXISTS `visitors`;
DROP TABLE IF EXISTS `jadwal_terima_tamu`;
DROP TABLE IF EXISTS `pengasuh_status`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `bt_users`;

CREATE TABLE `bt_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'ndalem') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pengasuh_status` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `status` ENUM('available', 'busy', 'closed') NOT NULL DEFAULT 'available',
    `message` VARCHAR(255) DEFAULT NULL,
    `updated_by` INT DEFAULT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`updated_by`) REFERENCES `bt_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `visitors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_code` VARCHAR(20) NOT NULL UNIQUE,
    `queue_number` INT NOT NULL,
    `nama_lengkap` VARCHAR(150) NOT NULL,
    `no_hp` VARCHAR(20) NOT NULL,
    `asal` VARCHAR(150) NOT NULL,
    `jumlah_rombongan` ENUM('1', '2-5', '>5') NOT NULL DEFAULT '1',
    `tujuan_kunjungan` ENUM('sowan', 'jenguk', 'administrasi', 'kerjasama', 'lainnya') NOT NULL,
    `detail_keperluan` TEXT,
    `nama_santri` VARCHAR(150) DEFAULT NULL,
    `foto_path` VARCHAR(255) DEFAULT NULL,
    `jenis_kedatangan` ENUM('sekarang', 'jadwal') NOT NULL DEFAULT 'sekarang',
    `jadwal_kunjungan` DATETIME DEFAULT NULL,
    `waktu_temu` DATETIME DEFAULT NULL,
    `area_masuk` ENUM('pesantren', 'ndalem') NOT NULL DEFAULT 'pesantren',
    `status` ENUM('pending', 'checked_in', 'in_queue', 'approved', 'called', 'completed', 'checked_out', 'rejected') NOT NULL DEFAULT 'pending',
    `hijri_date` VARCHAR(50) DEFAULT NULL,
    `checked_in_at` DATETIME DEFAULT NULL,
    `checked_out_at` DATETIME DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `approved_by` INT DEFAULT NULL,
    `whatsapp_sent` TINYINT(1) DEFAULT 0,
    `staff_wa_notified` TINYINT(1) DEFAULT 0,
    `jadwal_wa_notified` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_created` (`created_at`),
    INDEX `idx_tujuan` (`tujuan_kunjungan`),
    FOREIGN KEY (`approved_by`) REFERENCES `bt_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `whatsapp_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `visitor_id` INT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `recipient_type` VARCHAR(30) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    `response` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`visitor_id`) REFERENCES `visitors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jadwal_terima_tamu` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `area` ENUM('pesantren', 'ndalem') NOT NULL,
    `hari` TINYINT NOT NULL COMMENT '0=Minggu .. 6=Sabtu',
    `jam_mulai` TIME NOT NULL,
    `jam_selesai` TIME NOT NULL,
    `keterangan` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_area_hari` (`area`, `hari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
    `setting_key` VARCHAR(50) PRIMARY KEY,
    `setting_value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bt_users` (`username`, `password`, `name`, `role`) VALUES
('admin', '$2y$10$Y7Pv5Pv3tdjiFxtyEJXCR.YGQEsL4RQJPw7oXry360RzwvZzqstru', 'Petugas Keamanan', 'admin'),
('ndalem', '$2y$10$AXmi2nh7fUSamKu3uYR7Pu5PwBSkLcxHGhYYzUOXephWJ4bP8oo0S', 'Asisten Pengasuh', 'ndalem');

INSERT INTO `pengasuh_status` (`status`, `message`) VALUES
('available', 'Pengasuh sedang luang dan menerima tamu.');

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('pesantren_name', 'A.P.I Nailul Muna'),
('pesantren_address', 'Jl. Pesantren No. 1, Desa Muna, Kecamatan Ilmu, Jawa Timur'),
('whatsapp_provider', 'fonnte'),
('whatsapp_token', ''),
('whatsapp_enabled', '0'),
('ndalem_ruang', 'Ruang Tunggu Ndalem Barat'),
('daily_queue_counter', '0'),
('daily_queue_date', CURDATE()),
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


-- ############################################################
-- DATABASE 3: haflah_undangan
-- ############################################################

DROP TABLE IF EXISTS `guestbook_rsvp`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `inv_users`;

CREATE TABLE `inv_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('super_admin', 'panitia') NOT NULL DEFAULT 'panitia',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `title` VARCHAR(150) NOT NULL,
    `pesantren_name` VARCHAR(150) NOT NULL DEFAULT '',
    `theme_statement` TEXT,
    `invitation_greeting` VARCHAR(150) NOT NULL DEFAULT 'Kepada Yth. Bapak/Ibu/Saudara/i',
    `mukaddimah` TEXT,
    `quran_quote` TEXT,
    `date_masehi` VARCHAR(100) DEFAULT '',
    `date_hijriah` VARCHAR(100) DEFAULT '',
    `event_time` VARCHAR(100) DEFAULT '',
    `countdown_target` DATETIME DEFAULT NULL,
    `location_name` VARCHAR(150) DEFAULT '',
    `location_address` TEXT,
    `maps_url` TEXT,
    `speaker_name` VARCHAR(150) DEFAULT '',
    `speaker_origin` VARCHAR(150) DEFAULT '',
    `dresscode_pria` VARCHAR(255) DEFAULT '',
    `dresscode_wanita` VARCHAR(255) DEFAULT '',
    `special_rules` TEXT,
    `event_schedule` TEXT,
    `color_primary` VARCHAR(7) NOT NULL DEFAULT '#064E3B',
    `color_accent` VARCHAR(7) NOT NULL DEFAULT '#D4AF37',
    `theme_preset` VARCHAR(30) NOT NULL DEFAULT 'hijau_emas',
    `font_preset` VARCHAR(30) NOT NULL DEFAULT 'klasik',
    `font_custom_title` TEXT,
    `font_custom_body` TEXT,
    `logo_pesantren` TEXT,
    `logo_haflah` TEXT,
    `ornament_top` TEXT,
    `ornament_divider` TEXT,
    `ornament_bottom` TEXT,
    `bg_image` TEXT,
    `auto_scroll` TINYINT(1) NOT NULL DEFAULT 1,
    `scroll_interval` INT NOT NULL DEFAULT 5,
    `scroll_speed` INT NOT NULL DEFAULT 800,
    `scroll_snap` TINYINT(1) NOT NULL DEFAULT 1,
    `animated_ornaments` TINYINT(1) NOT NULL DEFAULT 1,
    `ornament_animation` VARCHAR(30) NOT NULL DEFAULT 'melayang',
    `section_animations` TEXT NULL,
    `invitation_pages` TEXT NULL,
    `audio_mode` ENUM('synth', 'url') NOT NULL DEFAULT 'synth',
    `audio_url` TEXT,
    `seat_capacity` INT DEFAULT 500,
    `status` ENUM('aktif', 'arsip') NOT NULL DEFAULT 'aktif',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `guestbook_rsvp` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `guest_name` VARCHAR(150) NOT NULL,
    `status` ENUM('hadir', 'absen') NOT NULL DEFAULT 'hadir',
    `pax_count` INT NOT NULL DEFAULT 1,
    `greeting_note` TEXT,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
    INDEX `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `inv_users` (`username`, `password_hash`, `role`) VALUES
('admin', '$2y$10$iYpoy8ff1yiAyROJP1PR.OxqWHeA02BtZlCGK5LOzJzK5XXjAYgkG', 'super_admin');

INSERT INTO `events` (
    `slug`, `title`, `pesantren_name`, `theme_statement`, `mukaddimah`, `quran_quote`,
    `date_masehi`, `date_hijriah`, `event_time`, `countdown_target`,
    `location_name`, `location_address`, `maps_url`,
    `speaker_name`, `speaker_origin`,
    `dresscode_pria`, `dresscode_wanita`, `special_rules`,
    `color_primary`, `color_accent`, `audio_mode`, `status`
) VALUES (
    'haflah-2026',
    'Haflah Akhirussanah 2026',
    'A.P.I Nailul Muna',
    'Menjadi Generasi Qur''ani yang Berakhlak Mulia',
    'Dengan memohon rahmat dan ridha Allah Subhanahu wa Ta''ala, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk hadir dalam acara Haflah Akhirussanah tahun ajaran 2025/2026.',
    'وَمَن يَتَّقِ اللَّهَ يَجْعَل لَّهُ مَخْرَجًا',
    'Ahad, 15 Juni 2026',
    '28 Dzulqa''dah 1447 H',
    '08.00 WIB – selesai',
    '2026-06-15 08:00:00',
    'Aula Utama Pesantren',
    'Jl. Pesantren No. 1, Desa Muna, Kecamatan Ilmu, Jawa Timur',
    'https://maps.google.com',
    'K.H. Ahmad Nailul Muna',
    'A.P.I Nailul Muna',
    'Baju koko/sarung batik, peci hitam',
    'Gamis/kerudung syar''i, warna sopan',
    'Mohon datang tepat waktu. Anak-anak di bawah 5 tahun tidak diperkenankan masuk aula utama.',
    '#064E3B', '#D4AF37', 'synth', 'aktif'
);

SET FOREIGN_KEY_CHECKS = 1;
