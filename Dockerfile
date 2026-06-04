# templates/docker/Dockerfile
FROM dunglas/frankenphp:1-php8.3-alpine

# 1. Installation des dépendances système nécessaires
RUN apk add --no-repeat --no-cache \
    acl \
    file \
    gettext \
    git \
    && set -eux

# 2. Installation des extensions PHP requises (notamment pour MariaDB/MySQL)
RUN install-php-extensions \
    pdo_mysql \
    intl \
    zip \
    opcache \
    apcu

# 3. Configuration de l'environnement de Production
ENV APP_ENV=prod
ENV FRANKENPHP_CONFIG="import /app/Caddyfile"

# Remplacement du php.ini de développement par celui de production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# 4. Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /app

# 5. Copie des fichiers de l'application
COPY . .

# Configuration des permissions pour Symfony
RUN chown -R www-data:www-data /app \
    && mkdir -p var/cache var/log \
    && setfacl -R -m u:www-data:rwX var \
    && setfacl -R -d -m u:www-data:rwX var

# 6. Installation des dépendances sans les scripts (évite les erreurs de build)
RUN composer install --no-dev --prefer-dist --no-scripts --no-progress --non-interactive

# 7. Optimisation du dump de l'Autoloader et exécution des scripts de prod
RUN composer dump-autoload --no-dev --classmap-authoritative \
    && composer run-script --no-dev post-install-cmd