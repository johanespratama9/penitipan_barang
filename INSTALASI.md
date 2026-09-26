# 🚀 Panduan Instalasi — Penitipan Barang (Sarinah Street)

> **Stack:** Laravel 13 · PHP 8.3+ · MySQL · Filament 5 · Tailwind CSS 4

---

## 📋 Daftar Isi

- [Prasyarat](#prasyarat)
- [Instalasi di Komputer Lokal (Development)](#instalasi-lokal)
- [Instalasi di Server (Production)](#instalasi-server)
- [Migrasi Database & Seeder](#migrasi-database--seeder)
- [Export / Import Database MySQL](#export--import-database-mysql)
- [Troubleshooting](#troubleshooting)

---

## Prasyarat

| Kebutuhan | Versi Minimum |
|-----------|--------------|
| PHP | 8.3+ |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |
| MySQL | 8.0+ |
| Git | 2.x |

---

## Instalasi Lokal

> Untuk pengembangan di komputer pribadi / laptop.

### Langkah 1 — Install PHP & Composer

**macOS:**
```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

**Windows (PowerShell):**
```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force
iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

**Linux (Ubuntu/Debian):**
```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

Verifikasi instalasi:
```bash
php -v
composer -V
```

---

### Langkah 2 — Clone Repository

```bash
git clone https://github.com/<username>/penitipan_barang.git
cd penitipan_barang
```

---

### Langkah 3 — Install Dependency PHP

```bash
composer install
```

---

### Langkah 4 — Install Dependency Node.js

```bash
npm install
```

---

### Langkah 5 — Salin File `.env`

```bash
cp .env.example .env
```

---

### Langkah 6 — Konfigurasi `.env` untuk MySQL (Lokal)

Buka file `.env` dan ubah bagian database:

```env
APP_NAME="Sarinah Street"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=penitipan_barang
DB_USERNAME=root
DB_PASSWORD=password_anda
```

> ⚠️ Ganti `DB_USERNAME`, `DB_PASSWORD`, dan `DB_DATABASE` sesuai konfigurasi MySQL lokal Anda.

---

### Langkah 7 — Buat Database MySQL

Masuk ke MySQL dan buat database:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE penitipan_barang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

---

### Langkah 8 — Generate Application Key

```bash
php artisan key:generate
```

---

### Langkah 9 — Jalankan Migrasi & Seeder

```bash
php artisan migrate --seed
```

Atau jika ingin fresh (hapus semua data lama):

```bash
php artisan migrate:fresh --seed
```

---

### Langkah 10 — Buat Symlink Storage

```bash
php artisan storage:link
```

---

### Langkah 11 — Build Asset

```bash
npm run build
```

---

### Langkah 12 — Jalankan Aplikasi

```bash
php artisan serve
```

Akses aplikasi di: **http://localhost:8000**

Panel Admin: **http://localhost:8000/admin**

---

## Instalasi Server

> Untuk deployment di VPS / server production (Ubuntu 22.04 / 24.04).

### Langkah 1 — Update Server & Install Dependensi

```bash
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3 beserta ekstensi yang dibutuhkan
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql \
    php8.3-mbstring php8.3-xml php8.3-zip php8.3-curl \
    php8.3-bcmath php8.3-gd php8.3-intl php8.3-tokenizer \
    php8.3-fileinfo

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install MySQL 8
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql

# Install Nginx
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Install Git
sudo apt install -y git
```

---

### Langkah 2 — Amankan MySQL Server

```bash
sudo mysql_secure_installation
```

Ikuti prompt, disarankan:
- Set root password: **Ya**
- Remove anonymous users: **Ya**
- Disallow root login remotely: **Ya**
- Remove test database: **Ya**
- Reload privilege tables: **Ya**

---

### Langkah 3 — Buat Database & User MySQL di Server

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE penitipan_barang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'penitipan_user'@'localhost' IDENTIFIED BY 'Password_Kuat_123!';
GRANT ALL PRIVILEGES ON penitipan_barang.* TO 'penitipan_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

> 🔐 Ganti `Password_Kuat_123!` dengan password yang kuat dan simpan baik-baik.

---

### Langkah 4 — Clone Repository ke Server

```bash
cd /var/www
sudo git clone https://github.com/<username>/penitipan_barang.git
sudo chown -R www-data:www-data /var/www/penitipan_barang
sudo chmod -R 755 /var/www/penitipan_barang
```

---

### Langkah 5 — Install Dependency di Server

```bash
cd /var/www/penitipan_barang

sudo -u www-data composer install --no-dev --optimize-autoloader

sudo -u www-data npm install
```

---

### Langkah 6 — Konfigurasi `.env` di Server

```bash
sudo cp .env.example .env
sudo nano .env
```

Isi konfigurasi untuk **production**:

```env
APP_NAME="Sarinah Street"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=penitipan_barang
DB_USERNAME=penitipan_user
DB_PASSWORD=Password_Kuat_123!

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

---

### Langkah 7 — Generate Application Key

```bash
sudo -u www-data php artisan key:generate
```

---

### Langkah 8 — Jalankan Migrasi & Seeder

```bash
sudo -u www-data php artisan migrate --seed --force
```

---

### Langkah 9 — Build Asset & Optimasi

```bash
sudo -u www-data npm run build

sudo -u www-data php artisan storage:link
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache
```

---

### Langkah 10 — Atur Permission Storage & Bootstrap

```bash
sudo chown -R www-data:www-data /var/www/penitipan_barang/storage
sudo chown -R www-data:www-data /var/www/penitipan_barang/bootstrap/cache
sudo chmod -R 775 /var/www/penitipan_barang/storage
sudo chmod -R 775 /var/www/penitipan_barang/bootstrap/cache
```

---

### Langkah 11 — Konfigurasi Nginx

```bash
sudo nano /etc/nginx/sites-available/penitipan_barang
```

Isi dengan konfigurasi berikut (ganti `domain-anda.com`):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name domain-anda.com www.domain-anda.com;

    root /var/www/penitipan_barang/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi:

```bash
sudo ln -s /etc/nginx/sites-available/penitipan_barang /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### Langkah 12 — (Opsional) SSL dengan Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.com -d www.domain-anda.com
```

---

## Migrasi Database & Seeder

| Perintah | Kegunaan |
|----------|----------|
| `php artisan migrate` | Jalankan semua migrasi baru |
| `php artisan migrate --seed` | Migrasi + isi data awal |
| `php artisan migrate:fresh --seed` | Reset total + migrasi ulang + seeder |
| `php artisan migrate:rollback` | Batalkan migrasi terakhir |
| `php artisan db:seed` | Jalankan seeder saja |

> ⚠️ Jangan gunakan `migrate:fresh` di server production karena akan **menghapus semua data**.

---

## Export / Import Database MySQL

### Export Database (dari komputer asal)

```bash
mysqldump -u root -p penitipan_barang > backup_penitipan_$(date +%Y%m%d).sql
```

### Transfer ke Server

```bash
# Menggunakan SCP
scp backup_penitipan_*.sql user@ip-server:/tmp/

# Atau menggunakan rsync
rsync -avz backup_penitipan_*.sql user@ip-server:/tmp/
```

### Import ke Server

```bash
# Login ke server
ssh user@ip-server

# Import database
mysql -u penitipan_user -p penitipan_barang < /tmp/backup_penitipan_*.sql
```

### Sync dari Server ke Lokal (Pull)

```bash
# Export dari server
ssh user@ip-server "mysqldump -u penitipan_user -p'Password_Kuat_123!' penitipan_barang" > backup_dari_server.sql

# Import ke lokal
mysql -u root -p penitipan_barang < backup_dari_server.sql
```

---

## Update Aplikasi di Server (Deploy Ulang)

Setiap ada perubahan kode baru, jalankan perintah berikut di server:

```bash
cd /var/www/penitipan_barang

# Aktifkan maintenance mode
sudo -u www-data php artisan down

# Pull kode terbaru
sudo git pull origin main

# Update dependency
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install

# Build asset terbaru
sudo -u www-data npm run build

# Jalankan migrasi baru (jika ada)
sudo -u www-data php artisan migrate --force

# Clear & rebuild cache
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan optimize

# Nonaktifkan maintenance mode
sudo -u www-data php artisan up
```

---

## Troubleshooting

### ❌ Error: `SQLSTATE[HY000] [1045] Access denied`

Periksa kembali `DB_USERNAME` dan `DB_PASSWORD` di file `.env`. Pastikan user MySQL memiliki akses ke database yang ditentukan.

### ❌ Error: `php_network_getaddresses: getaddrinfo failed`

Pastikan `DB_HOST` benar. Untuk lokal gunakan `127.0.0.1`, bukan `localhost`.

### ❌ Error: `storage/logs` tidak bisa ditulis

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### ❌ Error: `No application encryption key`

```bash
php artisan key:generate
```

### ❌ Halaman admin `/admin` tidak ditemukan

Pastikan seeder sudah dijalankan dan user admin sudah dibuat:
```bash
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=RoleSeeder
```

### ❌ Asset CSS/JS tidak muncul

```bash
npm run build
php artisan storage:link
```

---

## 👤 Akun Default (Setelah Seeder)

Cek file `database/seeders/AdminUserSeeder.php` untuk melihat email dan password default admin.

> 🔐 **Segera ganti password default setelah pertama kali login di server production.**

---

*Dibuat untuk project Penitipan Barang — Sarinah Street*

