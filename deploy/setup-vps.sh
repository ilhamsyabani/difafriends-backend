#!/bin/bash
# ============================================================
# VPS Setup Script — DifaFriends
# Ubuntu 22.04 / 24.04
# Jalankan sebagai root: bash setup-vps.sh
# ============================================================

set -e

DOMAIN="domain-kamu.id"          # Ganti dengan domain kamu
DB_NAME="difafriends"
DB_USER="difafriends_user"
DB_PASS="ganti_dengan_password_aman"  # Ganti!
APP_DIR="/var/www/difafriends"

echo "==> [1/8] Update sistem..."
apt update && apt upgrade -y
apt install -y curl git unzip ufw fail2ban

# ============================================================
echo "==> [2/8] Install PHP 8.4..."
# ============================================================
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.4-fpm php8.4-cli php8.4-common \
    php8.4-pgsql php8.4-mbstring php8.4-xml php8.4-zip \
    php8.4-curl php8.4-gd php8.4-intl php8.4-bcmath \
    php8.4-tokenizer php8.4-fileinfo

# ============================================================
echo "==> [3/8] Install PostgreSQL 16..."
# ============================================================
apt install -y postgresql postgresql-contrib
systemctl enable postgresql
systemctl start postgresql

# Buat database & user
sudo -u postgres psql <<EOF
CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';
CREATE DATABASE $DB_NAME OWNER $DB_USER;
GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;
EOF

# ============================================================
echo "==> [4/8] Install Redis..."
# ============================================================
apt install -y redis-server
systemctl enable redis-server
systemctl start redis-server

# ============================================================
echo "==> [5/8] Install Nginx..."
# ============================================================
apt install -y nginx
systemctl enable nginx

# Konfigurasi Nginx
cat > /etc/nginx/sites-available/difafriends <<NGINX
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $APP_DIR/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Upload max size (untuk video thumbnail, bukan video)
    client_max_body_size 50M;
}
NGINX

ln -s /etc/nginx/sites-available/difafriends /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

# ============================================================
echo "==> [6/8] Install Composer & Node.js..."
# ============================================================
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# ============================================================
echo "==> [7/8] Setup Firewall..."
# ============================================================
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# ============================================================
echo "==> [8/8] Setup Supervisor (Queue Worker)..."
# ============================================================
apt install -y supervisor

cat > /etc/supervisor/conf.d/difafriends-worker.conf <<SUPERVISOR
[program:difafriends-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $APP_DIR/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=$APP_DIR/storage/logs/worker.log
stopwaitsecs=3600

[program:difafriends-pulse]
process_name=%(program_name)s_%(process_num)02d
command=php $APP_DIR/artisan pulse:work
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=$APP_DIR/storage/logs/pulse.log
stopwaitsecs=3600

[program:difafriends-schedule]
process_name=%(program_name)s
command=php $APP_DIR/artisan schedule:work
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=$APP_DIR/storage/logs/schedule.log
SUPERVISOR

supervisorctl reread
supervisorctl update

echo ""
echo "============================================================"
echo " Setup selesai! Lanjutkan dengan:"
echo " 1. Upload kode ke $APP_DIR"
echo " 2. Jalankan: bash deploy.sh"
echo " 3. Pasang SSL: certbot --nginx -d $DOMAIN"
echo "============================================================"
