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
$community_id = (int)input('community_id', 0);

if (!$community_id) {
    json_response(['error' => 'Invalid parameters'], 400);
}

$community = db_row("SELECT community_id FROM community WHERE community_id = ?", [$community_id]);
if (!$community) {
    json_response(['error' => 'Community not found'], 404);
}

$existing = db_row("SELECT * FROM community_member WHERE community_id = ? AND user_id = ?", [$community_id, $user_id]);

if ($existing) {
    db_exec("DELETE FROM community_member WHERE community_id = ? AND user_id = ?", [$community_id, $user_id]);
    $joined = false;
} else {
    db_exec("INSERT INTO community_member (community_id, user_id) VALUES (?, ?)", [$community_id, $user_id]);
    $joined = true;
}

$member_count = (int)db_row("SELECT COUNT(*) c FROM community_member WHERE community_id = ?", [$community_id])['c'];

json_response([
    'success' => true,
    'joined' => $joined,
    'member_count' => $member_count
]);
