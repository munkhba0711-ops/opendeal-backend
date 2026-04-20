FROM php:8.2-cli
RUN apt-get update -y && apt-get install -y libpq-dev git unzip
RUN docker-php-ext-install pdo pdo_pgsql
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app
COPY . /app
RUN composer install --no-dev --optimize-autoloader
CMD php artisan serve --host=0.0.0.0 --port=$PORT
