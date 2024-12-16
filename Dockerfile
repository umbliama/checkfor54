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

# Install dependencies including Certbot
RUN apt-get update && apt-get install -y \
    openssl \
    certbot \
    python3-certbot-apache \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache and PHP
RUN mv "/usr/local/etc/php/php.ini-development" "/usr/local/etc/php/php.ini" \
    && a2enmod rewrite ssl \
    && sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Expose ports for HTTP and HTTPS
EXPOSE 80 443

# Copy the entrypoint script and make it executable
# COPY entrypoint.sh /usr/local/bin/entrypoint.sh
# RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the entrypoint
# ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
