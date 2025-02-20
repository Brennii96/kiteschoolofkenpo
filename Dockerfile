FROM php:8.3-fpm

ARG user
ARG uid
ENV APP_ROOT="/var/www/html"

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    zlib1g-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Node.js & NPM
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - && \
    apt-get install -y nodejs \
    && npm i -g npm

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add user
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR $APP_ROOT

COPY . .
RUN npm ci --omit=dev
RUN npm run build

RUN composer install --no-scripts --no-interaction --no-dev --optimize-autoloader

# Ensure proper permissions
RUN chown -R $user:www-data $APP_ROOT/

USER $user
