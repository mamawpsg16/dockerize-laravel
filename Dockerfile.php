# Dockerfile for PHP
FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd

# Set working directory
WORKDIR /var/www/html

# Copy current directory to /var/www/html in the container
# COPY ./src .

# Install Composer (PHP dependency manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add health check
HEALTHCHECK --interval=30s --timeout=10s --retries=3 \
    CMD ["sh", "-c", "curl --fail http://localhost:9000 || exit 1"]

# Expose port 9000 (used by PHP-FPM)
EXPOSE 9000

# Set the entrypoint
ENTRYPOINT ["/bin/sh", "-c"]

# Set the default command
CMD ["if [ ! -d \"vendor\" ]; then composer install --no-interaction --no-plugins --no-scripts; fi && php-fpm"]
