FROM php:8.1-fpm

# Install required zip extension 
RUN apt-get -y update \
    && apt-get install -y zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
