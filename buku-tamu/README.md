# Buku Tamu Online — Pesantren

Sistem buku tamu digital berbasis web untuk pesantren. Tamu mengisi formulir via HP (scan QR), petugas keamanan melakukan check-in/out, dan asisten pengasuh mengelola antrean sowan.

## Fitur

### Sisi Tamu (Mobile Web)
- Form pengisian tanpa login/aplikasi — cukup scan QR
- Pilihan tujuan kunjungan (Sowan, Jenguk Santri, Administrasi, Kerjasama, Lainnya)
- Field kondisional: nama santri muncul jika memilih "Jenguk Santri"
- Upload foto selfie/KTP (opsional)
- Tiket digital dengan nomor antrean + QR Code

### Sisi Admin (Pos Keamanan)
- Dasbor real-time tamu yang sedang di dalam
- Check-In & Check-Out
- Cetak stiker/kartu tamu (printer mini)
- Statistik kunjungan mingguan/bulanan

### Sisi Ndalem (Asisten Pengasuh)
- Kontrol status ketersediaan Pengasuh (Luang / Sibuk / Ditutup)
- Jika ditutup, form tamu otomatis menolak pilihan "Sowan"
- Manajemen antrean sowan
- Setujui tamu + kirim notifikasi WhatsApp (Fonnte/Wablas)

### Fitur Khusus Pesantren
- Tanggal Hijriah otomatis tercatat
- Integrasi WhatsApp Gateway (Fonnte / Wablas)
- Grafik statistik kunjungan

## Tech Stack

- PHP 8+ (native, tanpa Composer)
- MySQL
- TailwindCSS (CDN)
- Chart.js (statistik)
- XAMPP compatible

## Instalasi (XAMPP)

1. Pastikan **Apache** dan **MySQL** XAMPP sudah running
2. Project sudah berada di `C:\xampp\htdocs\BUKU_TAMU`
3. Buka browser: **http://localhost/BUKU_TAMU/install.php**
4. Klik **Install Database**
5. **Hapus `install.php`** setelah instalasi berhasil

## URL Akses

| Halaman | URL |
|---------|-----|
| Form Tamu | http://localhost/BUKU_TAMU/public/ |
| Poster QR (cetak) | http://localhost/BUKU_TAMU/public/qr-poster.php |
| Login Admin | http://localhost/BUKU_TAMU/public/admin/login |
| Login Ndalem | http://localhost/BUKU_TAMU/public/ndalem/login |

## Akun Default

| Role | Username | Password |
|------|----------|----------|
| Admin (Keamanan) | `admin` | `admin123` |
| Ndalem (Asisten) | `ndalem` | `ndalem123` |

> Ganti password setelah instalasi via phpMyAdmin (hash dengan `password_hash()`).

## Konfigurasi

Edit file `config/app.php`:

```php
'pesantren_name' => 'Nama Pesantren Anda',
'pesantren_address' => 'Alamat lengkap',
'ndalem_ruang' => 'Ruang Tunggu Ndalem Barat',
'whatsapp' => [
    'enabled' => true,
    'provider' => 'fonnte', // atau 'wablas'
    'token' => 'TOKEN_API_ANDA',
],
```

Edit `config/database.php` jika kredensial MySQL berbeda.

## Alur Kerja

```
Tamu scan QR → Isi form → Dapat tiket digital
       ↓
Petugas keamanan Check-In
       ↓
(Jika Sowan) Masuk antrean Ndalem → Asisten setujui → WA ke tamu → Panggil → Selesai
       ↓
Petugas keamanan Check-Out
```

## Cetak QR di Pintu Masuk

1. Buka **qr-poster.php**
2. Klik **Cetak Poster QR**
3. Tempel di pintu masuk / meja resepsionis

## Struktur Folder

```
BUKU_TAMU/
├── config/          # Konfigurasi app & database
├── database/        # schema.sql
├── public/          # Entry point (index.php)
│   └── uploads/     # Foto tamu
├── src/             # Models & services
└── views/           # Template HTML
```

## Lisensi

Gratis digunakan untuk keperluan pesantren.
