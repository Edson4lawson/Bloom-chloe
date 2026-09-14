#!/bin/sh
set -e

PORT=${PORT:-8080}
echo "Starting Bloom-Chloe API server on 0.0.0.0:$PORT..."

# Run PHP built-in web server pointing to the backend directory
exec php -S 0.0.0.0:$PORT -t /var/www/backend
