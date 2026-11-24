# Dapur Bucik — Aplikasi Pemesanan Kuliner & Manajemen Dapur

Aplikasi web pemesanan makanan rumahan dan manajemen operasional dapur berbasis **Laravel 13**, **MySQL**, **Tailwind CSS v4**, dan **Vite**. Mendukung layanan **Dine-in (Scan QR Meja)**, **Pickup (Ambil Sendiri)**, dan **Kurir Mandiri**, dilengkapi panel admin lengkap untuk pengelolaan menu, pesanan, meja QR, pelanggan, hingga laporan penjualan PDF/Excel.

---

## Fitur Utama

### Sisi Pelanggan
- **Beranda & Katalog Menu:** Pencarian menu, filter kategori (*Masakan Rumah*, *Cemilan Tradisional*, *Minuman Segar*, *Menu Anak*), pengurutan harga/terlaris/rating, dan tampilan detail menu.
- **Scan QR Meja Dine-in:** Mengakses `/menu?meja={id}` otomatis menyimpan nomor meja ke dalam session sehingga langsung terpilih pada saat Checkout.
- **Keranjang Belanja:** Tambah item dengan jumlah (`qty`) dan catatan khusus, ubah jumlah, hapus item, serta perhitungan subtotal otomatis menggunakan `harga_promo` apabila tersedia.
- **Checkout & Pembayaran:** Penyimpanan pesanan secara atomik (`DB::transaction`), pilihan metode penerimaan (*Dine-in*, *Pickup*, *Kurir Mandiri*), instruksi pembayaran QRIS/Transfer Bank/Tunai (tunai khusus *Dine-in* & *Pickup*), serta konfirmasi pembayaran.
- **Pelacakan & Riwayat Pesanan:** Pemantauan status pesanan 4 tahap (*Menunggu Konfirmasi* → *Dimasak* → *Siap Diambil / Menunggu Driver* → *Selesai*) beserta riwayat transaksi.
- **Dashboard & Profil (`/dashboard`):** Ubah nama panggilan, nomor WhatsApp, foto profil (otomatis dikonversi ke `.webp`), ganti kata sandi, dan CRUD buku alamat pengiriman.

### Sisi Admin (`/admin/login` & `/admin/dashboard`)
- **Dashboard Operasional:** Ringkasan KPI real-time (pesanan masuk hari ini, total pendapatan terverifikasi, pesanan aktif, menu habis), grafik pendapatan mingguan (Chart.js), dan 3 menu terlaris.
- **Manajemen Menu & Kategori:** CRUD menu lengkap, pengaturan harga promo, unggah foto (otomatis dikonversi ke `.webp`), serta toggle cepat ketersediaan (`tersedia`/`habis`).
- **Manajemen Pesanan & Struk Dapur:** Filter pesanan, ubah alur status pesanan, tolak pesanan dengan alasan, konfirmasi pembayaran lunas, dan cetak struk dapur (`/admin/pesanan/{id}/struk`).
- **Manajemen Meja & Generator QR Code:** CRUD meja dine-in dengan pembuatan QR Code SVG otomatis yang mengarah ke `/menu?meja={id}` serta fitur cetak kartu QR.
- **Buku Pelanggan:** Daftar pelanggan, jumlah transaksi, serta fitur blokir / buka blokir akun.
- **Laporan Keuangan & Penjualan:** Filter rentang tanggal, grafik garis/batang, tabel menu terlaris, serta ekspor ke **PDF** (DomPDF) dan **Excel/CSV**.

---

## Teknologi yang Digunakan

- **Backend:** PHP 8.2+ (teruji pada PHP 8.5), Laravel 13
- **Database:** MySQL 8+ (10 tabel sesuai ERD: `users`, `alamat`, `kategori`, `menu`, `keranjang`, `item_keranjang`, `meja`, `pesanan`, `detail_pesanan`, `pembayaran`)
- **Frontend:** Laravel Blade, Tailwind CSS v4, Vite, Chart.js
- **Package Pendukung:** `barryvdh/laravel-dompdf`, `simplesoftwareio/simple-qrcode`

---

## Prasyarat Sistem

Pastikan perangkat Anda sudah terpasang:
- **PHP >= 8.2** (dengan ekstensi `pdo_mysql`, `gd`, `mbstring`, `fileinfo`, `xml`)
- **Composer**
- **Node.js >= 18** & **NPM**
- **MySQL / MariaDB** (bisa melalui Laragon, XAMPP, atau MySQL Server)

---

## Panduan Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/Farell-max/dapur-bucik.git
   cd dapur-bucik
   ```

2. **Install dependensi backend & frontend**
   ```bash
   composer install
   npm install
   ```

3. **Salin file environment dan buat Application Key**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database pada file `.env`**
   Buat database MySQL baru bernama `dapur_bucik`, lalu sesuaikan pengaturan koneksi di `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dapur_bucik
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Jalankan migrasi, seeder data awal, dan storage symlink**
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

6. **Kompilasi aset frontend**
   ```bash
   npm run build
   ```

7. **Jalankan server pengembangan**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui `http://127.0.0.1:8000`.

---

## Akun Default (Hasil Seeder)

Setelah menjalankan `php artisan migrate --seed`, akun berikut siap digunakan untuk pengujian:

| Peran | Email | Kata Sandi | Status Akun | Halaman Login |
|---|---|---|---|---|
| **Admin** | `admindapurbucik@gmail.com` | `password` | Aktif | `/admin/login` |
| **Pelanggan** | `budisantoso@gmail.com` | `password` | Aktif | `/login` |
| **Pelanggan (Diblokir)** | `ismailpratama@gmail.com` | `password` | Diblokir | `/login` (untuk uji penolakan login) |

---

## Rute Penting Aplikasi

- **Landing Page / Beranda:** `/`
- **Katalog Menu:** `/menu` (mendukung scan QR meja: `/menu?meja=1`)
- **Login & Register Pelanggan:** `/login`, `/register`
- **Dashboard / Profil Pelanggan:** `/dashboard` atau `/profil`
- **Keranjang, Checkout, Riwayat:** `/keranjang`, `/checkout`, `/riwayat`
- **Login & Panel Admin:** `/admin/login`, `/admin/dashboard`

---

## Pengujian Otomatis

Untuk menjalankan seluruh pengujian fitur & skema (menggunakan SQLite in-memory sesuai `phpunit.xml`):

```bash
php artisan test
```

Untuk memeriksa standar gaya penulisan kode PHP (Laravel Pint):

```bash
php vendor/bin/pint --test
```
