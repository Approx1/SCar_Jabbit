FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

WORKDIR /var/www/scar_jabbit
