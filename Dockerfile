FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

CMD ["/bin/sh", "-c", "php artisan config:clear && php artisan migrate --force && php artisan db:seed --force && php artisan storage:link || true && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
