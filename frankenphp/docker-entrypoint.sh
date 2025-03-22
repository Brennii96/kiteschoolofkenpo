#!/bin/sh
set -e

if [ "$1" = 'frankenphp' ] || [ "$1" = 'php' ] || [ "$1" = 'artisan' ]; then
    # Ensure Laravel dependencies are installed
    if [ ! -d vendor ]; then
        composer install --prefer-dist --no-progress --no-interaction
    fi

    # Check if database is ready
    if grep -q ^DB_CONNECTION= .env; then
        echo 'Waiting for database to be ready...'
        ATTEMPTS_LEFT_TO_REACH_DATABASE=60
        until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || php artisan migrate:status > /dev/null 2>&1; do
            sleep 1
            ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
            echo "Still waiting for database... $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left."
        done
    fi

    # Run migrations if necessary
    if [ "$( find ./database/migrations -iname '*.php' -print -quit )" ]; then
        php artisan migrate --no-interaction --force
    fi

    # Fix permissions
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 777 storage bootstrap/cache

    echo 'PHP app ready!'
fi

if [ "$APP_ENV" = "dev" ]; then
    if [ ! -d "node_modules" ]; then
        echo "Installing frontend dependencies..."
        npm install
    fi
fi

exec docker-php-entrypoint "$@"
