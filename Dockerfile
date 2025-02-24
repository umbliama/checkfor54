################################################################################
# Stage 1: Composer dependencies
################################################################################
FROM composer:lts as deps

WORKDIR /app

# Copy application files into the container
COPY . .

# Install Composer dependencies
RUN --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction --prefer-dist

################################################################################
# Stage 2: Node.js build
################################################################################
FROM node:lts as node-build

WORKDIR /app

# ✅ Ensure vendor/ exists before running npm build
COPY --from=deps /app /app

# Install Node.js dependencies and build assets
RUN npm install && npm run build

################################################################################
# Stage 3: Final application image
################################################################################
FROM php:8.3-apache

WORKDIR /var/www

# Copy built application files from Composer & Node.js build stages
COPY --from=deps /app /var/www
COPY --from=node-build /app/public /var/www/public

# Install dependencies including Certbot
RUN apt-get update && apt-get install -y \
    openssl \
    certbot \
    git \
    nano\
    python3-certbot-apache \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js (LTS)
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Configure Apache and PHP
RUN mv "/usr/local/etc/php/php.ini-development" "/usr/local/etc/php/php.ini" \
    && a2enmod rewrite ssl \
    && sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# ✅ Copy startup script (for migrations, queue, etc.)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose ports for HTTP and HTTPS
EXPOSE 80 443

# ✅ Set entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

# Start Apache
CMD ["apache2-foreground"]
