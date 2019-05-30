FROM php:latest

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    echo 'date.timezone = Europe/Berlin' >> "$PHP_INI_DIR/php.ini"

RUN apt-get update && apt-get install -my \
    git \
    imagemagick \
    zip \
    && rm -rf /var/lib/apt/lists/*

COPY ./_docker/php/composer-installer.sh /tmp/
RUN /tmp/composer-installer.sh && rm /tmp/composer-installer.sh

WORKDIR /var/www

CMD bash