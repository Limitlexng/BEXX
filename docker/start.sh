#!/bin/sh
set -e

cd /var/www/html

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    APP_KEY=$(php artisan key:generate --show --no-ansi 2>/dev/null || php -r "echo 'base64:'.base64_encode(random_bytes(32));")
    export APP_KEY
fi

# Write runtime .env
cat > .env <<EOF
APP_NAME="${APP_NAME:-Cartlex}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost:8080}"
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

LOG_CHANNEL=stderr
LOG_LEVEL=error

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@gocartlex.com"
MAIL_FROM_NAME="Cartlex"
EOF

# Ensure SQLite DB exists
touch database/database.sqlite

# Check if migrations have been run by looking for a key table
MIGRATED=$(php artisan tinker --execute="echo \DB::table('migrations')->count();" 2>/dev/null || echo "0")

if [ "$MIGRATED" = "0" ] || [ -z "$MIGRATED" ]; then
    echo "Running migrations and seeding..."
    php artisan migrate --force --seed --no-interaction
else
    echo "Running pending migrations..."
    php artisan migrate --force --no-interaction
fi

# Storage symlink
php artisan storage:link --force 2>/dev/null || true

# Cache for production performance
php artisan config:cache --no-ansi
php artisan route:cache --no-ansi
php artisan view:cache --no-ansi

echo "✓ Cartlex Fleet Partner Portal ready — port ${PORT:-8080}"

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
