FROM php:8.0-fpm

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the existing application directory contents
COPY . .

# Install PHP dependencies
RUN composer install

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache


EXPOSE 80

CMD ["php-fpm"]
