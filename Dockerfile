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


FROM node:lts as node-build

WORKDIR /app

# Copy application files into the container
COPY . .

# Install Node.js dependencies 
RUN npm install

# Run the build command 
RUN npm run build
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
    curl \
    && rm -rf /var/lib/apt/lists/* \
    # Install Node.js and npm
    && curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs \
    && npm install
    && npm run build
    && rm -rf /var/lib/apt/lists/*



# Configure Apache and PHP
RUN mv "/usr/local/etc/php/php.ini-development" "/usr/local/etc/php/php.ini" \
    && a2enmod rewrite ssl \
    && sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Expose ports for HTTP and HTTPS
EXPOSE 80 443
