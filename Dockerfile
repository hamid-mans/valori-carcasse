FROM php:8.3-fpm-alpine

# 1. Installation des dépendances système nécessaires et de Caddy (serveur web moderne et rapide)
RUN apk add --no-cache \
    git \
    icu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    caddy

# 2. Installation des extensions PHP indispensables pour Symfony
RUN docker-php-ext-install \
    intl \
    opcache \
    pdo \
    pdo_mysql \
    zip

# 3. Récupération de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 4. Copie des fichiers du projet
COPY . .

# 5. Configuration de l'environnement de production et installation des dépendances
ENV APP_ENV=prod
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 6. Configuration du serveur Web Caddy pour rediriger vers le dossier public/ de Symfony
RUN echo 'http://:80 { \n\
    root * /app/public \n\
    encode gzip zstd \n\
    php_fastcgi 127.0.0.1:9000 \n\
    file_server \n\
}' > /etc/Caddyfile

EXPOSE 80

# 7. Lancement simultané de PHP-FPM et de Caddy
CMD php-fpm -D && caddy run --config /etc/Caddyfile