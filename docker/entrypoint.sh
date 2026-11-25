#!/bin/sh
set -e

#echo "[INIT] Waiting for DB..."
#
#until mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
#  sleep 1
#done

#echo "[INIT] DB ready"
if [ ! -f /var/www/.env ]; then
    echo "[INIT] Creating .env from .env.example"
    cp /var/www/.env.example /var/www/.env
fi

echo "[INIT] Running migrations..."
/migrate.sh

echo "[INIT] Starting app..."

exec "$@"