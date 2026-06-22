ALTER TABLE gallery MODIFY gallery_type ENUM('prestasi','daily','fasilitas') NOT NULL;

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_logo', 'assets/images/logo-nme.png'),
('admin_login_url', 'admin/login.php'),
('admin_login_text', 'Back Office'),
('section_fasilitas_title', 'Sarana Pendukung Pendidikan'),
('section_fasilitas_subtitle', 'Fasilitas Pesantren')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

UPDATE site_settings SET setting_value = 'A.P.I Nailul Muna' WHERE setting_key = 'site_name' AND setting_value = 'API Nailul Muna';

INSERT INTO gallery (gallery_type, title, image_path, sort_order, is_active)
SELECT 'fasilitas', 'Masjid & Musholla', 'https://images.unsplash.com/photo-1564760055775-d63b17a55c44?auto=format&fit=crop&w=600&q=80', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM gallery WHERE gallery_type = 'fasilitas' LIMIT 1);

INSERT INTO gallery (gallery_type, title, image_path, sort_order, is_active)
SELECT 'fasilitas', 'Perpustakaan Kitab Kuning', 'https://images.unsplash.com/photo-1521587760476-6c122a7fcee1?auto=format&fit=crop&w=600&q=80', 2, 1
WHERE (SELECT COUNT(*) FROM gallery WHERE gallery_type = 'fasilitas') < 2;

INSERT INTO gallery (gallery_type, title, image_path, sort_order, is_active)
SELECT 'fasilitas', 'Laboratorium Komputer', 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=600&q=80', 3, 1
WHERE (SELECT COUNT(*) FROM gallery WHERE gallery_type = 'fasilitas') < 3;

INSERT INTO gallery (gallery_type, title, image_path, sort_order, is_active)
SELECT 'fasilitas', 'Asrama Santri', 'https://images.unsplash.com/photo-1555854877-25aa03b922f7?auto=format&fit=crop&w=600&q=80', 4, 1
WHERE (SELECT COUNT(*) FROM gallery WHERE gallery_type = 'fasilitas') < 4;

INSERT INTO gallery (gallery_type, title, image_path, sort_order, is_active)
SELECT 'fasilitas', 'Ruang Tahfidz', 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=600&q=80', 5, 1
WHERE (SELECT COUNT(*) FROM gallery WHERE gallery_type = 'fasilitas') < 5;

INSERT INTO gallery (gallery_type, title, image_path, sort_order, is_active)
SELECT 'fasilitas', 'Lapangan Olahraga', 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=600&q=80', 6, 1
WHERE (SELECT COUNT(*) FROM gallery WHERE gallery_type = 'fasilitas') < 6;
