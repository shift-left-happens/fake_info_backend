FROM php:8.4-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system libraries required for PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    pkg-config \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first and install dependencies
COPY composer.json composer.lock ./
RUN composer install

# Copy the rest of the project
COPY . .

# Apache config
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

# Start Apache
CMD ["apache2-foreground"]