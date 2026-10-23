FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libzip-dev unzip \
    && docker-php-ext-install bcmath pdo_pgsql zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV APACHE_DOCUMENT_ROOT=/var/www/html/beriya-travels/backend/public
WORKDIR /var/www/html

RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    && printf '<Directory %s>\n    AllowOverride All\n    Require all granted\n</Directory>\n' "$APACHE_DOCUMENT_ROOT" \
        >> /etc/apache2/apache2.conf

COPY beriya-travels/backend/ /var/www/html/beriya-travels/backend/
RUN composer install \
    --working-dir=/var/www/html/beriya-travels/backend \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

COPY ["website v1.html", "/var/www/html/beriya-travels/backend/public/site.html"]
COPY beriya-travels/frontend/src/assets/ /var/www/html/beriya-travels/backend/public/beriya-travels/frontend/src/assets/
COPY deploy/seed-if-empty.php /var/www/html/beriya-travels/backend/deploy/seed-if-empty.php
COPY deploy/start.sh /usr/local/bin/beriya-start

RUN chmod +x /usr/local/bin/beriya-start \
    && mkdir -p /var/www/html/beriya-travels/backend/storage/framework/cache/data \
        /var/www/html/beriya-travels/backend/storage/framework/sessions \
        /var/www/html/beriya-travels/backend/storage/framework/views \
        /var/www/html/beriya-travels/backend/storage/logs \
        /var/www/html/beriya-travels/backend/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/beriya-travels/backend/storage \
        /var/www/html/beriya-travels/backend/bootstrap/cache

EXPOSE 10000
ENTRYPOINT ["/usr/local/bin/beriya-start"]
