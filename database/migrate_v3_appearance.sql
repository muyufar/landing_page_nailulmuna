INSERT INTO site_settings (setting_key, setting_value) VALUES
('navbar_logo_size', '58'),
('navbar_brand_font_size', '1.35'),
('hero_tagline_font_size', '2.75'),
('section_title_font_size', '2'),
('nav_link_font_size', '0.95'),
('gallery_thumb_height', '130'),
('gallery_fasilitas_height', '200'),
('blog_image_height', '200'),
('muassis_photo_max_width', '100')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
