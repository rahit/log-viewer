FROM php:apache

# Install required system package
RUN apt-get update \
	&& apt-get install -y \
    autoconf \
    automake \
    build-essential \
    git \
    gzip \
    libtool \
    logrotate \
    pkg-config \
    wget

# Install phpunit
RUN wget https://phar.phpunit.de/phpunit.phar \
	&& chmod +x phpunit.phar \
	&& mv phpunit.phar /usr/local/bin/phpunit \
	&& phpunit --version

# install composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# permission stuff
RUN usermod -u 1000 www-data
ADD . /var/www/html
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html
RUN chown -R www-data:1000 .
