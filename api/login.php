<?php
/**
 * api/login.php — JSON API endpoint for programmatic login (no CSRF required).
 * Returns a session cookie. Used by automated tests and mobile clients.
 * Security: rate-limited by IP via failed attempt tracking.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$body     = json_decode(file_get_contents('php://input'), true) ?? [];
$email    = strtolower(trim($body['email'] ?? input('email', '')));
$password = $body['password'] ?? input('password', '');

if (!$email || !$password) {
    json_response(['error' => 'email and password are required'], 400);
}

$user = attempt_login($email, $password);
if (!$user) {
    json_response(['error' => 'Invalid credentials'], 401);
}

$user_safe = [
    'id'    => $user['user_id'],
    'name'  => $user['user_name'],
    'email' => $user['email'],
    'role'  => $user['role'] ?? 'user',
];

login_user($user); // This still sets the cookie and session
$token = generate_jwt(['user' => $user_safe]);

json_response([
    'success' => true,
    'token'   => $token,
    'user'    => $user_safe,
]);
