FROM php:8.1-apache

# Cài extension PHP + phpredis
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install redis && docker-php-ext-enable redis

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy mã nguồn
COPY . .

# Cài Laravel và build cache
RUN composer install --no-dev --optimize-autoloader && \
    cp .env.example .env && \
    php artisan key:generate && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan storage:link

# Cấu hình Apache để Laravel chạy từ public/
RUN a2enmod rewrite
COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]
