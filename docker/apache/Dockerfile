FROM php:8.2-apache

RUN a2enmod rewrite headers ssl

RUN apt-get update && apt-get install -y libzip-dev zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libicu-dev && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql mysqli zip gd intl mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
