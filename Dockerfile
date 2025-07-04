FROM webdevops/php-nginx:8.4-alpine

# Install required packages
RUN apk add --no-cache postgresql-libs libxml2 libzip oniguruma

RUN apk add --no-cache --virtual .build-deps \
    postgresql-dev libxml2-dev libzip-dev $PHPIZE_DEPS \
    && docker-php-ext-install bcmath xml zip opcache pgsql pdo_pgsql \
    && apk del .build-deps

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN git config --global --add safe.directory /var/www/html \
    && composer install --no-dev --optimize-autoloader --no-interaction \
    --no-progress --no-scripts

# Copy application files
COPY . .

# Set proper permissions before running scripts
RUN chown -R application:application /var/www/html \
    && chmod -R ug+rwX storage bootstrap/cache

# Run Laravel optimization
RUN composer run-script post-autoload-dump --no-dev --no-interaction \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Create nginx configuration for Laravel
RUN echo 'server {' > /opt/docker/etc/nginx/vhost.conf \
    && echo '    listen 80;' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '    server_name _;' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '    root /var/www/html/public;' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '    index index.php index.html;' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '    location / {' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '        try_files $uri $uri/ /index.php?$query_string;' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '    }' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '    location ~ \.php$ {' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '        fastcgi_pass 127.0.0.1:9000;' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '        fastcgi_index index.php;' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '        include fastcgi_params;' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '    }' >> /opt/docker/etc/nginx/vhost.conf \
    && echo '}' >> /opt/docker/etc/nginx/vhost.conf

EXPOSE 80