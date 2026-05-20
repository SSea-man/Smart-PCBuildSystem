<?php
/**
 
 */

function _jwt_base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function _jwt_base64url_decode(string $data): string {
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
}
function generate_jwt(array $payload): string {
    $header  = _jwt_base64url_encode(json_encode(['alg'=>'HS256','typ'=>'JWT']));
    $payload['iat'] = $payload['iat'] ?? time();
    $payload['exp'] = $payload['exp'] ?? time() + SESSION_LIFETIME;
    $body    = _jwt_base64url_encode(json_encode($payload));
    $sig     = _jwt_base64url_encode(hash_hmac('sha256', "{$header}.{$body}", JWT_SECRET, true));
    return "{$header}.{$body}.{$sig}";
}
function verify_jwt(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    [$header, $body, $sig] = $parts;
    $expected = _jwt_base64url_encode(hash_hmac('sha256', "{$header}.{$body}", JWT_SECRET, true));
    if (!hash_equals($expected, $sig)) return null;
    $payload = json_decode(_jwt_base64url_decode($body), true);
    if (!is_array($payload) || (isset($payload['exp']) && $payload['exp'] < time())) return null;
    return $payload;
}

function get_auth_user(): ?array {
    if (!empty($_SESSION['user'])) return $_SESSION['user'];
    
    $token = $_COOKIE['jwt'] ?? '';
    if (!$token) {
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            $token = $matches[1];
        }
    }
    
    if ($token) {
        $payload = verify_jwt($token);
        if ($payload && isset($payload['user'])) {
            $_SESSION['user'] = $payload['user'];
            return $_SESSION['user'];
        }
    }
    return null;
}
function is_logged_in(): bool { return get_auth_user() !== null; }
function is_admin(): bool {
    $u = get_auth_user();
    return $u && ($u['role'] ?? '') === 'admin';
}
function require_auth(string $role = 'user'): void {
    $user = get_auth_user();
    if (!$user) { flash_message('warning','Please log in to continue.'); redirect('login.php'); }
    if ($role === 'admin' && ($user['role'] ?? '') !== 'admin') {
        flash_message('danger','Access denied.'); redirect('dashboard.php');
    }
}

/**

 */
function attempt_login(string $email, string $password): ?array {

    $user = db_row('SELECT * FROM `user` WHERE email = ?', [strtolower(trim($email))]);
    if (!$user) return null;

    $hash = $user['user_password'];
    $ok   = false;

    if (password_verify($password, $hash)) {
        $ok = true;
    } elseif ($hash === $password) {

        $ok = true;
        $new_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        db_exec('UPDATE `user` SET user_password=? WHERE user_id=?', [$new_hash, $user['user_id']]);
    }

    return $ok ? $user : null;
}

function login_user(array $user): void {
    $safe = [
        'id'    => $user['user_id'],
        'name'  => $user['user_name'],
        'email' => $user['email'],
        'role'  => $user['role'] ?? 'user',
    ];
    $_SESSION['user'] = $safe;
    $token = generate_jwt(['user' => $safe]);
    setcookie('jwt', $token, [
        'expires'  => time() + SESSION_LIFETIME,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => (APP_ENV === 'production'),
    ]);
}

function logout_user(): void {
    $_SESSION = [];
    session_destroy();
    setcookie('jwt', '', time() - 3600, '/', '', APP_ENV === 'production', true);
}
