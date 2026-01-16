# 1. La Base : On part d'une image officielle PHP 8.2 avec Apache
FROM php:8.2-apache

# 2. Les Outils : On installe les extensions PHP nécessaires (MySQL & Zip)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# 3. La Config Apache : On active la réécriture d'URL (pour le .htaccess)
RUN a2enmod rewrite

# 4. Le Dossier de travail dans le conteneur
WORKDIR /var/www/html

# 5. On copie tout notre projet DANS le conteneur
COPY . /var/www/html

# 6. On installe Composer (le gestionnaire de paquets)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 7. SÉCURITÉ : On change la racine web vers /public
# (Pour que /src et /config ne soient pas accessibles via l'URL)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 8. On autorise le .htaccess (AllowOverride All)
RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# On active l'Output Buffering pour éviter les erreurs d'en-têtes déjà envoyés
RUN echo "output_buffering = 4096" > /usr/local/etc/php/conf.d/00-default.ini

# 9. On installe les dépendances PHP (Vendor)
RUN composer install --no-dev --optimize-autoloader

# 10. On donne les droits à Apache (www-data) pour qu'il puisse lire les fichiers
RUN chown -R www-data:www-data /var/www/html

# On s'assure que tout le monde peut écrire dans le dossier temporaire (pour les sessions)
RUN chmod 777 /tmp