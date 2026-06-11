#!/bin/sh
set -e

for app in /var/www/laravel /var/www/event-app; do
    if [ ! -f "$app/public/build/manifest.json" ]; then
        echo "Building Vite assets for $app..."
        cd "$app" || exit 1
        npm install --no-audit --no-fund 2>&1
        npm run build 2>&1
    fi
done

exec "$@"
