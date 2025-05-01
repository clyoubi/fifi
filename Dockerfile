# Use the official PHP image with Apache
FROM php:8.4-apache

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite (for pretty URLs if needed)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files into the container (from local ./app)
COPY ./app /var/www/html

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html
