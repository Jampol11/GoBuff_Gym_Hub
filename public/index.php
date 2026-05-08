<?php
/**
 * GoBuff: Gym Hub - Front Controller
 */

define('GOBUFF_START', microtime(true));

// Load configuration
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/mail.php';

// Bootstrap application
$app = new App();
$app->run();
