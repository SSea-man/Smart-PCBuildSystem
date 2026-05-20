<?php
/**
 * api/logout.php — JSON API endpoint for programmatic logout (no CSRF required).
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');
logout_user();
json_response(['success' => true]);
