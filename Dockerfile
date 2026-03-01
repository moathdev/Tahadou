FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    nodejs \
    npm \
    supervisor \
    nginx

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        bcmath \
        gd \
        opcache \
        pcntl

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Copy application code
COPY . .

# Copy .env.example if .env doesn't exist
RUN [ -f .env ] || cp .env.example .env

# Complete composer setup
RUN composer dump-autoload --optimize --no-dev

# Install Node dependencies and build assets
RUN npm ci && npm run build && rm -rf node_modules

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisord config
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

# ---
# Queue Worker (run separately in k8s as a second container or Deployment)
# CMD: php artisan queue:work --sleep=3 --tries=3 --max-time=3600
