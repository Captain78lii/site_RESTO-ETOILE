FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

COPY . /var/www/html/

# Le dossier images/ doit rester modifiable pour les uploads (produits, avis).
RUN chown -R www-data:www-data /var/www/html/images

EXPOSE 80
