#!/usr/bin/env bash
# Remote setup script — runs ON the server, no escaping issues
set -e

APP_DIR="$HOME/cartlex_app"
PUBLIC_HTML="$HOME/public_html"

echo "▶ Extracting files..."
mkdir -p "$APP_DIR"
tar -xzf ~/cartlex_deploy.tar.gz -C "$APP_DIR" --overwrite
rm ~/cartlex_deploy.tar.gz

cd "$APP_DIR"

# ── Composer ──────────────────────────────────────────────────────
echo "▶ Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs 2>&1 | tail -5

# ── .env ──────────────────────────────────────────────────────────
echo "▶ Setting up .env..."
if [ ! -f .env ]; then
  cp .env.example .env
  # Force production settings
  sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
  sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
  # Remove old DB lines and append correct SQLite config
  grep -v "^DB_" .env > /tmp/.env_nodbs && mv /tmp/.env_nodbs .env
  printf '\nDB_CONNECTION=sqlite\nDB_DATABASE=%s/database/database.sqlite\n' "$APP_DIR" >> .env
  printf 'SESSION_DRIVER=file\nCACHE_STORE=file\nQUEUE_CONNECTION=sync\n' >> .env
  printf 'LOG_CHANNEL=single\nLOG_LEVEL=error\n' >> .env
  php artisan key:generate --force
fi

# ── Database ──────────────────────────────────────────────────────
echo "▶ Setting up database..."
touch database/database.sqlite
chmod 664 database/database.sqlite
chmod -R 775 storage bootstrap/cache

php artisan migrate --force --no-interaction

USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | grep -o '[0-9]*' | tail -1)
if [ -z "$USER_COUNT" ] || [ "$USER_COUNT" = "0" ]; then
  echo "▶ Seeding demo data..."
  php artisan db:seed --force --no-interaction
fi

# ── Cache ─────────────────────────────────────────────────────────
echo "▶ Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force 2>/dev/null || true

# ── Wire up public_html ───────────────────────────────────────────
echo "▶ Wiring up public_html..."

# Write a PHP patcher script to a file (avoids all shell quoting issues)
cat > /tmp/patch_index.php << 'PHPSCRIPT'
<?php
$appDir  = getenv('HOME') . '/cartlex_app';
$pubDir  = getenv('HOME') . '/public_html';

$src = file_get_contents($appDir . '/public/index.php');
if ($src === false) {
    echo "ERROR: Could not read $appDir/public/index.php\n";
    exit(1);
}

$find = [
    "__DIR__.'/../vendor/autoload.php'",
    "__DIR__.'/../bootstrap/app.php'",
    "__DIR__.'/../storage/framework/maintenance.php'",
];
$replace = [
    "'{$appDir}/vendor/autoload.php'",
    "'{$appDir}/bootstrap/app.php'",
    "'{$appDir}/storage/framework/maintenance.php'",
];

$patched = str_replace($find, $replace, $src);

if (file_put_contents($pubDir . '/index.php', $patched) === false) {
    echo "ERROR: Could not write to $pubDir/index.php\n";
    exit(1);
}
echo "✓ index.php patched\n";
PHPSCRIPT

php /tmp/patch_index.php
rm /tmp/patch_index.php

# Copy .htaccess
cp public/.htaccess "$PUBLIC_HTML/.htaccess"
echo "✓ .htaccess copied"

# Copy built assets
cp -rf public/build "$PUBLIC_HTML/"
echo "✓ Build assets copied"

# Copy public storage if any
if [ -d storage/app/public ]; then
  cp -rf storage/app/public "$PUBLIC_HTML/storage"
fi

# ── Verify ────────────────────────────────────────────────────────
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Deploy complete!"
echo "  App dir : $APP_DIR"
echo "  Web root: $PUBLIC_HTML"
echo ""
echo "  Checking index.php..."
if [ -f "$PUBLIC_HTML/index.php" ]; then
  echo "  ✓ public_html/index.php exists ($(wc -c < "$PUBLIC_HTML/index.php") bytes)"
  head -3 "$PUBLIC_HTML/index.php"
else
  echo "  ✗ public_html/index.php MISSING!"
fi
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "  Login: admin@gocartlex.com / Cartlex@2025!"
