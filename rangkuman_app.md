# Rangkuman Komprehensif: SIPSR (Sistem Informasi Pengarsipan PSR Tanaman)

Dokumen ini berisi penjelasan detail dan menyeluruh mengenai arsitektur, fitur, alur kerja, dan struktur teknis dari aplikasi **SIPSR** milik PTPN IV Regional IV. Aplikasi ini dibangun dengan tujuan mendigitalisasi, mengamankan, dan mengorganisasi arsip-arsip penting PSR Tanaman.

---

## 1. Teknologi Dasar (Tech Stack)

Aplikasi SIPSR dirancang menggunakan arsitektur modern berbasis PHP dengan *stack* sebagai berikut:
- **Framework Backend:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL/MariaDB (menggunakan Eloquent ORM)
- **Frontend / Styling:** Blade Templating Engine + Bootstrap 5 + Vanilla CSS Kustom
- **Ikonografi:** Bootstrap Icons
- **Efek Tambahan:** Canvas-Confetti (untuk selebrasi login) dan efek *glassmorphism*/*blob tracking* interaktif kustom.
- **Waktu Sistem:** Disinkronisasi ke `Asia/Jakarta` (WIB) secara *realtime*.

---

## 2. Struktur Hak Akses (Role-Based Access Control)

Sistem membedakan pengguna menjadi dua *role* utama yang diatur ketat melalui *Middleware*:
1. **ADMIN:** Memiliki kontrol penuh atas seluruh sistem. Bisa melihat/mengubah seluruh dokumen, mengakses *Recycle Bin*, melihat riwayat aktivitas *all-user*, dan mengelola master data (Kategori & Manajemen Pengguna). Admin juga memiliki hak untuk menghapus file secara permanen (Force Delete) atau mengosongkan *Recycle Bin*.
2. **USER (Pegawai/Staf):** Hanya dapat mengelola dokumen, membagikan *link*, dan melihat aktivitas dasar tanpa bisa masuk ke ranah konfigurasi sistem atau menghapus pengguna lain.

> **Catatan Keamanan:** Akun pengguna memiliki status `is_active`. Jika dinonaktifkan oleh Admin, sistem akan otomatis melakukan *logout* paksa dan memblokir upaya *login* selanjutnya.

---

## 3. Fitur Utama (Core Features)

### A. Manajemen Dokumen (Arsip)
Ini adalah tulang punggung aplikasi.
- **Upload & Metadata:** Setiap dokumen yang di-*upload* memiliki nomor unik, nama, kategori, tanggal rilis, deskripsi, dan otomatis mencatat siapa (*Uploader*) yang mengunggahnya.
- **Preview Terintegrasi:** Dokumen berekstensi PDF dapat dibaca langsung (*preview*) di dalam aplikasi tanpa perlu di- *download*. Layout *preview* bersifat responsif (menempel/*sticky* dan menyesuaikan tinggi layar desktop, atau bisa disembunyikan/di-*collapse* saat diakses melalui HP).
- **Format Fallback:** Untuk dokumen selain PDF (misal: DOCX), sistem secara cerdas menampilkan pesan peringatan dan tombol *Download* untuk melihat isinya.
- **Pencarian & Filter (Paginasi):** Tabel dokumen dilengkapi fitur pencarian pintar, filter kategori, pengurutan ASC/DESC pada kolom (Nomor, Nama, Tanggal), serta seleksi jumlah baris (*per page*).

### B. Riwayat Perubahan (Audit Trail & Versioning)
Setiap kali sebuah dokumen di-edit (misal mengubah nama atau kategori), sistem menggunakan tabel `DocumentHistory` untuk merekam:
- Tanggal dan jam persis perubahan.
- Siapa (*User*) yang mengubahnya.
- Nama *field* (kolom) apa yang berubah.
- **Data Sebelum (*Old Value*)** vs **Data Sesudah (*New Value*)**.
Ini memastikan transparansi dan integritas data (anti-manipulasi arsip).

### C. Sistem Berbagi Dokumen Aman (Secure Share Links)
Sistem ini memungkinkan pegawai membagikan dokumen ke pihak luar tanpa harus memberi *login* akun.
- **Masa Berlaku Dinamis (Slider):** Pengguna dapat menggeser tuas *slider* untuk menentukan umur tautan mulai dari **1 Jam hingga 7 Hari**. Slider ini memiliki efek "magnet" untuk menempel ke kelipatan waktu tertentu (24, 48, 72 jam, dst).
- **Tautan Permanen (Selamanya):** Jika dicentang, tautan tidak akan kedaluwarsa secara otomatis. Fitur ini diatur sebagai *default* opsi saat dialog pembagian dibuka.
- **Pencabutan Manual (*Revoke*):** Meski *link* masih aktif, pembuat *link* bisa mematikannya secara paksa dengan satu tombol klik.
- **Auto-Expired:** Setelah waktu habis, siapa pun yang mengeklik *link* akan mendapati halaman akses ditolak (*Expired*).

### D. Manajemen Sampah (Recycle Bin & Auto-Prune)
Sistem menggunakan metode `SoftDeletes`. Saat dokumen dihapus, ia tidak benar-benar hilang dari *database*, melainkan masuk ke *Recycle Bin*.
- **Restore:** Mengembalikan dokumen yang salah hapus ke posisi semula.
- **Force Delete / Empty Bin:** Menghancurkan file secara fisik dari ruang penyimpanan server.
- **Auto-Prune (Pembersih Otomatis):** Dokumen yang sudah dibiarkan di dalam *Recycle Bin* selama lebih dari **30 Hari** akan **dihapus secara permanen oleh sistem secara otomatis** setiap harinya (lewat *Laravel Scheduler*).

### E. Catatan Aktivitas (Activity Log)
Segala macam "gerak-gerik" di dalam aplikasi direkam. Mulai dari *Login*, membuat dokumen, hingga menghapus data. Log ini memuat *IP Address*, perangkat (*User Agent*), detail aktivitas, dan siapa pelakunya.

---

## 4. Estetika Visual dan User Experience (UX)

Alih-alih terlihat kaku seperti sistem pemerintahan pada umumnya, SIPSR dibangun dengan desain premium:
- **Warna Otentik:** Identitas hijau gelap PTPN (`#3B6D11`) mendominasi, dipadukan dengan efek-efek abu-abu bersih dan gaya bayangan (shadow) elegan.
- **Animasi Global:** 
  - Seluruh halaman menggunakan efek *fade-in* lembut setiap kali dimuat.
  - Kartu-kartu menu muncul dengan efek bergeser dari bawah (*fade-in-up*).
- **Login Ekstraordiner:** Halaman login dibekali bola abstrak gradien (*Antigravity Blob*) di latar belakang yang terus bergerak merespons arah kursor *mouse*.
- **Selebrasi:** Jika *login* berhasil, pengguna disambut dengan semburan *paper-splash* (Confetti) dengan *tone* warna hijau putih.
- **Tata Letak Responsif:** Kolom tabel bersembunyi otomatis di HP, tombol bisa memanjang penuh, fitur *collapse*, dan penempatan *Sidebar* yang menyesuaikan perangkat (*Sidebar Overlay*).
- **Zebra Striping Tabel:** Baris pada tabel (seperti di *Recycle Bin* dan Dokumen Utama) dibuat belang-belang abu-putih agar data lebih mudah dibaca, dilengkapi nomor urut yang otomatis menyesuaikan rentang pakinasi.

---

## 5. Ringkasan Basis Data (Struktur Skema Inti)

1. `users`: Menyimpan kredensial (`username`, `password`, `role`, status `is_active`).
2. `categories`: Mengelola klasifikasi map arsip.
3. `documents`: Menyimpan detail utama arsip. Menggunakan UUID untuk keamanan, merekam ID pengunggah, pengedit, dan penghapus.
4. `document_histories`: Catatan rekam jejak kolom per kolom.
5. `document_share_links`: Menyimpan token tautan publik (UUID acak), `expired_at` (kedaluwarsa), dan `revoked_at` (dicabut).
6. `activity_logs`: Rangkuman log kejadian sistem secara luas (Tipe Kejadian, IP, Perangkat).

---

***

*Dokumen ini dibuat otomatis sebagai panduan operasional teknis dan navigasi logika aplikasi SIPSR. Aplikasi ini memadukan keamanan data yang kokoh dengan balutan antarmuka yang sangat "smooth" dan memanjakan mata penggunanya.*
