<?php
/**
 * Cartlex Fleet Partner Portal — Web Installer
 * Upload this file to your public_html, then visit it in your browser.
 * It will install the full application automatically.
 */

session_start();
define('INSTALLER_LOCK', dirname(__FILE__) . '/../cartlex_app/.installed');
define('GITHUB_REPO',    'Limitlexng/BEXX');
define('GITHUB_BRANCH',  'main');

// ─────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────
function run(string $cmd): array {
    $output = []; $code = 0;
    exec($cmd . ' 2>&1', $output, $code);
    return ['code' => $code, 'out' => implode("\n", $output)];
}

function canExec(): bool {
    if (!function_exists('exec')) return false;
    $disabled = ini_get('disable_functions');
    return !in_array('exec', array_map('trim', explode(',', $disabled)));
}

function homeDir(): string {
    // public_html parent is the hosting home dir
    return rtrim(dirname($_SERVER['DOCUMENT_ROOT']), '/');
}

function appDir(): string  { return homeDir() . '/cartlex_app'; }
function publicDir(): string { return rtrim($_SERVER['DOCUMENT_ROOT'], '/'); }

function php(): string {
    foreach ([PHP_BINARY, 'php8.3', 'php8.2', 'php8.1', 'php8', 'php'] as $bin) {
        $r = run("$bin -r 'echo PHP_VERSION;' 2>/dev/null");
        if ($r['code'] === 0 && version_compare(trim($r['out']), '8.1', '>=')) {
            return $bin;
        }
    }
    return PHP_BINARY;
}

function composer(): string {
    foreach (['composer', 'composer2', 'composer.phar'] as $bin) {
        $r = run("$bin --version 2>/dev/null");
        if ($r['code'] === 0) return $bin;
    }
    // Try common Hostinger path
    $hpath = homeDir() . '/bin/composer';
    if (file_exists($hpath)) return $hpath;
    return 'composer';
}

function sysChecks(): array {
    $checks = [];
    $checks['PHP ≥ 8.1']       = version_compare(PHP_VERSION, '8.1', '>=');
    $checks['PDO SQLite']       = extension_loaded('pdo_sqlite');
    $checks['cURL']             = extension_loaded('curl');
    $checks['Zip']              = extension_loaded('zip');
    $checks['Mbstring']         = extension_loaded('mbstring');
    $checks['GD / Image']       = extension_loaded('gd');
    $checks['exec() enabled']   = canExec();
    $checks['public_html writable'] = is_writable(publicDir());
    $checks['Home dir writable']    = is_writable(homeDir());
    return $checks;
}

function allPassed(array $checks): bool {
    return !in_array(false, $checks, true);
}

// ─────────────────────────────────────────────────────────────────
// Already installed?
// ─────────────────────────────────────────────────────────────────
if (file_exists(INSTALLER_LOCK) && ($_GET['step'] ?? '') !== 'delete') {
    header('Location: /');
    exit;
}

