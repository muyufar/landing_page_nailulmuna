-- ============================================================
-- Landing Page — Import Sekali Jalan (phpMyAdmin)
-- ============================================================
-- PENTING: Gunakan file gabungan terbaru:
--   database/import_semua.sql
-- (landing_page + buku_tamu + haflah_undangan dalam 1 import)
--
-- File ini hanya untuk database landing_page saja.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `landing_page`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `landing_page`;

-- Hapus tabel lama agar import ulang aman
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `footer_links`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `gallery`;
DROP TABLE IF EXISTS `testimonials`;
DROP TABLE IF EXISTS `stats`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `nav_items`;
DROP TABLE IF EXISTS `site_settings`;

-- ------------------------------------------------------------
-- Struktur tabel
-- ------------------------------------------------------------

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

-- ------------------------------------------------------------
-- Data awal
-- ------------------------------------------------------------

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

-- Akun admin default: admin / admin123
INSERT INTO `admins` (`username`, `password_hash`, `full_name`) VALUES
('admin', '$2y$10$AMXBaaakghxeFIjWtHULNuu/5Fna2mDb/8JfC9YC8XIa14lKqUE7G', 'Administrator');

SET FOREIGN_KEY_CHECKS = 1;
