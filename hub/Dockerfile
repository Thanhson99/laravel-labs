FROM php:8.4-cli

ARG HUB_INTERNAL_PORT=8088
ENV HUB_INTERNAL_PORT=${HUB_INTERNAL_PORT}

RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates git unzip libicu-dev libsqlite3-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install intl pdo pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --optimize

RUN chmod +x docker/entrypoint.sh \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE ${HUB_INTERNAL_PORT}

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${HUB_INTERNAL_PORT}"]
