# SIMAS - Sistem Manajemen Aset

Sistem Manajemen Aset (SIMAS) adalah aplikasi web berbasis Laravel untuk mengelola aset barang inventaris di lingkungan BRIN. Aplikasi ini mendukung pencatatan aset, kategori, peminjaman, pengembalian, QR code scanning, hingga manajemen pengguna dan role.

---

## Daftar Isi

1. [Tentang Aplikasi](#tentang-aplikasi)
2. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
3. [Persyaratan Sistem](#persyaratan-sistem)
4. [Instalasi](#instalasi)
5. [Konfigurasi](#konfigurasi)
6. [Database & Migrasi](#database--migrasi)
7. [Seed Data (Data Awal)](#seed-data-data-awal)
8. [Menjalankan Aplikasi](#menjalankan-aplikasi)
9. [Akun Default](#akun-default)
10. [Struktur Proyek](#struktur-proyek)
11. [Skema Database](#skema-database)
12. [Role & Hak Akses](#role--hak-akses)
13. [Fitur Lengkap](#fitur-lengkap)
14. [Routing](#routing)
15. [Sistem Kode Aset & Kode Peminjaman](#sistem-kode-aset--kode-peminjaman)
16. [Storage Foto (RustFS)](#storage-foto-rustfs)
17. [QR Code & Akses Publik](#qr-code--akses-publik)
18. [Akses dari HP dengan ngrok](#akses-dari-hp-dengan-ngrok)
19. [Build Frontend (Vite & Tailwind)](#build-frontend-vite--tailwind)
20. [Testing](#testing)
21. [Deployment](#deployment)
22. [Troubleshooting](#troubleshooting)
23. [Lisensi](#lisensi)

---

## Tentang Aplikasi

SIMAS dibangun untuk membantu institusi/organisasi dalam mengelola inventaris aset secara digital. Sistem ini menggantikan pencatatan manual dengan fitur otomatis seperti:

- Pencatatan master data aset beserta foto dan kategori
- Sistem peminjaman dan pengembalian aset dengan QR code
- Pencarian dan pelacakan aset melalui QR code scanning dari HP
- Manajemen pengguna dengan role-based access control (RBAC)
- Riwayat peminjaman yang terdokumentasi dengan audit trail
- Export data aset ke Excel
- Dashboard statistik real-time

---

## Teknologi yang Digunakan

| Komponen | Teknologi | Versi |
|---|---|---|
| Backend Framework | Laravel | ^12.0 |
| Bahasa Pemrograman | PHP | ^8.2 |
| Frontend CSS Framework | Tailwind CSS | ^4.0 |
| Frontend Build Tool | Vite | ^7.0 |
| Template Engine | Blade | Bawaan Laravel |
| Database (Produksi) | PostgreSQL | 14+ |
| Database (Default .env.example) | SQLite | - |
| Object Storage | RustFS (S3-compatible) | - |
| Session Driver | Database | - |
| Cache Driver | Database | - |
| Authentication | Laravel Auth + Sanctum | ^4.0 |
| PHP Dependency Manager | Composer | 2.x |
| JS Package Manager | npm | - |

---

## Persyaratan Sistem

Sebelum menginstal, pastikan sistem Anda memenuhi:

- **PHP** >= 8.2 dengan ekstensi: `pdo`, `pdo_pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` (untuk foto)
- **Composer** >= 2.x
- **Node.js** >= 18.x dan **npm** >= 9.x
- **PostgreSQL** >= 14 (untuk produksi) atau dukungan SQLite (untuk development cepat)
- **RustFS** (opsional, untuk object storage foto aset) atau bisa gunakan local storage sebagai fallback

---

## Instalasi

### 1. Clone Repository

```powershell
git clone <url-repository>
cd Simas
```

### 2. Install Dependency PHP

```powershell
composer install
```

### 3. Install Dependency Frontend

```powershell
npm install
```

### 4. Copy File Environment

```powershell
Copy-Item .env.example .env
```

### 5. Generate Application Key

```powershell
php artisan key:generate
```

### 6. Build Frontend Assets

```powershell
npm run build
```

> Untuk development dengan hot reload, gunakan `npm run dev` sebagai gantinya.

---

## Konfigurasi

Buka file `.env` di root proyek dan sesuaikan konfigurasi berikut:

### Konfigurasi Aplikasi

```env
APP_NAME="SIMAS - Sistem Manajemen Aset"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### Konfigurasi Database (PostgreSQL)

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=simas_db
DB_USERNAME=postgres
DB_PASSWORD=password_anda
```

> Jika menggunakan SQLite, ubah `DB_CONNECTION=sqlite` dan pastikan file `database/database.sqlite` ada.

### Konfigurasi Session & Cache

```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Konfigurasi Email (untuk Reset Password)

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@simas.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Konfigurasi RustFS (Object Storage untuk Foto Aset)

```env
RUSTFS_ACCESS_KEY=admin
RUSTFS_SECRET_KEY=admin123
RUSTFS_REGION=us-east-1
RUSTFS_BUCKET=master-asset
RUSTFS_ENDPOINT=http://127.0.0.1:9000
RUSTFS_USE_PATH_STYLE_ENDPOINT=true
RUSTFS_URL=http://127.0.0.1:9000/master-asset
```

> Jika RustFS tidak tersedia, sistem otomatis fallback ke local storage (`storage/app/public`).

---

## Database & Migrasi

### Menjalankan Migrasi

Buat database PostgreSQL terlebih dahulu:

```sql
CREATE DATABASE simas_db;
```

Kemudian jalankan migrasi:

```powershell
php artisan migrate
```

### Daftar Migrasi

| File Migrasi | Deskripsi |
|---|---|
| `0001_01_01_000000_create_users_table` | Tabel users, password_reset_tokens, sessions |
| `0001_01_01_000001_create_cache_table` | Tabel cache |
| `0001_01_01_000002_create_jobs_table` | Tabel jobs (queue) |
| `2026_04_21_033426_create_categories_table` | Tabel categories |
| `2026_04_21_033603_create_assets_table` | Tabel assets |
| `2026_04_21_033722_create_loans_table` | Tabel loans dengan audit trail & soft delete |
| `2026_04_21_033746_create_loan_details_table` | Tabel pivot loan_details (loan-asset) |
| `2026_04_29_060439_create_roles_table` | Tabel roles |
| `2026_05_04_000000_add_qr_code_to_assets_table` | Kolom qr_code, brand, model, year, serial_number, location |
| `2026_05_04_061551_add_category_code_to_categories_table` | Kolom category_code |
| `2026_05_04_100000_add_photo_to_assets_table` | Kolom photo |
| `2026_05_05_032359_add_asset_id_to_loans` | Kolom asset_id di loans |
| `2026_05_12_030207_add_condition_to_loan_details_table` | Kolom condition di loan_details |
| `2026_05_12_040000_add_loan_code_to_loans` | Kolom loan_code di loans |

### Rollback Migrasi

```powershell
# Rollback batch terakhir
php artisan migrate:rollback

# Rollback semua
php artisan migrate:reset

# Fresh migrate (rollback + migrate ulang)
php artisan migrate:fresh
```

---

## Seed Data (Data Awal)

Setelah migrasi, jalankan seeder untuk mengisi data awal:

```powershell
php artisan db:seed
```

Atau jalankan migrate + seed sekaligus:

```powershell
php artisan migrate:fresh --seed
```

### Data yang Diisi oleh Seeder

**RoleSeeder** - Mengisi tabel `roles`:
| ID | Nama Role |
|---|---|
| 1 | admin |
| 2 | staff |

**UsersSeeder** - Mengisi tabel `users`:
| Nama | Email | Password | Role |
|---|---|---|---|
| Admin User | admin@simas.com | admin123 | admin |
| Staff User | staff@simas.com | staff123 | staff |

**CategorySeeder** - Mengisi tabel `categories`:
| ID | Nama Kategori | Kode Kategori |
|---|---|---|
| 1 | Laptop | 01 |
| 2 | Monitor | 02 |
| 3 | Printer | 03 |
| 4 | Keyboard | 04 |
| 5 | Mouse | 05 |

**AssetSeeder** - Mengisi tabel `assets` dengan data contoh.

---

## Menjalankan Aplikasi

### Menjalankan Development Server

```powershell
php artisan serve
```

Aplikasi akan tersedia di `http://localhost:8000`.

### Menjalankan dengan Vite (Hot Reload Frontend)

Buka terminal terpisah:

```powershell
npm run dev
```

### Menjalankan Keduanya Sekaligus

```powershell
composer run dev
```

Perintah ini menjalankan Laravel server dan Vite secara bersamaan menggunakan `concurrently`.

---

## Akun Default

Setelah seeding, gunakan akun berikut untuk login:

| Role | Email | Password |
|---|---|---|
| Admin | admin@simas.com | admin123 |
| Staff | staff@simas.com | staff123 |

> Password default untuk reset password user oleh admin adalah `password123`.

---

## Struktur Proyek

```
Simas/
├── app/
│   ├── Helpers/
│   │   └── AssetHelper.php          # Helper untuk URL foto (RustFS/local)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AssetController.php          # CRUD aset (admin)
│   │   │   ├── AssetLibraryController.php   # QR scan & detail aset
│   │   │   ├── AssetReturnController.php    # Pengembalian aset
│   │   │   ├── AuthController.php           # Login, logout, reset password
│   │   │   ├── CategoryController.php       # CRUD kategori (admin)
│   │   │   ├── DashboardController.php      # Dashboard & statistik
│   │   │   ├── LoanCheckController.php      # Cek peminjaman (admin)
│   │   │   ├── LoanController.php           # Peminjaman aset
│   │   │   ├── LoanHistoryController.php    # Riwayat peminjaman
│   │   │   ├── ProfileController.php        # Profil pengguna
│   │   │   ├── RoleController.php           # CRUD role (admin)
│   │   │   └── UserController.php           # CRUD user (admin)
│   │   └── Middleware/
│   │       ├── CheckActiveStatus.php        # Cek user aktif/nonaktif
│   │       ├── NoCacheMiddleware.php        # Disable cache halaman
│   │       └── RoleMiddleware.php           # Role-based access control
│   └── Models/
│       ├── Asset.php
│       ├── Category.php
│       ├── Loan.php
│       ├── Role.php
│       └── User.php
├── bootstrap/
│   └── app.php                              # Konfigurasi middleware & routing
├── config/
│   ├── database.php
│   └── filesystems.php                      # Konfigurasi disk RustFS & local
├── database/
│   ├── migrations/                          # Skema database
│   └── seeders/                             # Data awal
├── resources/
│   └── views/
│       ├── asset-library/                   # Halaman QR scan & detail aset
│       ├── assets/                           # Halaman master aset
│       ├── auth/                             # Login, forgot, reset password
│       ├── layouts/app.blade.php             # Layout utama
│       ├── loan/                              # Halaman peminjaman
│       ├── loan-check/                       # Halaman cek peminjaman (admin)
│       ├── loan-history/                     # Halaman riwayat peminjaman
│       ├── pengembalian/                     # Halaman pengembalian aset
│       ├── roles/                             # Halaman master role (admin)
│       ├── users/                             # Halaman master user (admin)
│       ├── dashboard.blade.php
│       └── profile.blade.php
├── routes/
│   └── web.php                               # Semua route aplikasi
├── .env                                      # Konfigurasi environment
├── composer.json
├── package.json
└── artisan
```

---

## Skema Database

### Tabel `users`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | Primary key |
| name | varchar(100) | Nama lengkap |
| email | varchar(150) | Email unik |
| email_verified_at | timestamp | Verifikasi email (nullable) |
| password | string | Password (hashed bcrypt) |
| role_id | foreignId | Relasi ke roles |
| is_active | boolean | Status aktif/nonaktif |
| profile_photo | string | Path foto profil (nullable) |
| remember_token | string | Token "remember me" |
| created_at, updated_at | timestamp | Timestamps |

### Tabel `roles`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | Primary key |
| name | varchar(100) | Nama role (admin, staff, dll) |
| created_at, updated_at | timestamp | Timestamps |

### Tabel `categories`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | Primary key |
| name | varchar(100) | Nama kategori |
| description | text | Deskripsi kategori (nullable) |
| category_code | varchar(2) | Kode kategori (01, 02, dst) |
| created_at, updated_at | timestamp | Timestamps |

### Tabel `assets`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | Primary key |
| category_id | foreignId | Relasi ke categories |
| name | varchar(100) | Nama aset |
| code | varchar(50) | Kode aset unik (BRIN-XX-YYYYYY) |
| qr_code | varchar(100) | QR code (nullable) |
| description | text | Deskripsi aset (nullable) |
| brand | string | Merek (nullable) |
| model | string | Model (nullable) |
| year | integer | Tahun (nullable) |
| serial_number | string | Nomor seri (nullable) |
| location | string | Lokasi (nullable) |
| stock | integer | Jumlah stok tersedia |
| condition | varchar(20) | Kondisi: baik, rusak, hilang |
| status | varchar(20) | Status: tersedia, dipinjam, perlu_perbaikan, tidak_tersedia |
| photo | string | Path foto di RustFS (nullable) |
| created_at, updated_at | timestamp | Timestamps |

### Tabel `loans`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | Primary key |
| user_id | foreignId | User yang meminjam |
| borrow_date | date | Tanggal pinjam |
| return_date | date | Tanggal kembali (nullable) |
| status | varchar(20) | Status: dipinjam, dikembalikan |
| loan_code | string | Kode peminjaman (PIN-XXXXXX) |
| created_by | foreignId | User yang membuat (audit) |
| updated_by | foreignId | User yang mengupdate (audit) |
| deleted_by | foreignId | User yang menghapus (audit) |
| created_at, updated_at | timestamp | Timestamps |
| deleted_at | timestamp | Soft delete |

### Tabel `loan_details` (pivot)

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | Primary key |
| loan_id | foreignId | Relasi ke loans |
| asset_id | foreignId | Relasi ke assets |
| quantity | integer | Jumlah yang dipinjam |
| condition | varchar | Kondisi saat dikembalikan: baik, rusak, hilang (nullable) |
| created_at, updated_at | timestamp | Timestamps |

### Relasi Antar Tabel

```
roles (1) ──── (N) users
categories (1) ── (N) assets
users (1) ──── (N) loans
loans (N) ←──→ (N) assets   [melalui loan_details]
users (1) ──── (N) loans     [created_by, updated_by, deleted_by]
```

---

## Role & Hak Akses

### Role yang Tersedia

| Role | Deskripsi |
|---|---|
| **admin** | Akses penuh ke semua fitur termasuk manajemen aset, kategori, user, role, dan pengecekan peminjaman |
| **staff** | Akses terbatas: dashboard, peminjaman, pengembalian, riwayat peminjaman, asset library, dan profil |

### Matriks Hak Akses

| Fitur | Admin | Staff |
|---|:---:|:---:|
| Dashboard | ✅ | ✅ |
| Peminjaman Aset | ✅ | ✅ |
| Pengembalian Aset | ✅ | ✅ |
| Riwayat Peminjaman (sendiri) | ✅ | ✅ |
| Asset Library (QR scan) | ✅ | ✅ |
| Profil | ✅ | ✅ |
| Master Aset (CRUD, Export) | ✅ | ❌ |
| Master Kategori (CRUD) | ✅ | ❌ |
| Master User (CRUD, Reset Password) | ✅ | ❌ |
| Master Role (CRUD) | ✅ | ❌ |
| Pengecek Peminjaman (semua user) | ✅ | ❌ |
| Riwayat Aset (per asset) | ✅ | ❌ |
| Riwayat User (per user) | ✅ | ❌ |

### Middleware

| Middleware | Alias | Fungsi |
|---|---|---|
| `RoleMiddleware` | `role` | Membatasi akses berdasarkan nama role |
| `CheckActiveStatus` | `checkactive` | Memaksa logout jika user dinonaktifkan (auto-run di semua route web) |
| `NoCacheMiddleware` | `nocache` | Mencegah caching halaman agar data selalu fresh |

---

## Fitur Lengkap

### 1. Autentikasi

- Login dengan email dan password
- Logout dengan invalidasi session
- Forgot password (kirim link reset via email)
- Reset password dengan token
- Validasi status user aktif saat login
- Auto-logout jika user dinonaktifkan saat sedang login

### 2. Dashboard

- Statistik aset: total, tersedia, dipinjam, perlu perbaikan, hilang
- Galeri aset terbaru (6 item)
- Grafik maintenance aset per bulan
- Navigasi menu berdasarkan role

### 3. Master Aset (Admin)

- Tambah aset dengan auto-generate kode (format: `BRIN-XX-YYYYYY`)
- Upload foto aset ke RustFS (wajib, maks 2MB, format: jpeg/png/jpg)
- Edit aset (nama, kategori, kondisi, status, foto)
- Hapus aset individual maupun bulk delete
- Pencarian aset (kode, nama, kategori, kondisi, status, deskripsi)
- Sorting (kode, nama, terbaru, per kategori)
- Export data ke Excel (.xls)
- Riwayat peminjaman per aset (aktif & selesai)
- Pagination 15 data per halaman

### 4. Master Kategori (Admin)

- Tambah kategori dengan auto-generate kode kategori (01, 02, dst)
- Edit kategori
- Hapus kategori (otomatis menghapus semua aset dalam kategori tersebut)
- Bulk delete kategori
- Pencarian (nama, deskripsi, kode kategori)
- Sorting (terbaru, terlama, A-Z, Z-A)
- Pagination 10 data per halaman

### 5. Peminjaman Aset (Semua User)

- Pilih multiple aset sekaligus (maksimal 5 aset per peminjaman)
- Cek ketersediaan aset (hanya yang status `tersedia`)
- Cek duplikat aset (tidak boleh pinjam aset yang sama)
- Batas dinamis: total aset dipinjam user maksimal 5
- Auto-generate kode peminjaman (format: `PIN-XXXXXX`)
- Auto-update status aset menjadi `dipinjam` dan kurangi stok
- Audit trail (created_by otomatis diisi)

### 6. Pengembalian Aset (Semua User)

- Lihat daftar user dengan peminjaman aktif
- Pilih aset yang akan dikembalikan
- Pilih kondisi saat dikembalikan: `baik`, `rusak`, atau `hilang`
- Auto-update status aset:
  - **baik** → status `tersedia`, stok +1
  - **rusak** → status `perlu_perbaikan`, stok +1
  - **hilang** → status `tidak_tersedia`, stok 0
- Auto-update status loan menjadi `dikembalikan` jika semua aset sudah dikembalikan
- Transaksi database (rollback jika gagal)

### 7. Riwayat Peminjaman (Semua User)

- Lihat riwayat peminjaman sendiri (admin & staff)
- Detail peminjaman dengan list aset dan kondisi
- Pencarian (kode peminjaman, tanggal, status)
- Dukungan pencarian format tanggal Indonesia (contoh: "12 mei", "12 mei 2026")
- Sorting (terbaru, terlama)
- Pagination 15 data per halaman

### 8. Pengecek Peminjaman (Admin)

- Lihat semua peminjaman aktif (status `dipinjam`) dari semua user
- Detail peminjaman dengan informasi user dan aset
- Bulk delete peminjaman (otomatis kembalikan aset ke `tersedia`)
- Pencarian (kode, nama/email user, tanggal)
- Sorting (terbaru, terlama)
- Pagination 15 data per halaman

### 9. Asset Library & QR Code

- Scan QR code aset dari kamera HP atau web
- Generate QR code untuk setiap aset
- Halaman detail aset setelah scan (menampilkan spesifikasi, foto, riwayat)
- Halaman publik (tanpa login) untuk akses QR dari HP
- Photo proxy endpoint untuk menyajikan foto dari RustFS ke eksternal

### 10. Master User (Admin)

- Tambah user (nama, email, password, role, status aktif)
- Edit user (nama, email, role, password opsional)
- Hapus user (dengan proteksi: tidak bisa hapus diri sendiri, tidak bisa hapus user yang punya riwayat peminjaman)
- Reset password user ke default (`password123`)
- Aktifkan/nonaktifkan user (otomatis logout user yang dinonaktifkan)
- Riwayat peminjaman per user (aktif & selesai)
- Pencarian (nama, email, role, status)
- Pagination 15 data per halaman

### 11. Master Role (Admin)

- Tambah role
- Edit role
- Hapus role (dengan proteksi: role `Admin` tidak bisa dihapus)
- Bulk delete role (kecuali `Admin`)
- Hapus semua role (kecuali `Admin`)
- Pencarian dan sorting
- Pagination 5 data per halaman

### 12. Profil Pengguna

- Update nama dan email
- Ganti password (minimal 8 karakter, perlu konfirmasi)
- Jika password diubah, user otomatis logout dan harus login ulang
- Logout dari semua perangkat

---

## Routing

### Route Publik (Tanpa Login)

| Method | URL | Nama Route | Fungsi |
|---|---|---|---|
| GET | `/` | - | Redirect ke login |
| GET | `/login` | `login` | Halaman login |
| POST | `/login` | `login.process` | Proses login |
| GET | `/forgot-password` | `password.request` | Halaman forgot password |
| POST | `/forgot-password` | `password.email` | Kirim link reset password |
| GET | `/reset-password/{token}` | `password.reset` | Halaman reset password |
| POST | `/reset-password` | `password.update` | Proses reset password |
| POST | `/logout` | `logout` | Logout |
| GET | `/asset/{code}` | `asset.public` | Detail aset publik (untuk QR scan) |
| GET | `/scan` | `scan.public` | Halaman scan publik |
| GET | `/asset-library/search` | `asset-library.search` | Cari aset berdasarkan QR code |
| GET | `/asset-photo/{path}` | `asset.photo` | Proxy foto dari RustFS |

### Route Auth (Perlu Login)

| Method | URL | Nama Route | Fungsi | Role |
|---|---|---|---|---|
| GET | `/dashboard` | `dashboard` | Dashboard | All |
| GET | `/layanan` | `layanan` | Halaman layanan/menu | All |
| GET | `/peminjaman` | `peminjaman` | Form peminjaman | All |
| POST | `/peminjaman` | `peminjaman.store` | Simpan peminjaman | All |
| GET | `/pengembalian` | `pengembalian` | Form pengembalian | All |
| POST | `/pengembalian` | `pengembalian.store` | Proses pengembalian | All |
| GET | `/riwayat-peminjaman` | `riwayat-peminjaman` | Riwayat peminjaman | All |
| GET | `/riwayat-peminjaman/{id}` | `riwayat-peminjaman.show` | Detail riwayat | All |
| GET | `/asset-library` | `asset-library.index` | Asset library | All |
| GET | `/asset-library/scan` | `asset-library.scan` | Scan QR (login) | All |
| GET | `/asset-library/qr-generator` | `asset-library.qr-generator` | Generator QR | All |
| GET | `/asset-library/{code}` | `asset-library.show` | Detail aset (login) | All |
| GET | `/profile` | `profile` | Halaman profil | All |
| PUT | `/profile` | `profile.update` | Update profil | All |
| GET | `/assets` | `assets.index` | Master aset | Admin |
| POST | `/assets` | `assets.store` | Tambah aset | Admin |
| GET | `/assets/{id}` | `assets.show` | Detail aset | Admin |
| PUT/PATCH | `/assets/{id}` | `assets.update` | Edit aset | Admin |
| DELETE | `/assets/{id}` | `assets.destroy` | Hapus aset | Admin |
| GET | `/assets-export` | `assets.export` | Export Excel | Admin |
| GET | `/assets/next-code` | - | Generate kode aset | Admin |
| POST | `/assets/bulk-delete` | - | Bulk hapus aset | Admin |
| GET | `/assets/{id}/history` | `assets.history` | Riwayat aset | Admin |
| GET | `/categories` | `categories.index` | Master kategori | Admin |
| POST | `/categories` | `categories.store` | Tambah kategori | Admin |
| PUT/PATCH | `/categories/{id}` | `categories.update` | Edit kategori | Admin |
| DELETE | `/categories/{id}` | `categories.destroy` | Hapus kategori | Admin |
| GET | `/categories/list` | - | List kategori (AJAX) | Admin |
| GET | `/users` | `users.index` | Master user | Admin |
| POST | `/users` | `users.store` | Tambah user | Admin |
| GET | `/users/{id}/edit` | - | Edit user (AJAX) | Admin |
| PUT/PATCH | `/users/{id}` | `users.update` | Update user | Admin |
| DELETE | `/users/{id}` | `users.destroy` | Hapus user | Admin |
| POST | `/users/{id}/reset-password` | `users.reset` | Reset password | Admin |
| GET | `/users/{id}/history` | `users.history` | Riwayat user | Admin |
| GET | `/roles` | `roles.index` | Master role | Admin |
| POST | `/roles` | `roles.store` | Tambah role | Admin |
| PUT/PATCH | `/roles/{id}` | `roles.update` | Edit role | Admin |
| DELETE | `/roles/{id}` | `roles.destroy` | Hapus role | Admin |
| POST | `/roles/bulk-delete` | `roles.bulkDelete` | Bulk hapus role | Admin |
| GET | `/roles-delete-all` | `roles.deleteAll` | Hapus semua role | Admin |
| GET | `/pengecek-peminjaman` | `pengecek-peminjaman` | Cek peminjaman | Admin |
| GET | `/pengecek-peminjaman/{id}` | `pengecek-peminjaman.show` | Detail peminjaman | Admin |
| POST | `/pengecek-peminjaman/bulk-delete` | `pengecek-peminjaman.bulkDelete` | Bulk hapus | Admin |

---

## Sistem Kode Aset & Kode Peminjaman

### Format Kode Aset

```
BRIN-XX-YYYYYY
```

| Bagian | Keterangan | Contoh |
|---|---|---|
| `BRIN` | Prefix tetap | BRIN |
| `XX` | Kode kategori (2 digit) | 01 (Laptop), 02 (Monitor), dll |
| `YYYYYY` | Nomor urut (6 digit) | 000001, 000002, dst |

Contoh: `BRIN-01-000001` = Aset Laptop pertama

Kode di-generate otomatis berdasarkan ID aset terakhir + 1, sehingga selalu unik dan berurutan.

### Format Kode Peminjaman

```
PIN-XXXXXX
```

| Bagian | Keterangan | Contoh |
|---|---|---|
| `PIN` | Prefix tetap | PIN |
| `XXXXXX` | Nomor urut (minimal 6 digit) | 000001, 000010, dst |

Contoh: `PIN-000001` = Peminjaman pertama

Kode di-generate setelah loan dibuat menggunakan ID loan, dengan padding minimal 6 digit.

---

## Storage Foto (RustFS)

SIMAS menggunakan RustFS (S3-compatible object storage) untuk menyimpan foto aset. Konfigurasi disk tersimpan di `config/filesystems.php` dengan key `rustfs`.

### Cara Kerja

1. Saat admin menambah/edit aset, foto di-upload ke bucket RustFS (`master-asset`)
2. Path foto disimpan di kolom `assets.photo`
3. Helper `AssetHelper::getPhotoUrl()` meng-generate URL foto (RustFS URL atau fallback ke local)
4. Route `/asset-photo/{path}` menyajikan foto dari RustFS untuk akses eksternal (misal: dari HP)

### Fallback ke Local Storage

Jika RustFS tidak tersedia atau error, sistem otomatis fallback ke local storage di `storage/app/public/`. Untuk mengakses foto dari local storage, jalankan:

```powershell
php artisan storage:link
```

---

## QR Code & Akses Publik

SIMAS mendukung QR code scanning untuk identifikasi aset dari perangkat eksternal (HP):

1. **Generate QR Code** — Admin dapat generate QR code untuk setiap aset dari menu QR Generator. QR code berisi URL publik: `{APP_URL}/asset/{kode-aset}`

2. **Scan dari HP** — QR code yang ditempel di barang fisik dapat di-scan menggunakan kamera HP. Tanpa perlu login, langsung menampilkan:
   - Spesifikasi barang (nama, kode, status, kondisi)
   - Foto barang
   - Riwayat peminjaman

3. **Route Publik** — Berikut route yang dapat diakses tanpa login:
   - `GET /asset/{code}` — Detail aset publik
   - `GET /scan` — Halaman scan publik
   - `GET /asset-library/search` — Pencarian aset berdasarkan QR code
   - `GET /asset-photo/{path}` — Proxy foto dari RustFS

---

## Akses dari HP dengan ngrok

Untuk mengakses QR code dari HP di luar jaringan lokal, gunakan ngrok. Panduan lengkap tersimpan di file [NGROK_SETUP_GUIDE.md](NGROK_SETUP_GUIDE.md).

### Ringkasan Cepat

```powershell
# Terminal 1: Jalankan Laravel
php artisan serve --host=0.0.0.0

# Terminal 2: Jalankan ngrok
ngrok http 8000
```

Copy URL ngrok (contoh: `https://abc123.ngrok-free.app`), update di `.env`:

```env
APP_URL=https://abc123.ngrok-free.app
```

Clear cache dan restart server:

```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## Build Frontend (Vite & Tailwind)

### Build untuk Production

```powershell
npm run build
```

Hasil build tersimpan di `public/build/`.

### Development dengan Hot Reload

```powershell
npm run dev
```

Vite akan menjalankan dev server dengan hot module replacement (HMR).

### Konfigurasi

- **Tailwind CSS** — dikonfigurasi via `@tailwindcss/vite` plugin
- **Vite** — dikonfigurasi via `vite.config.js` dengan `laravel-vite-plugin`
- **Entry points** — `resources/css/app.css` dan `resources/js/app.js`

---

## Testing

### Menjalankan Test

```powershell
php artisan test
```

Atau via Composer:

```powershell
composer run test
```

### Konfigurasi Test

Test menggunakan PHPUnit/Pest (sudah termasuk di `composer.json` dev dependencies). Database test default menggunakan SQLite in-memory.

---

## Deployment

### Persiapan Production

1. Set environment ke production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com
```

2. Optimasi aplikasi:

```powershell
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

3. Build frontend:

```powershell
npm run build
```

4. Set permission folder:

```powershell
# Storage dan cache harus writable
# Sesuaikan dengan user web server Anda
```

5. Jalankan migrasi:

```powershell
php artisan migrate --force
```

6. Buat symbolic link storage:

```powershell
php artisan storage:link
```

### Checklist Deployment

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` sesuai domain
- [ ] Database PostgreSQL sudah dikonfigurasi
- [ ] Migrasi sudah dijalankan
- [ ] Seeder sudah dijalankan (untuk data awal)
- [ ] Frontend sudah di-build (`npm run build`)
- [ ] Config/route/view cache sudah dioptimasi
- [ ] Storage link sudah dibuat
- [ ] RustFS sudah dikonfigurasi (atau local storage sebagai fallback)
- [ ] SSL/HTTPS sudah aktif
- [ ] Cron job untuk queue worker sudah diset (jika diperlukan)

---

## Troubleshooting

### Database Connection Refused

- Pastikan PostgreSQL berjalan: `pg_isready -h 127.0.0.1 -p 5432`
- Cek konfigurasi di `.env`: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Pastikan database `simas_db` sudah dibuat

### Error: No Application Encryption Key

```powershell
php artisan key:generate
```

### Foto Aset Tidak Muncul

- Pastikan RustFS berjalan dan dapat diakses di `RUSTFS_ENDPOINT`
- Cek konfigurasi RustFS di `.env` (`RUSTFS_ACCESS_KEY`, `RUSTFS_SECRET_KEY`, `RUSTFS_BUCKET`, `RUSTFS_ENDPOINT`)
- Jika tidak menggunakan RustFS, jalankan `php artisan storage:link` untuk local storage
- Cek route `/asset-photo/{path}` untuk proxy foto

### QR Code Tidak Bisa Di-scan dari HP

- Pastikan `APP_URL` di `.env` sudah sesuai dengan URL ngrok/domain publik
- Clear cache: `php artisan config:clear`
- Restart Laravel server
- Lihat [NGROK_SETUP_GUIDE.md](NGROK_SETUP_GUIDE.md) untuk panduan lengkap

### User Tidak Bisa Login Setelah Dinonaktifkan

- Ini fitur, bukan bug. Admin menonaktifkan user akan otomatis logout user tersebut dari semua device.
- Hubungi admin untuk mengaktifkan kembali akun.

### Migration Error (PostgreSQL)

- Pastikan ekstensi `pdo_pgsql` aktif di PHP
- Cek versi PostgreSQL >= 14
- Jika ada error kolom yang tidak ada, jalankan: `php artisan migrate:fresh --seed`

### Permission Denied (Linux/macOS)

```bash
chmod -R 775 storage bootstrap/cache
```

### Vite Build Error

```powershell
# Hapus node_modules dan install ulang
Remove-Item -Recurse -Force node_modules
npm install
npm run build
```

---

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

## Kontribusi

Untuk kontribusi pada proyek ini:

1. Fork repository
2. Buat branch fitur baru (`git checkout -b feature/nama-fitur`)
3. Commit perubahan (`git commit -m 'Tambah fitur X'`)
4. Push ke branch (`git push origin feature/nama-fitur`)
5. Buat Pull Request

---

> Dibuat untuk **BRIN** — Badan Riset dan Inovasi Nasional
