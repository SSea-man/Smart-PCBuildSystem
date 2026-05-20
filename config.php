<?php
/**
 * config.php — Central environment configuration
 * PC Builder & Recommendation System
 *
 * PRODUCTION: Replace all placeholder values before deployment.
 * This is the ONLY file that differs between local and production.
 */

// ── Database ─────────────────────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'project_alpha');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ── Application ───────────────────────────────────────────────────────────────
define('APP_ENV',   'local');          // 'local' | 'production'
define('BASE_URL',  'http://localhost/myproject'); // no trailing slash
define('APP_NAME',  'PC Builder BD');

// ── Security ──────────────────────────────────────────────────────────────────
define('JWT_SECRET',        'CHANGE_ME_TO_A_256BIT_RANDOM_STRING_BEFORE_DEPLOY');
define('SESSION_LIFETIME',  7200);   // seconds (2 hours)
define('CSRF_TOKEN_LENGTH', 32);

// ── AI / Chatbot ──────────────────────────────────────────────────────────────
define('ANTHROPIC_API_KEY', 'YOUR_KEY_HERE');
define('ANTHROPIC_MODEL',   'claude-sonnet-4-20250514');
define('CHATBOT_RATE_LIMIT', 20);    // requests per hour per user

// ── Build System ──────────────────────────────────────────────────────────────
define('PSU_SAFETY_MARGIN', 1.20);   // 20% headroom over calculated TDP
define('TOP_BUILDS_LIMIT',  3);

// ── Runtime ───────────────────────────────────────────────────────────────────
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

date_default_timezone_set('Asia/Dhaka');

// Session hardening
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly',  1);
    ini_set('session.cookie_samesite', 'Lax');
    if (APP_ENV === 'production') {
        ini_set('session.cookie_secure', 1);
    }
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_start();
}
