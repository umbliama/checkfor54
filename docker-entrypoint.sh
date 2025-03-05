#!/bin/sh
set -e

echo "📌 Запуск entrypoint..."

# 1️⃣ Создаём .env, если его нет
if [ ! -f /var/www/.env ]; then
  echo "📄 Создаю .env файл..."
  cat <<EOF > /var/www/.env
APP_NAME=БМ-Сервис
APP_ENV=production
APP_KEY=${APP_KEY:-base64:default_key}
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false

BROADCAST_CONNECTION=reverb
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

BROADCAST_DRIVER=reverb
REVERB_APP_ID=${REVERB_APP_ID:-default}
REVERB_APP_KEY=${REVERB_APP_KEY:-default}
REVERB_APP_SECRET=${REVERB_APP_SECRET:-default}
REVERB_HOST=${REVERB_HOST:-localhost}
REVERB_PORT=${REVERB_PORT:-6001}
REVERB_SCHEME=${REVERB_SCHEME:-https}

VITE_REVERB_APP_KEY=${REVERB_APP_KEY}
VITE_REVERB_HOST=${REVERB_HOST}
VITE_REVERB_PORT=${REVERB_PORT}
VITE_REVERB_SCHEME=${REVERB_SCHEME}
EOF
else
  echo "✅ .env уже существует, пропускаем создание"
fi

# 2️⃣ Ждём подключение к БД
echo "⏳ Ожидание подключения к базе данных..."
sleep 5

# 3️⃣ Фиксим права
echo "🔧 Исправляем права на storage и cache..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 4️⃣ Очищаем кэш Laravel
echo "🧹 Очистка кэша Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 5️⃣ Запускаем Supervisor для Reverb и очередей
echo "🚀 Запуск Supervisor..."
exec supervisord -c /etc/supervisord.conf
