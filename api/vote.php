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
$type = input('type', ''); // 'post' or 'comment'
$id = (int)input('id', 0);
$vote_type = input('vote', ''); // 'upvote' or 'downvote'

if (!in_array($type, ['post', 'comment']) || !$id || !in_array($vote_type, ['upvote', 'downvote'])) {
    json_response(['error' => 'Invalid parameters'], 400);
}

// Find existing vote
$col = $type === 'post' ? 'post_id' : 'comment_id';
$existing = db_row("SELECT vote_id, vote_type FROM vote WHERE user_id = ? AND $col = ?", [$user_id, $id]);

if ($existing) {
    if ($existing['vote_type'] === $vote_type) {
        // Toggle off if clicking the same vote button again
        db_exec("DELETE FROM vote WHERE vote_id = ?", [$existing['vote_id']]);
    } else {
        // Update to new vote type
        db_exec("UPDATE vote SET vote_type = ? WHERE vote_id = ?", [$vote_type, $existing['vote_id']]);
    }
} else {
    // Insert new vote
    db_exec("INSERT INTO vote (user_id, $col, vote_type, created_at) VALUES (?, ?, ?, NOW())", [$user_id, $id, $vote_type]);
}

// Recalculate score
$upvotes = (int)db_row("SELECT COUNT(*) c FROM vote WHERE $col = ? AND vote_type = 'upvote'", [$id])['c'];
$downvotes = (int)db_row("SELECT COUNT(*) c FROM vote WHERE $col = ? AND vote_type = 'downvote'", [$id])['c'];
$new_score = $upvotes - $downvotes;

json_response(['success' => true, 'new_score' => $new_score]);
