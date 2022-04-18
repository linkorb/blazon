FROM ghcr.io/linkorb/php-docker-base:php8

EXPOSE 80

COPY --chown=www-data:www-data . /app

WORKDIR /app

USER www-data

RUN COMPOSER_MEMORY_LIMIT=-1 /usr/bin/composer install --no-scripts --no-dev

RUN npm install && node_modules/.bin/encore production && rm -rf node_modules

USER root

ENTRYPOINT ["apache2-foreground"]
