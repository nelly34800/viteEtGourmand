# Image CLI
FROM php:8.2-cli

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
        zip
# Installation des dépendances nécessaires pour compiler l'extension
RUN apt-get update && apt-get install -y \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Installation de l'extension MongoDB officielle via PECL
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copie tout le backend
COPY . /var/www/html

# Installe les dépendances Composer
RUN composer install --no-dev --optimize-autoloader

# Expose le port (purement indicatif pour Docker)
EXPOSE 80

# Commande finale :  lance le serveur intégré de PHP qui écoute sur le port d'Heroku
# et qui pointe directement sur le dossier public/index.php
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT -t public"]