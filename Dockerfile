FROM php:8.3-cli
RUN apt-get update && apt-get install -y libpq-dev unzip git && docker-php-ext-install pdo pdo_pgsql
WORKDIR /app
COPY . .
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN composer install --no-interaction
CMD ["php","artisan","serve","--host=0.0.0.0","--port=8000"]
