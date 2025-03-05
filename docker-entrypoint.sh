#!/bin/sh
set -e

if [ ! -f /var/www/.env ]; then
  cat <<EOF > /var/www/.env
APP_ENV=production
APP_DEBUG=true
APP_KEY=${APP_KEY}
BROADCAST_DRIVER=${BROADCAST_DRIVER}
REVERB_APP_ID=${REVERB_APP_ID}
REVERB_APP_KEY=${REVERB_APP_KEY}
REVERB_APP_SECRET=${REVERB_APP_SECRET}
REVERB_HOST=${REVERB_HOST}
REVERB_PORT=${REVERB_PORT}
REVERB_SCHEME=${REVERB_SCHEME}
EOF
fi

exec "$@"

echo "Waiting for database connection..."
sleep 5 # Adjust as needed

# Fix permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Start Supervisor to manage Reverb & Queue
echo "Starting Supervisor..."
exec supervisord -c /etc/supervisord.conf
