<?php
// api/watchlist.php — project_alpha schema (watchlist table created by migration)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');
if (!is_logged_in()) { json_response(['error'=>'Unauthorized'],401); }
$body         = json_decode(file_get_contents('php://input'),true) ?? [];
$action       = sanitise($body['action'] ?? '');
$component_id = (int)($body['component_id'] ?? 0);
$uid          = (int)get_auth_user()['id'];
if (!in_array($action,['add','remove']) || !$component_id) { json_response(['error'=>'Invalid parameters'],400); }
if ($action === 'add') {
    db_exec('INSERT IGNORE INTO watchlist (user_id, component_id) VALUES (?,?)', [$uid, $component_id]);
} else {
    db_exec('DELETE FROM watchlist WHERE user_id=? AND component_id=?', [$uid, $component_id]);
}
$count = (int)db_row('SELECT COUNT(*) c FROM watchlist WHERE user_id=?',[$uid])['c'];
json_response(['success'=>true,'count'=>$count,'action'=>$action]);
