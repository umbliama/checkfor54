

################################################################################
# Stage 1: Composer dependencies
################################################################################
FROM composer:lts as deps

WORKDIR /app

# Copy application files into the container
COPY . .

# Run composer install
RUN --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction

################################################################################
# Stage 2: Build the final image
################################################################################
FROM php:8.3-apache

WORKDIR /var/www

# Copy all application files from the previous stage
COPY --from=deps /app /var/www

# Configure PHP and Apache
RUN mv "/usr/local/etc/php/php.ini-development" "/usr/local/etc/php/php.ini" \
    && a2enmod rewrite \
    && sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www
    
# Expose port 80 for web traffic
EXPOSE 80
