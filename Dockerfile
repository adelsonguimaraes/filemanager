# Use a imagem oficial do PHP com Apache
FROM php:8.2-apache

RUN adduser www-data www-data && \
chown -R www-data:www-data /var/www && \
chmod -R 777 /var/www/html/

COPY src/ /var/www/html/