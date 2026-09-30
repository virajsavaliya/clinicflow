#!/bin/sh
set -e

cd /var/www

# Ensure Laravel writable paths exist with safe permissions.
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

# Clear common Laravel caches to ensure fresh views and configs on container start.
if [ -f artisan ]; then
	php artisan view:clear || true
	php artisan cache:clear || true
	php artisan config:clear || true
	php artisan route:clear || true
	php artisan optimize:clear || true
fi

# Attempt to clear PHP opcache (best-effort).
php -r 'if(function_exists("opcache_reset")){ @opcache_reset(); }' || true

exec "$@"
