# Integrasi RustFS untuk Foto Asset

## Ringkasan Perubahan

Foto asset sekarang disimpan ke **RustFS (Object Storage)** menggunakan bucket `master-asset`.

### Strategi Implementasi
- ✅ **Foto BARU** → Disimpan ke RustFS
- ✅ **Foto LAMA** → Tetap di local storage (tidak diubah)
- ✅ **Support keduanya** → Sistem bisa baca dari RustFS atau local storage

---

## File yang Diubah

### 1. `.env`
Menambahkan konfigurasi RustFS:
```env
RUSTFS_ACCESS_KEY=admin
RUSTFS_SECRET_KEY=admin123
RUSTFS_REGION=us-east-1
RUSTFS_BUCKET=master-asset
RUSTFS_ENDPOINT=http://127.0.0.1:9000
RUSTFS_USE_PATH_STYLE_ENDPOINT=true
RUSTFS_URL=http://127.0.0.1:9000/master-asset
```

### 2. `config/filesystems.php`
Menambahkan disk `rustfs`:
```php
'rustfs' => [
    'driver' => 's3',
    'key' => env('RUSTFS_ACCESS_KEY'),
    'secret' => env('RUSTFS_SECRET_KEY'),
    'region' => env('RUSTFS_REGION', 'us-east-1'),
    'bucket' => env('RUSTFS_BUCKET'),
    'url' => env('RUSTFS_URL'),
    'endpoint' => env('RUSTFS_ENDPOINT'),
    'use_path_style_endpoint' => env('RUSTFS_USE_PATH_STYLE_ENDPOINT', true),
],
```

### 3. `app/Http/Controllers/AssetController.php`
- Method `store()`: Upload foto baru ke RustFS
- Method `update()`: Upload foto baru ke RustFS, hapus foto lama dari RustFS
- Method `show()`: Return URL foto (support local & RustFS)

### 4. `app/Helpers/AssetHelper.php` (BARU)
Helper untuk mendapatkan URL foto:
- Cek di RustFS dulu (foto baru)
- Jika tidak ada, cek di local storage (foto lama)
- Return URL yang sesuai

### 5. `composer.json`
Menambahkan autoload helper

### 6. `resources/views/assets/master-asset.blade.php`
- Menggunakan helper untuk mendapatkan URL foto
- JavaScript `editAsset()` menggunakan `photo_url` dari API

---

## Cara Kerja

### Upload Foto Baru (Tambah Asset)
1. User pilih foto
2. Foto diupload ke RustFS bucket `master-asset`
3. Path disimpan di database: `assets/1234567890_abc123.jpg`

### Upload Foto Baru (Edit Asset)
1. User pilih foto baru
2. Sistem hapus foto lama dari RustFS (jika ada)
3. Foto baru diupload ke RustFS
4. Path baru disimpan di database

### Tampilkan Foto
1. Sistem cek apakah foto ada di RustFS
2. Jika ada → return URL RustFS: `http://127.0.0.1:9000/master-asset/assets/xxx.jpg`
3. Jika tidak ada → cek local storage → return URL local: `/storage/assets/xxx.jpg`

---

## Instalasi & Setup

### 1. Install AWS SDK (jika belum)
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### 2. Regenerate Autoload
```bash
composer dump-autoload
```

### 3. Clear Config Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Test Upload
- Buka halaman Master Asset
- Klik "Tambah Aset"
- Upload foto
- Cek di RustFS browser: http://127.0.0.1:9001
- Bucket `master-asset` → folder `assets` → foto ada disana

---

## Troubleshooting

### Error: "Class 'League\Flysystem\AwsS3V3\AwsS3V3Adapter' not found"
**Solusi:**
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
composer dump-autoload
```

### Error: "Unable to connect to RustFS"
**Cek:**
1. RustFS service berjalan di port 9000
2. Kredensial di `.env` benar (admin/admin123)
3. Bucket `master-asset` sudah dibuat

### Foto lama tidak muncul
**Normal!** Foto lama masih di local storage. Sistem akan otomatis cek local storage jika tidak ada di RustFS.

### Foto baru tidak muncul
**Cek:**
1. Upload berhasil ke RustFS (cek di browser http://127.0.0.1:9001)
2. Path tersimpan di database
3. RustFS URL di `.env` benar

---

## Migrasi Foto Lama (Opsional)

Jika ingin memindahkan foto lama ke RustFS:

```php
// Buat command artisan
php artisan make:command MigratePhotosToRustFS

// Di handle():
$assets = Asset::whereNotNull('photo')->get();
foreach ($assets as $asset) {
    $localPath = storage_path('app/public/' . $asset->photo);
    if (file_exists($localPath)) {
        Storage::disk('rustfs')->put($asset->photo, file_get_contents($localPath));
        echo "Migrated: {$asset->photo}\n";
    }
}
```

**PERINGATAN:** Backup database dan foto sebelum migrasi!

---

## Keamanan

- ✅ Kredensial disimpan di `.env` (tidak di version control)
- ✅ Bucket `master-asset` hanya untuk foto asset
- ✅ Validasi file: max 2MB, format JPG/PNG/JPEG
- ✅ Nama file unique: timestamp + uniqid

---

## Monitoring

### Cek Foto di RustFS
1. Buka browser: http://127.0.0.1:9001
2. Login: admin / admin123
3. Bucket: master-asset
4. Folder: assets

### Cek Foto di Database
```sql
SELECT id, name, photo FROM assets WHERE photo IS NOT NULL;
```

---

## Backup & Restore

### Backup Bucket RustFS
```bash
# Menggunakan mc (MinIO Client)
mc mirror rustfs/master-asset ./backup/master-asset
```

### Restore Bucket RustFS
```bash
mc mirror ./backup/master-asset rustfs/master-asset
```

---

## Status Implementasi

- ✅ Konfigurasi RustFS
- ✅ Upload foto baru ke RustFS
- ✅ Edit foto (hapus lama, upload baru)
- ✅ Tampilkan foto (support local & RustFS)
- ✅ Helper untuk URL foto
- ✅ Tidak merusak foto lama
- ✅ Dokumentasi lengkap

**Implementasi selesai dan aman!** 🎉
