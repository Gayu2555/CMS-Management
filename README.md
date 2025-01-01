# CMS Management Portal Berita

Proyek ini adalah CMS (Content Management System) untuk mengelola portal berita, dibuat menggunakan PHP sebagai backend dan TailwindCSS untuk styling. CMS ini dirancang untuk fleksibilitas pengelolaan berita berdasarkan kategori, dengan dukungan untuk subdomain dan manajemen artikel di berbagai level, seperti News List, Sub Headline, dan Headline.

## Fitur Utama

- **Manajemen Artikel**: CRUD (Create, Read, Update, Delete) artikel dengan penentuan kategori, status (News List, Sub Headline, Headline), dan pengelolaan slug.
- **Manajemen Kategori**: Dukungan kategori fleksibel, memungkinkan penambahan kategori baru sesuai kebutuhan (contoh: Teknologi, Perang Rusia Ukraina, dll).
- **Subdomain untuk Isolasi Database**: Setiap kategori dapat memiliki subdomain berbeda untuk mengisolasi database (contoh: `politics.urbansiana.com`).
- **Dashboard Terpadu**: Semua kategori dikelola melalui satu dashboard pusat.
- **Responsive Design**: Menggunakan TailwindCSS untuk memastikan antarmuka ramah perangkat mobile.

## Teknologi yang Digunakan

- **Backend**: PHP
- **Frontend**: TailwindCSS
- **Database**: MySQL / MariaDB
- **Web Server**: Nginx atau Apache

## Instalasi

### Prasyarat

- PHP >= 8.0
- Composer
- Node.js & npm (untuk build TailwindCSS)
- MySQL / MariaDB
- Web server seperti Nginx atau Apache

### Langkah Instalasi

1. Clone repositori ini:
   ```bash
   git clone https://github.com/username/cms-management-portal.git
   cd cms-management-portal
   ```

2. Install dependensi PHP dengan Composer:
   ```bash
   composer install
   ```

3. Install dependensi frontend dengan npm:
   ```bash
   npm install
   npm run build
   ```

4. Konfigurasi file `.env`:
   Salin file `.env.example` menjadi `.env` dan sesuaikan isinya:
   ```env
   APP_NAME=CMS Portal Berita
   APP_URL=http://localhost
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=username_database
   DB_PASSWORD=password_database
   ```

5. Migrasi database:
   ```bash
   php artisan migrate
   ```

6. Jalankan server lokal:
   ```bash
   php artisan serve
   ```

7. Akses aplikasi di [http://localhost:8000](http://localhost:8000).

## Struktur Direktori

```plaintext
.
├── app            # Logic aplikasi
├── public         # Direktori untuk assets publik
├── resources      # Views dan file TailwindCSS
├── routes         # File rute aplikasi
├── database       # File migrasi dan seeder
├── .env.example   # Template file konfigurasi
└── README.md      # Dokumentasi proyek
```

## Kontribusi

Kami menyambut kontribusi dari siapa saja! Untuk kontribusi, silakan fork repositori ini dan kirimkan pull request.

## Lisensi

Proyek ini dirilis di bawah lisensi [MIT](LICENSE).

---

Dikembangkan dengan ❤ oleh tim pengembang.
