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
php artisan reverb:start
php artisan queue:work
exec apache2-foreground
