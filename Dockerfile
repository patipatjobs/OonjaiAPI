FROM php:7.2-apache

RUN docker-php-ext-install pdo_mysql
RUN a2enmod rewrite

ADD . /var/www
ADD ./public /var/www/html

# docker run -it --name oonjai-sv -p 56870:80 -v "$PWD":/var/www/html --restart always -d ubuntu