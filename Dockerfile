FROM dunglas/frankenphp:1-php8.4-alpine

# 1. Installation des dépendances système nécessaires
RUN apk add --no-cache --no-progress \
    acl \
    file \
    gettext \
    git \
    && set -eux

# 2. Installation des extensions PHP requises
RUN install-php-extensions \
    pdo_mysql \
    intl \
    zip \
    opcache \
    apcu

# 3. Configuration de l'environnement de Production
ENV APP_ENV=prod
# ON SUPPRIME LA VARIABLE FRANKENPHP_CONFIG QUI REPRÉSENTE L'IMPORT EMBÊTANT

# Remplacement du php.ini de développement par celui de production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# 4. Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /app

# 5. Copie des fichiers de l'application
COPY . .

# ON COPIE NOTRE CADDYFILE DIRECTEMENT AU BON ENDROIT POUR ÉCRASER LA CONFIG PAR DÉFAUT
COPY Caddyfile /etc/frankenphp/Caddyfile

# Configuration des permissions pour Symfony
RUN chown -R www-data:www-data /app \
    && mkdir -p var/cache var/log \
    && setfacl -R -m u:www-data:rwX var \
    && setfacl -R -d -m u:www-data:rwX var

# 6. Configuration de sécurité Git + Installation des dépendances
RUN git config --global --add safe.directory /app \
    && composer install --no-dev --prefer-dist --no-scripts --no-progress --no-interaction

# 7. Optimisation du dump de l'Autoloader
RUN composer dump-autoload --no-dev --classmap-authoritative