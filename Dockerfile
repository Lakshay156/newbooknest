FROM php:8.2-apache

# Install and enable the mysqli extension required for the database connection
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy all project files to the Apache document root
COPY . /var/www/html/

# Expose port (Render sets PORT environment variable, default 80 in container)
EXPOSE 80

# The default command in the php:apache image starts the apache daemon
