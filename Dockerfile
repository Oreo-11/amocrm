FROM wordpress:php8.1-apache

# Установка дополнительных PHP расширений
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip

# Включаем необходимые модули Apache
RUN a2enmod rewrite

# Копируем custom php.ini
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html