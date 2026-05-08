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
