<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!is_post() || !is_logged_in()) {
    json_response(['error' => 'Unauthorized'], 401);
}

verify_csrf();

$user_id = get_auth_user()['id'];
$name = trim(input('name', ''));
$description = trim(input('description', ''));

if (empty($name)) {
    json_response(['error' => 'Community name is required.'], 400);
}

if (strpos(strtolower($name), 'pcb/') === 0) {
    $name = substr($name, 4);
} elseif (strpos(strtolower($name), 'r/') === 0) {
    $name = substr($name, 2);
}
$name = preg_replace('/\s+/', '', $name);

if (!preg_match('/^[a-zA-Z0-9]{3,30}$/', $name)) {
    json_response(['error' => 'Community name must be 3-30 alphanumeric characters with no spaces.'], 400);
}

if (strlen($description) > 500) {
    json_response(['error' => 'Description must not exceed 500 characters.'], 400);
}

$exists = db_row("SELECT community_id FROM community WHERE LOWER(name) = LOWER(?)", [$name]);
if ($exists) {
    json_response(['error' => 'A community with this name already exists.'], 400);
}

try {
    db_exec("INSERT INTO community (name, description, created_by, created_at) VALUES (?, ?, ?, NOW())", [
        $name, $description, $user_id
    ]);
    
    $community_id = db_row("SELECT LAST_INSERT_ID() AS id")['id'];
    
    db_exec("INSERT IGNORE INTO community_member (community_id, user_id) VALUES (?, ?)", [$community_id, $user_id]);
    
    json_response([
        'success' => true,
        'community_id' => $community_id,
        'name' => $name
    ]);
} catch (Exception $e) {
    json_response(['error' => 'Failed to create community: ' . $e->getMessage()], 500);
}
