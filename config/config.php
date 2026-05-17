<?php
/**
 * GoBuff: Gym Hub - Application Configuration
 */

define('APP_NAME', 'GoBuff: Gym Hub');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/GoBuff/public');
define('APP_ROOT', dirname(__DIR__));
define('APP_ENV', 'development'); // development | production

// Path constants
define('APP_PATH',     APP_ROOT . '/app');
define('PUBLIC_PATH',  APP_ROOT . '/public');
define('STORAGE_PATH', APP_ROOT . '/storage');
define('VIEWS_PATH',   APP_PATH . '/views');

// Session
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'gobuff_session');

// Pagination
define('RECORDS_PER_PAGE', 15);

// Upload settings
define('UPLOAD_PATH', PUBLIC_PATH . '/assets/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Security
define('CSRF_TOKEN_NAME', 'gobuff_csrf_token');
define('BCRYPT_COST', 12);

// Timezone
date_default_timezone_set('Asia/Manila');

/**
 * Read a value from $_ENV / getenv(), falling back to $default.
 */
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        $val = $_ENV[$key] ?? getenv($key);
        return ($val !== false && $val !== null && $val !== '') ? $val : $default;
    }
}

// Load .env file if present (simple key=value parser)
$envFile = APP_ROOT . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $key = trim($key); $val = trim($val);
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $val;
                putenv("{$key}={$val}");
            }
        }
    }
}

// PayMongo
define('PAYMONGO_SECRET_KEY',     env('PAYMONGO_SECRET_KEY',     'sk_test_REPLACE_ME'));
define('PAYMONGO_PUBLIC_KEY',     env('PAYMONGO_PUBLIC_KEY',     'pk_test_REPLACE_ME'));
define('PAYMONGO_WEBHOOK_SECRET', env('PAYMONGO_WEBHOOK_SECRET', ''));
define('PAYMONGO_API_URL',        'https://api.paymongo.com/v1');

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/core/',
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/middleware/',
        APP_PATH . '/services/',
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Composer autoloader (PHPMailer, League OAuth2, etc.)
$composerAutoload = APP_ROOT . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}
