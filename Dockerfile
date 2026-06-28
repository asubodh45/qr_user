# ─── Stage 1: Build Vite/Node assets ────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm run build


# ─── Stage 2: PHP application ────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS app

# System dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    gettext \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    postgresql-dev \
    && rm -rf /var/cache/apk/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        mbstring \
        zip \
        bcmath \
        intl \
        pcntl \
        exif \
        opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies (production only)
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction \
    --prefer-dist

# Copy application source
COPY . .

# Copy built frontend assets from node stage
COPY --from=node-builder /app/public/build ./public/build

# Create required directories before copying configs
RUN mkdir -p /etc/supervisor/conf.d /etc/nginx/conf.d /var/log/nginx /run/nginx

# Copy Docker config files
COPY docker/nginx.conf       /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini          /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/entrypoint.sh    /entrypoint.sh

RUN chmod +x /entrypoint.sh

# Storage directories
RUN mkdir -p storage/app/public \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx log dir
RUN mkdir -p /var/log/nginx /run/nginx

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
