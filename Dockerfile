# Use an official PHP runtime
FROM php:8.1-apache

# Install required extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy application files to the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 80 for the application
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]