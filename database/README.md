# Database — Landing Page, Buku Tamu, Undangan

Proyek ini memakai **3 database MySQL terpisah** di server yang sama (XAMPP lokal atau hosting).

| Aplikasi | Nama database | File konfigurasi |
|----------|---------------|------------------|
| Landing page CMS | `landing_page` | [`../config/database.php`](../config/database.php) |
| Buku Tamu | `buku_tamu` | [`../buku-tamu/config/database.php`](../buku-tamu/config/database.php) |
| Undangan Digital | `haflah_undangan` | [`../undangan/config/database.php`](../undangan/config/database.php) |

Buku Tamu dan Undangan **tidak** disimpan di database `landing_page`.

---

## Import database (disarankan)

**Sekali jalan — ketiga database:**

1. Start **Apache** dan **MySQL** di XAMPP
2. Buka http://localhost/phpmyadmin
3. Tab **Import** (tidak perlu pilih database dulu)
4. Pilih file [`import_semua.sql`](import_semua.sql) → **Go**

Setelah import, di sidebar phpMyAdmin harus muncul:

- `landing_page` — CMS website, settings, galeri, artikel
- `buku_tamu` — tamu, admin, ndalem, jadwal, WhatsApp
- `haflah_undangan` — events, RSVP, tema undangan

### Import per aplikasi (opsional)

| File | Database |
|------|----------|
| [`landing_page_import.sql`](landing_page_import.sql) | `landing_page` saja |
| [`buku_tamu_import.sql`](buku_tamu_import.sql) | `buku_tamu` saja |
| [`haflah_undangan_import.sql`](haflah_undangan_import.sql) | `haflah_undangan` saja |

Schema asli tiap modul juga ada di:

- [`../buku-tamu/database/schema.sql`](../buku-tamu/database/schema.sql)
- [`../undangan/database/schema.sql`](../undangan/database/schema.sql)

### Migrasi tambahan (jika database sudah lama)

| Modul | File |
|-------|------|
| Buku Tamu | [`../buku-tamu/database/migrate_v2.php`](../buku-tamu/database/migrate_v2.php) … `migrate_v6.php` |
| Undangan | [`../undangan/database/migrate_themes.sql`](../undangan/database/migrate_themes.sql), skrip [`../undangan/migrate_*.php`](../undangan/) |

---

## Lokasi data fisik (XAMPP)

Setelah import, folder MySQL:

```
C:\xampp\mysql\data\landing_page\
C:\xampp\mysql\data\buku_tamu\
C:\xampp\mysql\data\haflah_undangan\
```

---

## Konfigurasi koneksi

### XAMPP lokal (default)

Salin file `database.example.php` → `database.php` di masing-masing folder config, atau sesuaikan manual:

| Aplikasi | Host | Database | User | Password |
|----------|------|----------|------|----------|
| Landing page | `127.0.0.1` | `landing_page` | `root` | *(kosong)* |
| Buku Tamu | `127.0.0.1` | `buku_tamu` | `root` | *(kosong)* |
| Undangan | `localhost` | `haflah_undangan` | `root` | *(kosong)* |

Contoh file: [`../config/database.example.php`](../config/database.example.php)

### Hosting

Sesuaikan ketiga file `database.php` dengan kredensial dari panel hosting (cPanel / hPanel). Biasanya satu user MySQL bisa mengakses beberapa database, atau buat database terpisah per aplikasi.

**Penting:** Jangan commit password production ke Git. Gunakan `database.example.php` sebagai template.

### Hosting vs lokal (perhatian)

| File | Environment saat ini |
|------|----------------------|
| [`../config/database.php`](../config/database.php) | Mengarah ke **hosting** (`u700125577_santri`) — dari commit remote |
| [`../buku-tamu/config/database.php`](../buku-tamu/config/database.php) | XAMPP: `root` / password kosong |
| [`../undangan/config/database.php`](../undangan/config/database.php) | XAMPP: `root` / password kosong |

Untuk development lokal CMS, salin [`../config/database.example.php`](../config/database.example.php) ke `database.php`.  
Untuk deploy ke hosting, sesuaikan **ketiga** file `database.php` (atau buat 3 database terpisah di panel hosting).

---

## URL akses (lokal)

| Aplikasi | URL |
|----------|-----|
| Website | http://localhost/landing%20page/ |
| Back Office CMS | http://localhost/landing%20page/panel.php |
| Buku Tamu | http://localhost/landing%20page/buku-tamu/ |
| Undangan admin | http://localhost/landing%20page/undangan/admin/login.php |

---

## Login default (setelah import)

| Aplikasi | Username | Password |
|----------|----------|----------|
| CMS (`panel.php`) | `admin` | `admin123` |
| Buku Tamu admin | `admin` | `admin123` |
| Buku Tamu ndalem | `ndalem` | `ndalem123` |
| Undangan admin | `admin` | `admin123` |

Ganti password setelah login pertama.
