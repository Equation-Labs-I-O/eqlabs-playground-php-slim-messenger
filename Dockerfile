FROM php:8.2-cli-alpine

COPY --from=mlocati/php-extension-installer:2.6.4 /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer/composer:2.8.2-bin /composer /usr/bin/composer

# Install PHP extensions
RUN install-php-extensions pdo_mysql zip amqp opcache pcntl

# Set the working directory
WORKDIR /app

# Install Composer
ADD composer.json composer.lock /app/
RUN composer install --no-scripts

# Copy the rest of the application
COPY . /app

