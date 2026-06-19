# SIPSR — Sistem Informasi Pengarsipan PSR Tanaman

> Aplikasi pengarsipan dokumen internal Bidang PSR Bagian Tanaman, PTPN IV Regional IV

## Tech Stack

- **Backend:** Laravel 11 · PHP 8.4
- **Frontend:** Blade · Bootstrap 5 · Bootstrap Icons
- **Database:** MySQL 8.0
- **Server:** Laragon Full 6.0

## Prasyarat

- [Laragon Full 6.0](https://laragon.org/download/) (Windows)
- PHP ≥ 8.3
- MySQL 8.0
- Composer
- Node.js & npm

## Instalasi

### 1. Clone / Buka Project

Project berada di folder Laragon:

```
c:\laragon\www\SIPSR
```

### 2. Buat Database

Buka **HeidiSQL** (bawaan Laragon) atau MySQL CLI:

```sql
CREATE DATABASE sipsr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Install Dependencies

```bash
composer install
npm install
```

### 4. Konfigurasi Environment

File `.env` sudah dikonfigurasi. Pastikan setting database benar:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipsr
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migrasi & Seeder

```bash
php artisan migrate --seed
```

### 6. Build Frontend Assets

```bash
npm run build
```

### 7. Jalankan Aplikasi

**Opsi A — Virtual Host Laragon (Rekomendasi):**

1. Klik kanan tray icon Laragon → **Apache** → **sites-enabled**
2. Laragon otomatis membuat virtual host `sipsr.test`
3. Buka browser: **http://sipsr.test**

**Opsi B — Artisan Serve:**

```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

## Akun Login

| Username  | Password    | Role  |
|-----------|-------------|-------|
| admin     | Admin1234   | ADMIN |
| budi_psr  | Staff1234   | STAFF |
| sari_psr  | Staff1234   | STAFF |
| eko_psr   | Staff1234   | STAFF |

## Struktur Folder

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   └── DashboardController.php
│   └── Middleware/
│       └── RoleMiddleware.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Document.php
│   ├── DocumentHistory.php
│   ├── DocumentShareLink.php
│   └── ActivityLog.php
database/
├── migrations/ (8 file)
└── seeders/
    ├── DatabaseSeeder.php
    ├── UserSeeder.php
    ├── CategorySeeder.php
    ├── DocumentSeeder.php
    └── ActivityLogSeeder.php
resources/views/
├── layouts/
│   ├── app.blade.php
│   └── guest.blade.php
├── auth/
│   └── login.blade.php
├── dashboard/
│   └── index.blade.php
└── components/
    ├── sidebar.blade.php
    ├── navbar.blade.php
    └── toast.blade.php
storage/app/uploads/
```

## Migrasi Database

| Tabel                  | Deskripsi                       |
|------------------------|---------------------------------|
| users                  | Data pengguna (UUID, role)      |
| categories             | Kategori dokumen                |
| documents              | Dokumen arsip (soft delete)     |
| document_histories     | Riwayat perubahan dokumen       |
| document_share_links   | Link sharing dengan token       |
| activity_logs          | Log aktivitas pengguna          |

## Backup Database

Untuk melakukan pencadangan (backup) seluruh data sistem, buka terminal Laragon dan jalankan perintah berikut:

```bash
mysqldump -u root sipsr > backup_sipsr.sql
```

## License

Internal use only — PTPN IV Regional IV
