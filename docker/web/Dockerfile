FROM php:8.1-apache

LABEL Description="Share Portfolio" Vendor="Corestar" Version="0.1"

RUN apt-get update && apt-get install -y \
	vim \
	sudo \
	mlocate \
	zip \
    libicu67 \
    libicu-dev \
    zlib1g \
    zlib1g-dev \
    libpng-dev


RUN docker-php-ext-install pdo_mysql intl gd \
	&& a2enmod rewrite \
	&& a2enmod actions

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=. --filename=composer \
    && mv composer /usr/local/bin \
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini 

RUN true \
    && echo "upload_max_filesize = 16M" > /usr/local/etc/php/conf.d/other.ini \
    && echo "post_max_size = 16M" >> /usr/local/etc/php/conf.d/other.ini \
    && echo "error_log = /var/log/apache2/error.log" >> /usr/local/etc/php/conf.d/other.ini \
    && echo "date.timezone = \"Australia/Perth\"" >> /usr/local/etc/php/conf.d/other.ini  \
    && echo "Listen 81" >> /etc/apache2/ports.conf \
    && echo "Listen 82" >> /etc/apache2/ports.conf

RUN chown -Rv www-data:www-data  /var/www
RUN sudo -u www-data mkdir /var/www/html/blog

WORKDIR /var/www/html/blog


EXPOSE 80
EXPOSE 81
EXPOSE 82
EXPOSE 443
