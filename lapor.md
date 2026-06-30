# Laporan Kesiapan Hosting (SIPSR / ArsiPSR)

Berikut adalah hasil pengecekan dan daftar periksa (checklist) untuk mempersiapkan aplikasi agar siap di-deploy ke production / hosting yang diperbarui:

## 1. Konfigurasi Lingkungan (`.env`)
Di server hosting, pastikan file `.env` sudah disesuaikan untuk *production*:
- `APP_ENV=production` (Saat ini: `local`)
- `APP_DEBUG=false` (Saat ini: `true` - **Sangat Penting** untuk dimatikan demi keamanan)
- `APP_URL=https://domain-anda.com` (Saat ini: `sipsr.test` / `http://localhost`)
- Pastikan konfigurasi Database (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) sesuai dengan database hosting.

## 2. Kebutuhan Server (Requirements)
Berdasarkan `composer.json` dan `php artisan about`:
- **Versi PHP**: Minimal PHP 8.3 (Saat ini di lokal: PHP 8.5.0)
- **Ekstensi PHP Wajib**: `mbstring`, `pdo`, `xml`, `openssl`, `json`, `bcmath`, `fileinfo`, `ctype`, `tokenizer`.
- **Ekstensi Tambahan**: Karena menggunakan `maatwebsite/excel` dan `dompdf`, pastikan ekstensi `gd` (atau `imagick`), `zip`, dan `dom` aktif di hosting.

## 3. Optimasi Cache Laravel
Untuk meningkatkan performa di hosting, jalankan perintah optimasi ini melalui SSH atau terminal hosting (jangan dijalankan di lokal jika masih tahap development):
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```
*(Saat ini di lokal Config, Events, dan Routes berstatus `NOT CACHED`)*

## 4. Tautan Storage (Storage Link)
Aplikasi ini belum memiliki symlink untuk folder storage. Hal ini wajib dilakukan agar file yang diunggah ke `storage/app/public` dapat diakses dari web.
- Jalankan: `php artisan storage:link`
*(Saat ini status: `C:\laragon\www\SIPSR\public\storage .. NOT LINKED`)*

## 5. Hak Akses Folder (Permissions)
Pastikan folder berikut dapat ditulis (writable) oleh web server (misal: `www-data` atau `nginx`):
- `storage/` dan seluruh subfoldernya.
- `bootstrap/cache/`
*(Gunakan `chmod -R 775 storage bootstrap/cache` jika menggunakan Linux/CPanel shell)*

## 6. Build Aset Frontend (Vite)
Aplikasi ini menggunakan Node.js untuk frontend (Vite/NPM). Sebelum di-upload atau saat di hosting, pastikan aset sudah di-compile:
```bash
npm install
npm run build
```
Upload folder `public/build/` (hasil build Vite) ke hosting.

## 7. Database Migration
Setelah database dibuat di hosting dan `.env` terkonfigurasi, pastikan struktur tabel diperbarui:
```bash
php artisan migrate --force
```

## 8. Audit Keamanan & Kode (Deep Scan)
Tim telah melakukan pemindaian mendalam tambahan untuk memastikan kesiapan:
- **Ketergantungan (Dependencies)**: Hasil pemindaian `composer audit` menunjukkan **0 kerentanan keamanan** (No security vulnerability advisories found).
- **Migrasi Database**: Semua file migrasi (termasuk yang terbaru: `make_expired_at_nullable_again`) sudah berhasil dijalankan (`Ran`) dan siap untuk dipindahkan strukturnya ke production.
- **Kode Hardcode & Debugging**: Pemeriksaan source code di folder `app/` dan `resources/views/` memastikan tidak ada URL lokal yang tertinggal (seperti `sipsr.test` atau `localhost`) dan tidak ada fungsi *debugging* yang membahayakan (`dd()`, `dump()`) yang bocor ke produksi.
- **Penggunaan ENV**: Tidak ditemukan pemanggilan fungsi `env()` secara langsung pada file aplikasi, hal ini memastikan optimasi `config:cache` nantinya dapat berjalan 100% tanpa error.

## Kesimpulan
Aplikasi (Laravel 13.16.1) secara umum **SANGAT SIAP** untuk diluncurkan ke production. Kunci utama saat pemindahan ke hosting adalah: mengubah status ke **Production/Debug Off**, menghubungkan **storage:link**, dan memastikan server menggunakan **PHP 8.3+**.
