# Dapur Aisyah - Catering Management System

Dapur Aisyah adalah sebuah sistem informasi berbasis web dan e-commerce sederhana yang dirancang khusus untuk mempermudah manajemen dan pemesanan katering, mencakup layanan Katering Harian dan Katering Acara.

## Overview

Project ini dibuat untuk mendigitalisasi proses pemesanan dari sisi pelanggan dan mempermudah pengelolaan operasional di sisi pengelola. Sistem ini menyelesaikan masalah pemesanan manual, manajemen jadwal menu katering harian, manajemen stok menu katering acara, serta menyediakan proses pembayaran otomatis menggunakan Midtrans Payment Gateway.

## Features

### Fitur Pelanggan
* Pemesanan Katering Harian & Katering Acara.
* Pembayaran online terintegrasi via Midtrans (Mendukung Pembayaran Penuh & Uang Muka / DP).
* Fitur Reschedule (Penjadwalan ulang pengiriman katering).
* Melihat riwayat pesanan secara real-time.
* Mengelola profil dan memberikan ulasan (review) pelayanan.

### Fitur Admin
* Dashboard manajemen pesanan secara real-time.
* Pembaruan status pesanan (Konfirmasi, Proses, Kirim, Selesai).
* Manajemen Jadwal Menu Katering Harian.
* Manajemen Stok Porsi Katering Acara.
* CRUD Menu Katering, Tambahan Lauk Pauk, dan Minuman.

### Fitur Owner
* Dashboard monitoring performa dan pesanan.
* Melihat daftar pelanggan.
* Moderasi (menghapus) ulasan pelanggan.
* Melihat laporan penjualan transaksi katering.
* Cetak atau Ekspor laporan menjadi format PDF.
* Manajemen akun Admin (Tambah, Edit, Hapus akun admin).

## User Roles

Sistem ini memiliki 3 Role Utama yang dibatasi menggunakan Middleware:

1. **Pelanggan (Customer):** Menggunakan fitur utama untuk pemesanan katering dan pembayaran.
2. **Admin:** Mengoperasikan aplikasi, menerima pesanan, mengurus katering dan update menu.
3. **Owner:** Memantau ringkasan pesanan, mencetak laporan, dan mengelola admin.

## Application Flow

1. **Guest Flow:** Pengunjung membuka website -> Memilih tipe layanan (Harian / Acara) -> Memilih lokasi -> Melihat daftar menu yang tersedia.
2. **Customer Flow:** Pelanggan Login -> Mengisi detail form pemesanan -> Membayar via Payment Gateway Midtrans -> Memantau pesanan di halaman Riwayat Pesanan.
3. **Admin Flow:** Admin Login -> Mengecek pesanan baru di Dashboard Admin -> Memperbarui status katering yang sedang diproses.

## Tech Stack

* **Programming Language:** PHP 8.2+
* **Framework:** Laravel 11
* **Frontend:** Blade Templates, Tailwind CSS, Alpine.js
* **Authentication:** Laravel Breeze
* **Database:** SQLite / MySQL / PostgreSQL (Tergantung konfigurasi `.env`)
* **Payment Gateway:** Midtrans (midtrans-php)
* **PDF Generator:** barryvdh/laravel-dompdf
* **Build Tool:** Vite

## Screenshots

*Berikut adalah tampilan sistem dari Dapur Aisyah:*

**Landing Page**  
![Screenshot - Home](docs/screenshots/home.png)

**Halaman Pemesanan & Menu**  
![Screenshot - Menu Pemesanan](docs/screenshots/menu.png)

**Riwayat Pesanan (Pelanggan)**  
![Screenshot - Riwayat Pesanan](docs/screenshots/riwayat-pesanan.png)

**Manajemen Pesanan (Admin)**  
![Screenshot - Admin Pesanan](docs/screenshots/admin-pesanan.png)

**Laporan Penjualan (Owner)**  
![Screenshot - Laporan Owner](docs/screenshots/owner-laporan.png)

## Project Structure

Struktur direktori utama yang digunakan pada project ini:
```text
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/         # Logic untuk fitur Admin
│   │   ├── Owner/         # Logic untuk fitur Owner
│   │   └── Pelanggan/     # Logic untuk fitur Pelanggan
│   └── Models/            # Model Database (Pesanan, Menu, dll)
├── database/              # File Migrations & Seeders
├── resources/
│   └── views/             # Berisi file UI Blade per-role
├── routes/
│   ├── web.php            # File routing aplikasi & Webhook Midtrans
│   └── auth.php           # Routing Authentikasi bawaan
└── public/                # Assets frontend
