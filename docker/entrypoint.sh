#!/bin/sh
set -e

echo "Running database migrations..."

php vendor/bin/phinx migrate

echo "Starting application..."

exec "$@"