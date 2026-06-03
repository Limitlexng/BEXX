#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────
# Cartlex Fleet Portal — Deploy to Shared Hosting Server
# Run from your PC: bash deploy-to-server.sh
# ─────────────────────────────────────────────────────────────────
set -e
cd "$(dirname "$0")"

# ── Server settings ───────────────────────────────────────────────
SSH_HOST="88.223.85.95"
SSH_PORT="65002"
SSH_USER="u503991995"
SSH_PASS="Seasons*10"
# ─────────────────────────────────────────────────────────────────

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
info()    { echo -e "${GREEN}▶ $*${NC}"; }
success() { echo -e "${GREEN}✓ $*${NC}"; }
die()     { echo -e "${RED}✗ $*${NC}"; exit 1; }

echo ""
echo "  ╔═══════════════════════════════════════╗"
echo "  ║   Cartlex Fleet Partner Portal        ║"
echo "  ║   Deploy to Server                    ║"
echo "  ╚═══════════════════════════════════════╝"
echo ""

# ── SSH / SCP commands ────────────────────────────────────────────
SSH_OPTS="-o StrictHostKeyChecking=no -o ConnectTimeout=20 -p $SSH_PORT"

if command -v sshpass >/dev/null 2>&1; then
  SSHPASS="sshpass -p '$SSH_PASS'"
else
  SSHPASS=""
  echo -e "${YELLOW}⚠ sshpass not found — you will be prompted for password${NC}"
fi

do_ssh() { eval "$SSHPASS ssh $SSH_OPTS $SSH_USER@$SSH_HOST $*"; }
do_scp() { eval "$SSHPASS scp -P $SSH_PORT -o StrictHostKeyChecking=no $*"; }

# ── Test connection ───────────────────────────────────────────────
info "Testing connection..."
do_ssh "'echo connected && php --version | head -1'" || die "Cannot connect. Check host/port/password."
success "Connected!"

# ── Build assets ─────────────────────────────────────────────────
if command -v npm >/dev/null 2>&1; then
  info "Building frontend assets..."
  npm run build 2>&1 | tail -3
fi

# ── Create deployment archive ─────────────────────────────────────
info "Creating deployment archive..."
ARCHIVE="/tmp/cartlex_$(date +%s).tar.gz"
tar -czf "$ARCHIVE" \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='tests' \
  --exclude='.github' \
  --exclude='.env' \
  --exclude='*.log' \
  --exclude='database/database.sqlite' \
  .
info "Archive: $(du -sh "$ARCHIVE" | cut -f1)"

# ── Upload archive + setup script ────────────────────────────────
info "Uploading to server (~1-2 min)..."
do_scp "'$ARCHIVE' '$USER@$SSH_HOST:~/cartlex_deploy.tar.gz'" 2>/dev/null || \
  do_scp "$ARCHIVE $SSH_USER@$SSH_HOST:~/cartlex_deploy.tar.gz"
do_scp "deploy-setup-remote.sh $SSH_USER@$SSH_HOST:~/deploy-setup-remote.sh"
rm "$ARCHIVE"
success "Uploaded!"

# ── Run remote setup script ───────────────────────────────────────
info "Running remote setup (takes ~2 min for composer install)..."
do_ssh "'chmod +x ~/deploy-setup-remote.sh && bash ~/deploy-setup-remote.sh && rm ~/deploy-setup-remote.sh'"

success "Deployment complete!"
echo ""
echo "  Open your domain in a browser and log in with:"
echo "  Email:    admin@gocartlex.com"
echo "  Password: Cartlex@2025!"
echo ""
