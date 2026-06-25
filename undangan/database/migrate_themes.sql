-- Migrasi: tema preset & gambar ornamen
USE haflah_undangan;

ALTER TABLE events
  ADD COLUMN IF NOT EXISTS theme_preset VARCHAR(30) NOT NULL DEFAULT 'hijau_emas' AFTER color_accent,
  ADD COLUMN IF NOT EXISTS ornament_top TEXT NULL AFTER logo_haflah,
  ADD COLUMN IF NOT EXISTS ornament_divider TEXT NULL AFTER ornament_top,
  ADD COLUMN IF NOT EXISTS ornament_bottom TEXT NULL AFTER ornament_divider,
  ADD COLUMN IF NOT EXISTS bg_image TEXT NULL AFTER ornament_bottom;
