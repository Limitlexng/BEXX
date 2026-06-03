#!/usr/bin/env bash
# Runs ON the server — fixes the PHP version issue and completes deployment
set -e

PUBLIC_HTML="$HOME/domains/deliverypartner.gocartlex.com/public_html"
APP_DIR="$HOME/cartlex_app"
DOMAIN="deliverypartner.gocartlex.com"

# ── Find correct PHP 8.2+ binary ─────────────────────────────────
find_php() {
  for bin in php8.4 php8.3 php8.2 \
    /usr/local/lsws/lsphp83/bin/php \
    /usr/local/lsws/lsphp82/bin/php \
    /opt/alt/php83/usr/bin/php \
    /opt/alt/php82/usr/bin/php \
    /usr/bin/php8.3 /usr/bin/php8.2; do
    if command -v "$bin" >/dev/null 2>&1 || [ -x "$bin" ]; then
      VER=$("$bin" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null)
      if [ "$(echo "$VER 8.2" | awk '{print ($1 >= $2)}')" = "1" ]; then
        echo "$bin"; return
      fi
    fi
  done
  # Last resort: current php if >= 8.2
  VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null)
  if [ "$(echo "$VER 8.2" | awk '{print ($1 >= $2)}')" = "1" ]; then
    echo "php"; return
  fi
  echo ""; return
}

PHP_BIN=$(find_php)
if [ -z "$PHP_BIN" ]; then
  echo "ERROR: No PHP 8.2+ found. Please set PHP 8.3 in hPanel → PHP Configuration, then re-run."
  exit 1
fi
echo "Using PHP: $PHP_BIN ($($PHP_BIN -r 'echo PHP_VERSION;'))"

# ── Clone or update app ───────────────────────────────────────────
if [ ! -d "$APP_DIR/.git" ]; then
  echo "Cloning from GitHub..."
  rm -rf "$APP_DIR"
  git clone --depth=1 --branch main https://github.com/Limitlexng/BEXX.git "$APP_DIR"
else
  echo "Updating from GitHub..."
  cd "$APP_DIR" && git fetch --depth=1 origin main && git reset --hard origin/main
fi

cd "$APP_DIR"

# ── Composer install via correct PHP ─────────────────────────────
echo "Installing PHP dependencies..."
COMPOSER_BIN=$(command -v composer || command -v composer2 || echo composer)
# Run install; suppress the post-install-cmd error — we re-run it after patching
"$PHP_BIN" "$COMPOSER_BIN" install \
  --no-dev --optimize-autoloader --no-interaction \
  --no-progress --ignore-platform-reqs 2>&1 | tail -5 || true

# ── PHP 8.3 compatibility patch ───────────────────────────────────
# laravel/framework ≥13.12 calls LeagueUri::new() which is PHP 8.4 syntax.
# Replace with createFromString() which is the PHP 8.3-compatible equivalent.
URI_FILE="$APP_DIR/vendor/laravel/framework/src/Illuminate/Support/Uri.php"
if [ -f "$URI_FILE" ] && grep -q 'LeagueUri::new(' "$URI_FILE"; then
  sed -i 's/LeagueUri::new(/LeagueUri::createFromString(/g' "$URI_FILE"
  echo "✓ Patched Uri.php: LeagueUri::new() → LeagueUri::createFromString()"
fi

# Re-run package discovery now that the syntax is fixed
"$PHP_BIN" artisan package:discover --ansi 2>&1 || true

# ── .env ─────────────────────────────────────────────────────────
if [ ! -f .env ]; then
  cp .env.example .env
fi
sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|" .env
grep -v "^DB_CONNECTION\|^DB_DATABASE\|^DB_HOST\|^DB_PORT\|^DB_USERNAME\|^DB_PASSWORD\|^SESSION_DRIVER\|^CACHE_STORE\|^QUEUE_CONNECTION\|^LOG_CHANNEL\|^LOG_LEVEL" .env > /tmp/.env_clean
mv /tmp/.env_clean .env
cat >> .env <<ENVBLOCK
DB_CONNECTION=sqlite
DB_DATABASE=$APP_DIR/database/database.sqlite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=single
LOG_LEVEL=error
ENVBLOCK
"$PHP_BIN" artisan key:generate --force

# ── Database ──────────────────────────────────────────────────────
touch database/database.sqlite
chmod 664 database/database.sqlite
chmod -R 775 storage bootstrap/cache
"$PHP_BIN" artisan migrate --force --no-interaction
COUNT=$("$PHP_BIN" artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | grep -o '[0-9]*' | tail -1)
if [ -z "$COUNT" ] || [ "$COUNT" = "0" ]; then
  "$PHP_BIN" artisan db:seed --force --no-interaction
fi

# ── Cache ─────────────────────────────────────────────────────────
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
"$PHP_BIN" artisan storage:link --force 2>/dev/null || true

# ── Wire up public_html ───────────────────────────────────────────
mkdir -p "$PUBLIC_HTML"
"$PHP_BIN" -r "
  \$appDir = '$APP_DIR';
  \$pubDir = '$PUBLIC_HTML';
  \$src    = file_get_contents(\$appDir . '/public/index.php');
  \$find   = [\"__DIR__.'/../vendor/autoload.php'\", \"__DIR__.'/../bootstrap/app.php'\", \"__DIR__.'/../storage/framework/maintenance.php'\"];
  \$rep    = [\"'\$appDir/vendor/autoload.php'\", \"'\$appDir/bootstrap/app.php'\", \"'\$appDir/storage/framework/maintenance.php'\"];
  file_put_contents(\$pubDir . '/index.php', str_replace(\$find, \$rep, \$src));
  echo 'index.php written' . PHP_EOL;
"
cp public/.htaccess "$PUBLIC_HTML/.htaccess"
cp -rf public/build "$PUBLIC_HTML/"
[ -d storage/app/public ] && cp -rf storage/app/public "$PUBLIC_HTML/storage" || true

# ── Sanity check ─────────────────────────────────────────────────
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " Deployment complete!"
echo " PHP:    $PHP_BIN ($($PHP_BIN -r 'echo PHP_VERSION;'))"
echo " App:    $APP_DIR"
echo " Web:    $PUBLIC_HTML"
[ -f "$PUBLIC_HTML/index.php" ] && echo " index:  ✓ $(wc -c < "$PUBLIC_HTML/index.php") bytes" || echo " index:  ✗ MISSING"
[ -f "$APP_DIR/vendor/autoload.php" ] && echo " vendor: ✓" || echo " vendor: ✗ MISSING"
[ -f "$APP_DIR/database/database.sqlite" ] && echo " db:     ✓ $(wc -c < "$APP_DIR/database/database.sqlite") bytes" || echo " db:     ✗"
echo ""
echo " https://deliverypartner.gocartlex.com"
echo " Login:  admin@gocartlex.com / Cartlex@2025!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
