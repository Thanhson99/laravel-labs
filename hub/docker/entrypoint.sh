#!/usr/bin/env sh
set -eu

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --ansi
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache database
touch database/database.sqlite

php artisan config:clear --ansi

exec "$@"
