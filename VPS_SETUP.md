# Panduan Setup VPS — DifaFriends

Stack: PHP 8.4 · PostgreSQL · Redis · Nginx · Supervisor  
Target OS: Ubuntu 22.04 / 24.04 LTS

---

## 1. Persiapan Awal

```bash
# Login sebagai root, lalu buat user baru
adduser difafriends
usermod -aG sudo difafriends

# Pindah ke user baru
su - difafriends

# Update sistem
sudo apt update && sudo apt upgrade -y
```

---

## 2. Install PHP 8.4

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y \
  php8.4-fpm \
  php8.4-cli \
  php8.4-pgsql \
  php8.4-redis \
  php8.4-mbstring \
  php8.4-xml \
  php8.4-bcmath \
  php8.4-curl \
  php8.4-zip \
  php8.4-gd \
  php8.4-intl \
  php8.4-opcache
```

### Konfigurasi PHP-FPM

Edit `/etc/php/8.4/fpm/pool.d/www.conf`:

```ini
pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
```

> Untuk server 2 vCPU 8 GB RAM, nilai di atas sudah optimal.

```bash
sudo systemctl enable php8.4-fpm
sudo systemctl start php8.4-fpm
```

---

## 3. Install PostgreSQL

```bash
sudo apt install -y postgresql postgresql-contrib

# Buat database dan user
sudo -u postgres psql <<EOF
CREATE DATABASE difafriends;
CREATE USER difafriends_user WITH ENCRYPTED PASSWORD 'ganti_password_kuat';
GRANT ALL PRIVILEGES ON DATABASE difafriends TO difafriends_user;
ALTER DATABASE difafriends OWNER TO difafriends_user;
EOF
```

---

## 4. Install Redis

```bash
sudo apt install -y redis-server

# Edit /etc/redis/redis.conf
# Ubah: supervised no → supervised systemd
sudo sed -i 's/supervised no/supervised systemd/' /etc/redis/redis.conf

sudo systemctl enable redis-server
sudo systemctl restart redis-server
```

---

## 5. Install Nginx

```bash
sudo apt install -y nginx

# Hapus config default
sudo rm /etc/nginx/sites-enabled/default
```

Buat file `/etc/nginx/sites-available/difafriends`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/difafriends/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 50M;
}
```

```bash
sudo ln -s /etc/nginx/sites-available/difafriends /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl enable nginx
sudo systemctl restart nginx
```

---

## 6. Install Node.js (untuk build frontend)

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 7. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 8. Deploy Aplikasi

```bash
# Buat direktori
sudo mkdir -p /var/www/difafriends
sudo chown -R difafriends:www-data /var/www/difafriends
sudo chmod -R 755 /var/www/difafriends

# Clone repo
cd /var/www
git clone https://github.com/username/difafriends.git difafriends
cd difafriends

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Edit .env (lihat seksi konfigurasi .env di bawah)
nano .env

# Migrasi database
php artisan migrate --force

# Optimasi Laravel
php artisan optimize
php artisan storage:link

# Permission storage
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 9. Konfigurasi .env Production

```env
APP_NAME="DifaFriends"
APP_ENV=production
APP_KEY=            # di-generate otomatis
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=difafriends
DB_USERNAME=difafriends_user
DB_PASSWORD=ganti_password_kuat

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Pulse (aktifkan setelah server stabil)
PULSE_ENABLED=false

# Midtrans Production
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=true

# Storage (Cloudflare R2 atau S3)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=
AWS_ENDPOINT=
AWS_URL=
```

---

## 10. Setup Supervisor (Queue Worker)

```bash
sudo apt install -y supervisor
```

Buat file `/etc/supervisor/conf.d/difafriends-worker.conf`:

```ini
[program:difafriends-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/difafriends/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/difafriends/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start difafriends-worker:*
```

---

## 11. Setup SSL (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx

sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Certbot otomatis update Nginx config untuk HTTPS
# Auto-renewal sudah aktif via systemd timer
```

---

## 12. Setup Scheduler (Cron)

```bash
sudo crontab -u www-data -e

# Tambahkan baris ini:
* * * * * cd /var/www/difafriends && php artisan schedule:run >> /dev/null 2>&1
```

---

## 13. Script Deploy Ulang (Update)

Simpan sebagai `/var/www/difafriends/deploy.sh`:

```bash
#!/bin/bash
set -e

cd /var/www/difafriends

echo "→ Pull latest code..."
git pull origin main

echo "→ Install PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "→ Install & build frontend..."
npm ci && npm run build

echo "→ Run migrations..."
php artisan migrate --force

echo "→ Clear & optimize..."
php artisan optimize:clear
php artisan optimize

echo "→ Restart queue workers..."
sudo supervisorctl restart difafriends-worker:*

echo "✓ Deploy selesai"
```

```bash
chmod +x /var/www/difafriends/deploy.sh
```

> Untuk menjalankan deploy: `bash /var/www/difafriends/deploy.sh`

---

## 14. Setelah Server Stabil

Aktifkan kembali Pulse di `.env`:

```env
PULSE_ENABLED=true
```

Lalu jalankan:

```bash
php artisan optimize:clear
php artisan optimize
php artisan pulse:check   # pastikan berjalan
```

---

## Checklist Akhir

- [ ] Domain sudah diarahkan ke IP VPS (A record)
- [ ] SSL aktif dan auto-renewal berjalan
- [ ] Queue worker berjalan (`supervisorctl status`)
- [ ] Scheduler aktif (crontab)
- [ ] `APP_DEBUG=false` di production
- [ ] `PULSE_ENABLED=false` saat awal (aktifkan setelah stabil)
- [ ] File permission storage sudah benar
- [ ] Midtrans key sudah diganti ke production
