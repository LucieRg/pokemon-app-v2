# Utiliser l'image PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    && docker-php-ext-install intl pdo pdo_pgsql opcache

# Installer Composer globalement
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Activer le module Apache pour la réécriture des URLs (important pour Symfony)
RUN a2enmod rewrite

# Copier le fichier de configuration Apache pour Symfony
COPY ./docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Copier le fichier php.ini personnalisé
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Définir le dossier de l'application Symfony comme racine du serveur
WORKDIR /var/www/html

# Copier le code source Symfony dans le conteneur
COPY . /var/www/html

# Changer les permissions pour Apache
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Installer les dépendances Symfony avec Composer
RUN composer install --no-scripts --no-interaction --optimize-autoloader

# Assurez-vous que Symfony CLI est disponible
RUN symfony check:requirements

# Exposer le port 80
EXPOSE 80

# Commande pour démarrer Apache
CMD ["apache2-foreground"]
