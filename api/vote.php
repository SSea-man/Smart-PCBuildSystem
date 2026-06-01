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
$type = input('type', ''); 
$id = (int)input('id', 0);

if (!in_array($type, ['post', 'comment']) || !$id) {
    json_response(['error' => 'Invalid parameters'], 400);
}

$col = $type === 'post' ? 'post_id' : 'comment_id';
$existing = db_row("SELECT vote_id FROM vote WHERE user_id = ? AND $col = ?", [$user_id, $id]);

if ($existing) {
    db_exec("DELETE FROM vote WHERE vote_id = ?", [$existing['vote_id']]);
    $user_voted = false;
} else {
    db_exec("INSERT INTO vote (user_id, $col, vote_type, created_at) VALUES (?, ?, 'upvote', NOW())", [$user_id, $id]);
    $user_voted = true;
}

$new_score = (int)db_row("SELECT COUNT(*) c FROM vote WHERE $col = ? AND vote_type = 'upvote'", [$id])['c'];

json_response([
    'success' => true, 
    'new_score' => $new_score,
    'user_voted' => $user_voted
]);
