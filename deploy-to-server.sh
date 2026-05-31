#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────
# Cartlex Fleet Portal — Deploy to Shared Hosting Server
# Run from your PC: bash deploy-to-server.sh
# Requires: ssh, scp (or git-bash on Windows)
# ─────────────────────────────────────────────────────────────────
set -e
cd "$(dirname "$0")"

# ── Server settings (edit these) ──────────────────────────────────
SSH_HOST="88.223.85.95"
SSH_PORT="65002"
SSH_USER="u503991995"
SSH_PASS="Seasons*10"      # leave blank to use SSH key
APP_DIR="\$HOME/cartlex_app"
PUBLIC_HTML="\$HOME/public_html"
DOMAIN=""                  # e.g. yourdomain.com (auto-detected if blank)
# ─────────────────────────────────────────────────────────────────

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
info()    { echo -e "${GREEN}▶ $*${NC}"; }
warn()    { echo -e "${YELLOW}⚠ $*${NC}"; }
success() { echo -e "${GREEN}✓ $*${NC}"; }
die()     { echo -e "${RED}✗ $*${NC}"; exit 1; }

echo ""
echo "  ╔═══════════════════════════════════════╗"
echo "  ║   Cartlex Fleet Partner Portal        ║"
echo "  ║   Deploy to Server                    ║"
echo "  ╚═══════════════════════════════════════╝"
echo ""

# ── SSH helper (handles password or key) ─────────────────────────
SSH_OPTS="-o StrictHostKeyChecking=no -o ConnectTimeout=20 -p $SSH_PORT"

if [ -n "$SSH_PASS" ]; then
  if command -v sshpass >/dev/null 2>&1; then
    SSH_CMD="sshpass -p '$SSH_PASS' ssh $SSH_OPTS"
    SCP_CMD="sshpass -p '$SSH_PASS' scp -P $SSH_PORT -o StrictHostKeyChecking=no"
  else
    warn "sshpass not found — you will be prompted for the password each time."
    warn "Install sshpass: brew install sshpass (Mac) or apt install sshpass (Linux)"
    SSH_CMD="ssh $SSH_OPTS"
    SCP_CMD="scp -P $SSH_PORT -o StrictHostKeyChecking=no"
  fi
else
  SSH_CMD="ssh $SSH_OPTS"
  SCP_CMD="scp -P $SSH_PORT -o StrictHostKeyChecking=no"
fi

remote() { eval "$SSH_CMD $SSH_USER@$SSH_HOST '$1'"; }

# ── Test connection ───────────────────────────────────────────────
info "Testing connection to $SSH_HOST:$SSH_PORT..."
remote "echo 'connected'" || die "Cannot connect to server. Check credentials."
success "Connected!"

# ── Build assets locally ──────────────────────────────────────────
if command -v npm >/dev/null 2>&1; then
  info "Building frontend assets..."
  npm run build
else
  info "npm not found — using pre-built assets"
fi

# ── Create deployment archive ─────────────────────────────────────
info "Creating deployment archive..."
ARCHIVE="/tmp/cartlex_deploy_$(date +%s).tar.gz"
tar -czf "$ARCHIVE" \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='tests' \
  --exclude='.github' \
  --exclude='.env' \
  --exclude='*.log' \
  --exclude='database/database.sqlite' \
  .

ARCHIVE_SIZE=$(du -sh "$ARCHIVE" | cut -f1)
info "Archive size: $ARCHIVE_SIZE"

# ── Upload ────────────────────────────────────────────────────────
info "Uploading to server..."
eval "$SCP_CMD '$ARCHIVE' $SSH_USER@$SSH_HOST:~/cartlex_deploy.tar.gz"
rm "$ARCHIVE"
success "Uploaded!"

# ── Remote setup ─────────────────────────────────────────────────
info "Running remote setup..."
eval "$SSH_CMD $SSH_USER@$SSH_HOST" <<REMOTE
set -e
echo "▶ Extracting files..."
mkdir -p $APP_DIR
tar -xzf ~/cartlex_deploy.tar.gz -C $APP_DIR
rm ~/cartlex_deploy.tar.gz

cd $APP_DIR

echo "▶ Setting up .env..."
if [ ! -f .env ]; then
  APP_URL_VAL="${DOMAIN:+https://$DOMAIN}"
  cat > .env <<ENV
APP_NAME="Cartlex Fleet Portal"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=\${APP_URL_VAL:-https://\$(hostname -f 2>/dev/null || echo localhost)}
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

DB_CONNECTION=sqlite
DB_DATABASE=$APP_DIR/database/database.sqlite

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

LOG_CHANNEL=single
LOG_LEVEL=error

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@gocartlex.com"
MAIL_FROM_NAME="Cartlex"
ENV
  php artisan key:generate --force
fi

echo "▶ Setting permissions..."
touch database/database.sqlite
chmod 664 database/database.sqlite
chmod -R 775 storage bootstrap/cache

echo "▶ Running migrations..."
php artisan migrate --force --no-interaction

echo "▶ Seeding database..."
USER_COUNT=\$(php artisan tinker --execute="echo App\\\\Models\\\\User::count();" 2>/dev/null | grep -o '[0-9]*' | tail -1)
if [ -z "\$USER_COUNT" ] || [ "\$USER_COUNT" = "0" ]; then
  php artisan db:seed --force --no-interaction
fi

echo "▶ Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force 2>/dev/null || true

echo "▶ Wiring up public_html..."
php -r "
  \\\$appDir = getenv('HOME') . '/cartlex_app';
  \\\$pubDir  = getenv('HOME') . '/public_html';
  \\\$src     = file_get_contents(\\\$appDir . '/public/index.php');
  \\\$from    = [\"__DIR__.'/../vendor/autoload.php'\", \"__DIR__.'/../bootstrap/app.php'\", \"__DIR__.'/../storage/framework/maintenance.php'\"];
  \\\$to      = [\"'\$appDir/vendor/autoload.php'\", \"'\$appDir/bootstrap/app.php'\", \"'\$appDir/storage/framework/maintenance.php'\"];
  file_put_contents(\\\$pubDir . '/index.php', str_replace(\\\$from, \\\$to, \\\$src));
  echo 'index.php patched\n';
"
cp public/.htaccess $PUBLIC_HTML/.htaccess 2>/dev/null || true
cp -rf public/build $PUBLIC_HTML/
[ -d storage/app/public ] && cp -rf storage/app/public $PUBLIC_HTML/storage || true

echo ""
echo "✓ Deployment complete!"
REMOTE

success "Deployed successfully!"
echo ""
echo "  Your site should now be live."
echo "  Login: admin@gocartlex.com / Cartlex@2025!"
echo ""
