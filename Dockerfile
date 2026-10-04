FROM serversideup/php:8.5-fpm-apache AS composer-deps
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

FROM node:22-alpine AS frontend-assets
WORKDIR /app
COPY package.json package-lock.json ./
COPY .npmrc ./.npmrc
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./vite.config.js
COPY --from=composer-deps /var/www/html/vendor ./vendor
RUN npm run build

FROM serversideup/php:8.5-fpm-apache
USER root
WORKDIR /var/www/html
COPY . .
COPY --from=composer-deps /var/www/html/vendor ./vendor
COPY --from=frontend-assets /app/public/build ./public/build
RUN rm -f .env \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    APACHE_HTTP_PORT=10000 \
    PHP_OPCACHE_ENABLE=1 \
    AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_OPTIMIZE=false \
    AUTORUN_LARAVEL_MIGRATION=true \
    AUTORUN_LARAVEL_ROUTE_CACHE=false \
    AUTORUN_LARAVEL_STORAGE_LINK=false

EXPOSE 10000
USER www-data