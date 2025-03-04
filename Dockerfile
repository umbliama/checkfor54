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
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
# Install dependencies including Certbot
RUN apt-get update && apt-get install -y \
    openssl \
    certbot \
    git \
    nano \ 
    python3-certbot-apache \
    curl \
    && rm -rf /var/lib/apt/lists/* \    
    && curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build \
    && rm -rf /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y supervisor
COPY supervisord.conf /etc/supervisord.conf
    
# Configure Apache and PHP
RUN mv "/usr/local/etc/php/php.ini-development" "/usr/local/etc/php/php.ini" \
    && a2enmod rewrite ssl \
    && sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www
RUN docker-php-ext-install pcntl

    
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh
CMD ["/docker-entrypoint.sh"]
# Expose ports for HTTP and HTTPS
EXPOSE 80 443 6001
