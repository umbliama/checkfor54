#!/bin/sh
set -e

echo "Waiting for database connection..."
sleep 5 # Adjust as needed

# Fix permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Start services
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Start Reverb (WebSockets)
php artisan reverb:start --port=6001 &

# Start Laravel Queue
php artisan queue:work &

# Start Apache
exec apache2-foreground
