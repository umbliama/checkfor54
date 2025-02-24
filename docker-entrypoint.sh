#!/bin/bash
set -e

# Wait for database (MySQL/Postgres)
echo "Waiting for database connection..."
sleep 5 # Adjust this if needed

# Run Laravel commands
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan reverb:start & 
php artisan queue:work & 

# Start Apache
exec apache2-foreground
