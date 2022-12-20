FROM php:8-fpm

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
