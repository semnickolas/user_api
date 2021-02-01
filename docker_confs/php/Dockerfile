FROM php:7.4-fpm

# libs
RUN apt-get update && apt-get install -y git unzip zlib1g-dev libzip-dev libxml2-dev vim telnet librabbitmq-dev nano htop npm

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# extensions
RUN docker-php-ext-install pdo pdo_mysql zip bcmath opcache sockets && docker-php-ext-enable opcache
RUN pecl install amqp  && docker-php-ext-enable amqp

# xdebug
RUN yes | pecl install xdebug && docker-php-ext-enable xdebug
COPY xdebug_additional /
RUN cat /xdebug_additional >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN echo 'memory_limit = 2G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

RUN npm install --global yarn;

# workdir
WORKDIR /code

# entrypoint
COPY entrypoint.sh /
RUN chmod +x /entrypoint.sh
ENTRYPOINT /entrypoint.sh