// ─────────────────────────────────────────────────────────────────
// POST: Run install
// ─────────────────────────────────────────────────────────────────
$log   = [];
$error = null;
$step  = $_GET['step'] ?? 'welcome';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'install') {
    $appUrl    = rtrim($_POST['app_url']    ?? '', '/') ?: ('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $appDir    = appDir();
    $pubDir    = publicDir();
    $phpBin    = php();
    $compBin   = composer();

    // 1. Clone or update repo
    if (!is_dir($appDir . '/.git')) {
        $log[] = '📦 Cloning application…';
        $r = run("git clone --depth=1 --branch " . GITHUB_BRANCH
              . " https://github.com/" . GITHUB_REPO . ".git $appDir");
        if ($r['code'] !== 0) { $error = 'Git clone failed: ' . $r['out']; goto done; }
    } else {
        $log[] = '🔄 Updating application…';
        run("cd $appDir && git fetch --depth=1 origin " . GITHUB_BRANCH
          . " && git reset --hard origin/" . GITHUB_BRANCH);
    }

    // 2. Composer install
    $log[] = '📚 Installing PHP dependencies (takes ~60s)…';
    $r = run("cd $appDir && $compBin install --no-dev --optimize-autoloader --no-interaction --no-progress --quiet --ignore-platform-reqs");
    if ($r['code'] !== 0) { $error = 'Composer failed: ' . $r['out']; goto done; }

    // 3. Write .env
    if (!file_exists($appDir . '/.env')) {
        $log[] = '⚙️  Writing .env…';
        $env = <<<ENV
APP_NAME="Cartlex Fleet Portal"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=$appUrl
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

DB_CONNECTION=sqlite
DB_DATABASE=$appDir/database/database.sqlite

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

LOG_CHANNEL=single
LOG_LEVEL=error

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@gocartlex.com"
MAIL_FROM_NAME="Cartlex"
ENV;
        file_put_contents($appDir . '/.env', $env);
    }

    // 4. Touch SQLite DB & set permissions
    $log[] = '🗄️  Preparing database…';
    touch($appDir . '/database/database.sqlite');
    chmod($appDir . '/database/database.sqlite', 0664);
    run("chmod -R 775 $appDir/storage $appDir/bootstrap/cache");

    // 5. Artisan key:generate
    $r = run("cd $appDir && $phpBin artisan key:generate --force --no-ansi");
    if ($r['code'] !== 0) { $error = 'key:generate failed: ' . $r['out']; goto done; }

    // 6. Migrate & seed
    $log[] = '🌱 Running migrations & seeding demo data…';
    $r = run("cd $appDir && $phpBin artisan migrate --force --seed --no-interaction --no-ansi");
    if ($r['code'] !== 0) { $error = 'Migration failed: ' . $r['out']; goto done; }

    // 7. Cache
    $log[] = '⚡ Caching config, routes, views…';
    run("cd $appDir && $phpBin artisan config:cache --no-ansi");
    run("cd $appDir && $phpBin artisan route:cache --no-ansi");
    run("cd $appDir && $phpBin artisan view:cache --no-ansi");
    run("cd $appDir && $phpBin artisan storage:link --force --no-ansi 2>/dev/null");

    // 8. Wire up public_html
    $log[] = '🔗 Wiring up public_html…';

    // index.php with absolute paths
    $indexPhp = <<<PHP
<?php
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
define('LARAVEL_START', microtime(true));
if (file_exists(\$m = '$appDir/storage/framework/maintenance.php')) { require \$m; }
require '$appDir/vendor/autoload.php';
/** @var Application \$app */
\$app = require_once '$appDir/bootstrap/app.php';
\$app->handleRequest(Request::capture());
PHP;
    file_put_contents($pubDir . '/index.php', $indexPhp);

    // .htaccess
    if (file_exists($appDir . '/public/.htaccess')) {
        copy($appDir . '/public/.htaccess', $pubDir . '/.htaccess');
    }

    // Copy build assets (already compiled in repo)
    if (is_dir($appDir . '/public/build')) {
        run("cp -r $appDir/public/build $pubDir/build");
    }

    // Public storage
    if (is_dir($appDir . '/storage/app/public')) {
        run("cp -r $appDir/storage/app/public $pubDir/storage");
    }

    // 9. Mark as installed
    file_put_contents(INSTALLER_LOCK, date('Y-m-d H:i:s'));

    $log[] = '✅ Installation complete!';
    $step = 'complete';
    done:
}

// ─────────────────────────────────────────────────────────────────
// Self-destruct
// ─────────────────────────────────────────────────────────────────
if ($step === 'delete') {
    @unlink(__FILE__);
    header('Location: /');
    exit;
}

// ─────────────────────────────────────────────────────────────────
// HTML OUTPUT
// ─────────────────────────────────────────────────────────────────
$checks = sysChecks();
$allOk  = allPassed($checks);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cartlex Fleet Portal — Installer</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body { background: #0D1B2A; }
  .card { background: #1a2a3a; border: 1px solid #2a3a4a; }
  .btn-red { background: #C41E3A; }
  .btn-red:hover { background: #a01830; }
  pre { white-space: pre-wrap; word-break: break-word; }
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-2xl">

  <!-- Logo -->
  <div class="text-center mb-8">
    <div class="inline-flex items-center gap-3 mb-2">
      <div class="w-10 h-10 rounded-lg btn-red flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0
               3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0
               00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946
               3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42
               3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0
               01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438
               3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
      </div>
      <span class="text-2xl font-bold text-white">Cartlex</span>
    </div>
    <p class="text-slate-400 text-sm">Fleet Partner Portal — Web Installer</p>
  </div>

  <?php if ($step === 'welcome'): ?>
  <!-- ── STEP 1: Welcome + System Check ── -->
  <div class="card rounded-2xl p-8">
    <h2 class="text-white text-xl font-semibold mb-6">System Requirements</h2>
    <div class="space-y-3 mb-8">
      <?php foreach ($checks as $label => $pass): ?>
      <div class="flex items-center justify-between py-2 border-b border-slate-700 last:border-0">
        <span class="text-slate-300 text-sm"><?= htmlspecialchars($label) ?></span>
        <?php if ($pass): ?>
          <span class="text-green-400 text-sm font-medium">✓ Pass</span>
        <?php else: ?>
          <span class="text-red-400 text-sm font-medium">✗ Fail</span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (!$allOk): ?>
    <div class="bg-red-900/30 border border-red-700 rounded-lg p-4 mb-6">
      <p class="text-red-300 text-sm">One or more requirements failed. Please contact your hosting provider to enable the missing features.</p>
    </div>
    <?php endif; ?>

    <div class="text-slate-400 text-sm mb-6 space-y-1">
      <p>• App files will be installed to: <code class="text-slate-200"><?= homeDir() ?>/cartlex_app/</code></p>
      <p>• Web root used: <code class="text-slate-200"><?= publicDir() ?>/</code></p>
      <p>• Install time: ~2 minutes</p>
    </div>

    <a href="?step=configure"
       class="<?= $allOk ? 'btn-red cursor-pointer' : 'bg-slate-600 cursor-not-allowed pointer-events-none' ?>
              block w-full text-center text-white font-semibold py-3 rounded-xl transition">
      Continue →
    </a>
  </div>

  <?php elseif ($step === 'configure'): ?>
  <!-- ── STEP 2: Configuration ── -->
  <div class="card rounded-2xl p-8">
    <h2 class="text-white text-xl font-semibold mb-6">Configuration</h2>
    <form method="POST" action="?step=install">
      <input type="hidden" name="step" value="install">

      <div class="mb-5">
        <label class="block text-slate-300 text-sm font-medium mb-2">Application URL</label>
        <input type="text" name="app_url"
               value="https://<?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? '') ?>"
               placeholder="https://yourdomain.com"
               class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2.5
                      text-white text-sm focus:outline-none focus:border-red-500">
        <p class="text-slate-500 text-xs mt-1">The full URL where this portal will be accessed.</p>
      </div>

      <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4 mb-6">
        <p class="text-slate-300 text-sm font-medium mb-2">Demo Accounts (created automatically)</p>
        <div class="space-y-1 text-xs text-slate-400">
          <div class="flex justify-between">
            <span>Super Admin</span>
            <span class="text-slate-300">admin@gocartlex.com / Cartlex@2025!</span>
          </div>
          <div class="flex justify-between">
            <span>Finance Admin</span>
            <span class="text-slate-300">finance@gocartlex.com / Cartlex@2025!</span>
          </div>
          <div class="flex justify-between">
            <span>Demo Partner</span>
            <span class="text-slate-300">demo@partner.com / Demo@2025!</span>
          </div>
        </div>
      </div>

      <button type="submit"
              class="btn-red w-full text-white font-semibold py-3 rounded-xl transition">
        Install Now →
      </button>
    </form>
  </div>

  <?php elseif ($step === 'install' && $error): ?>
  <!-- ── INSTALL ERROR ── -->
  <div class="card rounded-2xl p-8">
    <h2 class="text-red-400 text-xl font-semibold mb-4">Installation Failed</h2>
    <div class="bg-red-900/20 border border-red-700 rounded-lg p-4 mb-6">
      <pre class="text-red-300 text-xs"><?= htmlspecialchars($error) ?></pre>
    </div>
    <?php if ($log): ?>
    <div class="bg-slate-800 rounded-lg p-4 mb-6">
      <p class="text-slate-400 text-xs font-medium mb-2">Progress before error:</p>
      <?php foreach ($log as $line): ?>
        <p class="text-slate-300 text-xs"><?= htmlspecialchars($line) ?></p>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <a href="?step=configure" class="btn-red block w-full text-center text-white font-semibold py-3 rounded-xl">
      ← Try Again
    </a>
  </div>

  <?php elseif ($step === 'complete'): ?>
  <!-- ── STEP 3: Complete ── -->
  <div class="card rounded-2xl p-8 text-center">
    <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
      <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
    </div>
    <h2 class="text-white text-2xl font-bold mb-2">Installation Complete!</h2>
    <p class="text-slate-400 text-sm mb-8">Cartlex Fleet Partner Portal is ready.</p>

    <?php if ($log): ?>
    <div class="bg-slate-800 rounded-lg p-4 mb-6 text-left">
      <?php foreach ($log as $line): ?>
        <p class="text-slate-300 text-xs"><?= htmlspecialchars($line) ?></p>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4 mb-6 text-left">
      <p class="text-slate-300 text-sm font-medium mb-3">Login Credentials</p>
      <div class="space-y-2 text-xs">
        <div class="flex justify-between text-slate-400">
          <span>Super Admin</span>
          <span class="text-white font-mono">admin@gocartlex.com / Cartlex@2025!</span>
        </div>
        <div class="flex justify-between text-slate-400">
          <span>Finance Admin</span>
          <span class="text-white font-mono">finance@gocartlex.com / Cartlex@2025!</span>
        </div>
        <div class="flex justify-between text-slate-400">
          <span>Demo Partner</span>
          <span class="text-white font-mono">demo@partner.com / Demo@2025!</span>
        </div>
      </div>
    </div>

    <div class="flex gap-3">
      <a href="?step=delete"
         class="flex-1 bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm font-medium
                py-2.5 rounded-xl text-center transition">
        🗑 Delete Installer
      </a>
      <a href="/"
         class="flex-1 btn-red text-white text-sm font-medium py-2.5 rounded-xl text-center transition">
        Visit Site →
      </a>
    </div>
    <p class="text-slate-500 text-xs mt-4">⚠️ Delete the installer file after visiting your site for security.</p>
  </div>

  <?php else: ?>
  <!-- Redirect back to welcome if step unknown -->
  <script>location.href='?step=welcome'</script>
  <?php endif; ?>

</div>
</body>
</html>
