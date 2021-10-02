FROM phpdockerio/php74-fpm

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

RUN apt update && \
    apt -y install libssl1.1=1.1.1f-1ubuntu2.8 libssl-dev --allow-downgrades librabbitmq-dev gcc make autoconf libc-dev pkg-config php7.4-common php7.4-dev php7.4-pgsql php7.4-bcmath php7.4-intl php-pear && \
    pecl channel-update pecl.php.net && \
    pecl install amqp-1.10.2 && \
    echo "extension=amqp.so" >> /etc/php/7.4/cli/php.ini && \
    echo "extension=amqp.so" >> /etc/php/7.4/fpm/php.ini && \
    pecl install redis-5.3.4 && \
    echo "extension=redis.so" >> /etc/php/7.4/cli/php.ini && \
    echo "extension=redis.so" >> /etc/php/7.4/fpm/php.ini && \
    apt clean

WORKDIR "/application"

CMD /usr/sbin/php-fpm7.4 -O