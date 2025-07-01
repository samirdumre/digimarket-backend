FROM webdevops/php-nginx:8.4-alpine

RUN apk add --no-cache postgresql-libs libxml2 libzip oniguruma

RUN apk add --no-cache --virtual .build-deps \
        postgresql-dev libxml2-dev libzip-dev $PHPIZE_DEPS \
    && docker-php-ext-install bcmath xml zip opcache pgsql pdo_pgsql \
    && apk del .build-deps

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN git config --global --add safe.directory /var/www/html \
 && composer install --no-dev --optimize-autoloader --no-interaction \
       --no-progress --no-scripts

COPY . .

RUN composer run-script post-autoload-dump --no-dev --no-interaction \
 && php artisan optimize --no-interaction

RUN chown -R application:application /var/www/html \
 && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 80
