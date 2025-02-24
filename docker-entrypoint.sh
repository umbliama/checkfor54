#!/bin/sh
set -e

# Wait for database (MySQL/Postgres)
echo "Waiting for database connection..."
sleep 5 # Adjust if needed
ls -la /var/www/

# Fix permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run Laravel commands
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

# Start Reverb and Queue
php artisan reverb:start --host=0.0.0.0 --port=6001 &
php artisan queue:work &

# ✅ Ensure Apache runs in foreground
exec apache2-foreground
