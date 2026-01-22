php artisan migrate:fresh --database=central --path=database/migrations

php artisan db:seed --class="Modules\Tenancy\database\seeders\AdminUserSeeder"

php8.2 artisan db:seed --class=UserSeeder --database=central //////

FOR MIGRATE CENTRAL DB

===================================================================
php artisan serve --port=8080


FOR MIGRATE TENANT

php artisan tenants:migrate-fresh --tenants=master
php artisan tenants:migrate --seed --tenants=master
