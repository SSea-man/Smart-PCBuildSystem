<?php
/**

 */


define('DB_HOST', 'localhost');
define('DB_NAME', 'project_alpha');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('APP_ENV',   'local');         
define('BASE_URL',  'http://localhost/myproject');
define('APP_NAME',  'PC Builder BD');

define('JWT_SECRET',        'CHANGE_ME_TO_A_256BIT_RANDOM_STRING_BEFORE_DEPLOY');
define('SESSION_LIFETIME',  7200);   
define('CSRF_TOKEN_LENGTH', 32);

define('CHATBOT_RATE_LIMIT', 20);

define('PSU_SAFETY_MARGIN', 1.20);   
define('TOP_BUILDS_LIMIT',  3);

if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

date_default_timezone_set('Asia/Dhaka');

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly',  1);
    ini_set('session.cookie_samesite', 'Lax');
    if (APP_ENV === 'production') {
        ini_set('session.cookie_secure', 1);
    }
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_start();
}
