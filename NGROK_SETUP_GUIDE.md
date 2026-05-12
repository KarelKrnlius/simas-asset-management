# Panduan Lengkap: Akses QR Code Asset dari HP Menggunakan ngrok

Panduan ini menjelaskan cara menjalankan aplikasi SIMAS Asset Management agar QR code yang discan dari HP langsung membuka detail asset tanpa perlu login.

---

## 1. Persiapan Awal

### Pastikan yang sudah terinstall:
- **Laravel** sudah berjalan di laptop
- **ngrok** sudah terinstall (minimal versi 3.20)
- Aplikasi SIMAS sudah bisa diakses di `http://localhost:8000`

---

## 2. Daftar Akun ngrok (Gratis)

1. Buka browser, akses: https://dashboard.ngrok.com/signup
2. Daftar menggunakan email atau akun Google/GitHub
3. Setelah login, buka: https://dashboard.ngrok.com/get-started/your-authtoken
4. Copy **Authtoken** yang muncul (contoh: `2L...abc`)

---

## 3. Install Authtoken ngrok

Buka terminal (PowerShell/CMD) di laptop, lalu jalankan:

```powershell
ngrok config add-authtoken 2L...abc
```

> Ganti `2L...abc` dengan authtoken yang kamu copy dari dashboard ngrok.

Kalau berhasil, akan muncul pesan seperti:
```
Authtoken saved to configuration file
```

---

## 4. Update ngrok ke Versi Terbaru

Kalau ngrok versi lamamu tidak bisa jalan, update dulu:

```powershell
ngrok update
```

Tunggu sampai selesai. Pastikan versi minimal **3.20** atau lebih baru.

---

## 5. Jalankan Laravel Server

Buka terminal, masuk ke folder project, lalu jalankan:

```powershell
php artisan serve --host=0.0.0.0
```

> Pastikan server berjalan di `http://127.0.0.1:8000`

**Jangan tutup terminal ini!** Biarkan server tetap berjalan.

---

## 6. Jalankan ngrok Tunnel

Buka **terminal baru** (jangan tutup terminal Laravel), lalu jalankan:

```powershell
ngrok http 8000
```

Tunggu beberapa detik, akan muncul output seperti ini:

```
Session Status                online
Account                       Nama Kamu (Plan: Free)
Version                       3.39.1
Region                        United States (us)
Web Interface                 http://127.0.0.1:4040
Forwarding                    https://abc123-def456.ngrok-free.app -> http://localhost:8000
```

**Copy URL yang diawali `https://`**  
Contoh: `https://abc123-def456.ngrok-free.app`

**Jangan tutup terminal ngrok!** Biarkan tetap berjalan selama demo.

---

## 7. Update URL Aplikasi di File `.env`

1. Buka file `.env` di root folder project (bukan `.env.example`)
2. Cari baris:
   ```env
   APP_URL=http://localhost
   ```
3. Ganti dengan URL ngrok yang tadi dicopy:
   ```env
   APP_URL=https://abc123-def456.ngrok-free.app
   ```

> Ganti `abc123-def456.ngrok-free.app` dengan URL ngrok kamu sendiri.

---

## 8. Clear Cache Laravel

Setelah mengubah `.env`, jalankan di terminal (terminal baru atau terminal yang sama dengan server):

```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 9. Restart Laravel Server

1. Di terminal Laravel, tekan `Ctrl+C` untuk matikan server
2. Jalankan ulang:
   ```powershell
   php artisan serve --host=0.0.0.0
   ```

---

## 10. Generate QR Code Asset

1. Buka browser, akses aplikasi SIMAS melalui URL ngrok:
   ```
   https://abc123-def456.ngrok-free.app/login
   ```
2. Login sebagai admin
3. Buka menu **Master Asset** atau **QR Generator**
4. Klik tombol **Generate QR** pada asset yang ingin diprint
5. QR code sekarang berisi URL publik seperti:
   ```
   https://abc123-def456.ngrok-free.app/asset/BRIN-01-000001
   ```

---

## 11. Print QR Code dan Tempel di Barang

1. Klik **Download QR** pada modal yang muncul
2. Save file PNG
3. Print QR code menggunakan printer
 
4. Tempel/tempelkan QR code pada barang fisik (laptop, PC, mouse, dll)

---

## 12. Scan dari HP

1. Ambil HP (bisa pakai WiFi berbeda atau mobile data)
2. Buka aplikasi kamera atau QR scanner
3. Scan QR code yang menempel di barang
4. HP akan langsung membuka browser dan menampilkan:
   - **Spesifikasi barang** (nama, kode, status, kondisi)
   - **Foto barang**
   - **Riwayat peminjaman** (siapa saja yang pernah pinjam)

> **Tidak perlu login!** Langsung terbuka halaman detail asset.

---

## 13. Cek ngrok Dashboard (Opsional)

Buka di browser laptop:
```
http://127.0.0.1:4040
```

Disini bisa melihat:
- Request yang masuk dari HP
- Status tunnel
- URL yang sedang aktif

---

## Catatan Penting

### URL ngrok Berubah Setiap Session
| Kondisi | Hasil |
|---------|-------|
| Tutup terminal ngrok | URL berubah saat dijalankan ulang |
| Laptop restart | URL berubah |
| Session timeout (2 jam) | URL tidak aktif lagi |

**Solusi:** Kalau URL berubah, ulangi langkah **6-9** dan update URL baru di file `.env` dan QR code.

### Alternatif: Hotspot Laptop (Tanpa ngrok)
Kalau semua device ada di dekat laptop dan ingin lebih simpel:
1. Laptop buat hotspot WiFi
2. HP connect ke hotspot laptop
3. Gunakan IP laptop (contoh: `http://192.168.137.1:8000`)
4. QR code berisi IP tersebut

### Solusi Permanent: Deploy ke Hosting
Untuk penggunaan jangka panjang tanpa ngrok, deploy aplikasi ke hosting:
- Hostinger
- Niagahoster
- Heroku
- DigitalOcean

Setelah deploy, QR code otomatis menggunakan domain publik (contoh: `https://simas-sekolahku.com/asset/BRIN-01-000001`)

---

## Troubleshooting

### Error: "Your ngrok-agent version is too old"
```powershell
ngrok update
```

### Error: "Usage of ngrok requires a verified account"
Daftar akun ngrok gratis di https://dashboard.ngrok.com/signup lalu install authtoken (langkah 3).

### Error: "Connection refused" saat scan dari HP
- Pastikan Laravel server berjalan (`php artisan serve`)
- Pastikan ngrok berjalan (`ngrok http 8000`)
- Pastikan URL di `.env` sudah sesuai dengan URL ngrok

### Foto tidak muncul di HP
Foto dari RustFS sudah di-handle dengan route proxy `/asset-photo/`. Pastikan Laravel server dan ngrok tetap berjalan.

---

## Ringkasan Perintah Terminal

| Terminal 1 | Terminal 2 |
|---|---|
| `php artisan serve --host=0.0.0.0` | `ngrok http 8000` |
| Jangan ditutup | Jangan ditutup |

Dua terminal ini harus tetap berjalan selama demo/presentasi.

---

**Selamat mencoba!** Kalau ada kendala, screenshot error-nya dan tanyakan.
