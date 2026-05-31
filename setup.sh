#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────
# Cartlex Fleet Portal — Local Setup Script
# Run once after cloning: bash setup.sh
# ─────────────────────────────────────────────────────────────────
set -e
cd "$(dirname "$0")"

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
info()    { echo -e "${GREEN}▶ $*${NC}"; }
warn()    { echo -e "${YELLOW}⚠ $*${NC}"; }
success() { echo -e "${GREEN}✓ $*${NC}"; }
die()     { echo -e "${RED}✗ $*${NC}"; exit 1; }

echo ""
echo "  ╔═══════════════════════════════════════╗"
echo "  ║   Cartlex Fleet Partner Portal        ║"
echo "  ║   Local Setup                         ║"
echo "  ╚═══════════════════════════════════════╝"
echo ""

# ── Dependency checks ──
command -v php  >/dev/null 2>&1 || die "PHP not found. Install PHP 8.1+ first."
command -v composer >/dev/null 2>&1 || die "Composer not found. Install from https://getcomposer.org"
command -v npm  >/dev/null 2>&1 || warn "npm not found — skipping asset build (pre-built assets will be used)"

PHP_VER=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
info "PHP $PHP_VER detected"

# ── PHP dependencies ──
info "Installing PHP dependencies..."
composer install --no-interaction --optimize-autoloader

# ── JS dependencies & build ──
if command -v npm >/dev/null 2>&1; then
  info "Installing JS dependencies and building assets..."
  npm install
  npm run build
else
  info "Using pre-built assets from public/build/"
fi

# ── Environment ──
if [ ! -f .env ]; then
  info "Creating .env file..."
  cp .env.example .env
  php artisan key:generate
else
  info ".env already exists — skipping"
fi

# ── Database ──
info "Setting up SQLite database..."
touch database/database.sqlite

info "Running migrations and seeding demo data..."
php artisan migrate --force --seed --no-interaction

# ── Storage ──
info "Linking storage..."
php artisan storage:link --force 2>/dev/null || true

echo ""
success "Setup complete!"
echo ""
echo "  Demo credentials:"
echo "  ┌────────────────┬───────────────────────────┬──────────────────┐"
echo "  │ Role           │ Email                     │ Password         │"
echo "  ├────────────────┼───────────────────────────┼──────────────────┤"
echo "  │ Super Admin    │ admin@gocartlex.com        │ Cartlex@2025!    │"
echo "  │ Finance Admin  │ finance@gocartlex.com      │ Cartlex@2025!    │"
echo "  │ Demo Partner   │ demo@partner.com           │ Demo@2025!       │"
echo "  └────────────────┴───────────────────────────┴──────────────────┘"
echo ""
echo "  Start the server:"
echo "    php artisan serve"
echo ""
echo "  Then open: http://localhost:8000"
echo ""
