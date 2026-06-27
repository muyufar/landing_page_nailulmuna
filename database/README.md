# Database — Landing Page, Buku Tamu, Undangan

## Mode hosting (satu database) — **aktif saat ini**

Semua modul memakai database **`u700125577_santri`**:

| Aplikasi | Database | File config | Tabel khusus |
|----------|----------|-------------|--------------|
| Landing page CMS | `u700125577_santri` | [`../config/database.php`](../config/database.php) | `admins`, `site_settings`, … |
| Buku Tamu | `u700125577_santri` | [`../buku-tamu/config/database.php`](../buku-tamu/config/database.php) | `bt_users`, `visitors`, … |
| Undangan | `u700125577_santri` | [`../undangan/config/database.php`](../undangan/config/database.php) | `inv_users`, `events`, … |

Tabel `users` dipisah menjadi **`bt_users`** (buku tamu) dan **`inv_users`** (undangan) agar tidak bentrok.

### Import hosting (disarankan)

1. Buka phpMyAdmin → tab **Import**
2. Pilih [`import_hosting.sql`](import_hosting.sql) → **Go**

---

## Mode XAMPP lokal (3 database terpisah) — opsional

Untuk development lokal dengan database terpisah:

| Aplikasi | Database |
|----------|----------|
| Landing page | `landing_page` |
| Buku Tamu | `buku_tamu` |
| Undangan | `haflah_undangan` |

Import: [`import_semua.sql`](import_semua.sql)  
Ubah config ke `root` / password kosong (lihat `database.example.php` di tiap modul).

---

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

### Hosting

Semua modul: database **`u700125577_santri`**, user **`u700125577_santri`**.

Import: [`import_hosting.sql`](import_hosting.sql)

### XAMPP lokal (development)

Salin file `database.example.php` → `database.php` di masing-masing folder config, atau sesuaikan manual:

| Aplikasi | Host | Database | User | Password |
|----------|------|----------|------|----------|
| Landing page | `127.0.0.1` | `landing_page` | `root` | *(kosong)* |
| Buku Tamu | `127.0.0.1` | `buku_tamu` | `root` | *(kosong)* |
| Undangan | `localhost` | `haflah_undangan` | `root` | *(kosong)* |

### Hosting vs lokal (perhatian)

| File | Environment saat ini |
|------|----------------------|
| Ketiga `config/database.php` | Database **`u700125577_santri`**, user hosting |

Untuk development lokal, salin `database.example.php` → `database.php` di tiap modul (database terpisah + `root`).  
Untuk deploy hosting, import [`import_hosting.sql`](import_hosting.sql) — config sudah diset ke **`u700125577_santri`**.

**Penting:** Jangan commit password production ke Git. Gunakan `database.example.php` sebagai template.

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
