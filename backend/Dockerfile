# Image PHP 8.2 avec Apache
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    unzip \
    git \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        intl \
        zip \
    && a2enmod rewrite

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copie tout le backend
COPY . /var/www/html

# Installe les dépendances Composer (PHPMailer, etc.)
RUN composer install --no-dev --optimize-autoloader

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Avant de lancer Apache, on remplace dynamiquement le port "80" configuré par défaut 
# dans Apache par la variable $PORT fournie par Heroku.
CMD sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf && apache2-foreground