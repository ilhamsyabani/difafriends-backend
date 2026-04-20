#!/bin/bash
# ============================================================
# Deploy Script — DifaFriends
# Jalankan setiap kali update kode: bash deploy.sh
# ============================================================

set -e

APP_DIR="/var/www/difafriends"

echo "==> Pull kode terbaru..."
cd $APP_DIR
git pull origin main

echo "==> Install PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Install & build frontend..."
npm ci
npm run build

echo "==> Jalankan migrasi..."
php artisan migrate --force

echo "==> Link storage publik..."
php artisan storage:link --force

echo "==> Clear & cache config..."
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Set permission..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR/storage
chmod -R 755 $APP_DIR/bootstrap/cache

echo "==> Restart queue & pulse worker..."
supervisorctl restart difafriends-worker:*
supervisorctl restart difafriends-pulse:*

echo ""
echo "Deploy selesai!"
