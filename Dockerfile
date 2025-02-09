FROM dunglas/frankenphp

ARG uid
ARG gid

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libzip-dev \
    libpq-dev \
    libxslt-dev \
    libmemcached-dev \
    nano \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# add additional extensions here:
RUN install-php-extensions \
	gd \
	intl \
    imap \
    bcmath \
    redis \
    curl \
    exif \
    hash \
    iconv \
    json \
    mbstring \
    mysqli \
    mysqlnd \
    pcntl \
    pcre \
    xml \
    libxml \
    zlib \
	zip

# Install Node.js (latest LTS) and Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - && \
    apt-get install -y nodejs

# add additional extensions here:
RUN install-php-extensions \
	pdo_mysql \
	gd \
	intl \
    imap \
    bcmath \
    redis \
    curl \
    exif \
    hash \
    iconv \
    json \
    mbstring \
    mysqli \
    mysqlnd \
    pcntl \
    pcre \
    xml \
    libxml \
    zlib \
	zip

## Install PECL extensions
RUN pecl install \
    memcached-3.3.0 \
    redis-6.1.0 \
    xdebug-3.4.1 \
    && docker-php-ext-enable memcached redis

RUN apt update && apt install -y supervisor

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

COPY /docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

RUN chown -R www-data:www-data .

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-ansi \
    --no-scripts

RUN npm install

EXPOSE 8000 5173 9003 2019

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
