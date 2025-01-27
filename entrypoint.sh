rm database/database.sqlite
echo | php artisan migrate
php artisan db:seed DatabaseSeeder
