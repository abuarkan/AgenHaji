#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    public/uploads

echo "Waiting for MySQL at ${DB_HOST:-mysql}:3306..."
i=0
until php -r "
try {
    new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s', getenv('DB_HOST') ?: 'mysql', getenv('DB_PORT') ?: '3306', getenv('DB_DATABASE') ?: 'bpkh_agen_haji'),
        getenv('DB_USERNAME') ?: 'agenhaji',
        getenv('DB_PASSWORD') ?: 'agenhaji'
    );
    exit(0);
} catch (Throwable \$e) {
    exit(1);
}
"; do
    i=$((i + 1))
    if [ "$i" -ge 60 ]; then
        echo "MySQL did not become ready in time."
        exit 1
    fi
    sleep 2
done

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -qE '^APP_KEY=base64:.+' .env; then
    php artisan key:generate --force --no-interaction
fi

chown -R www-data:www-data storage bootstrap/cache public/uploads
php artisan storage:link --force --no-interaction >/dev/null 2>&1 || true
php artisan migrate --force --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
