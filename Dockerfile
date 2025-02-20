# Use an official PHP runtime image
FROM php:8.1-apache

# Install necessary extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Expose port 10000 (Render's default web service port)
EXPOSE 10000

# Copy the app into the container
COPY . /var/www/html/

# Set Apache to listen on port 10000
RUN echo "Listen 10000" >> /etc/apache2/ports.conf

# Set ServerName to suppress the warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Enable Apache mod_rewrite (if needed for your API)
RUN a2enmod rewrite

# Set the default site configuration to listen on port 10000
RUN echo '<VirtualHost *:10000>\nDocumentRoot /var/www/html\n</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Run Apache in the foreground
CMD ["apache2-foreground"]
