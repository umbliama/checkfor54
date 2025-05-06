#!/bin/sh
set -e

echo "Waiting for database connection..."
sleep 5 # Adjust as needed

# Fix permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan storage:link

# Start Supervisor to manage Reverb & Queue
# echo "Starting Supervisor..."
# exec supervisord -c /etc/supervisord.conf
