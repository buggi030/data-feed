FROM php:8.2-alpine

ARG filename

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY ./app .

COPY ./$filename ./feed.xml

RUN composer install

RUN php bin/doctrine orm:schema-tool:create

CMD ["php", "bin/console", "parse-data"]
