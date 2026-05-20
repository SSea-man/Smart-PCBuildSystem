<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/wattage.php';
header('Content-Type: application/json');
if (!is_logged_in()) { json_response(['error'=>'Unauthorized'],401); }
$body    = json_decode(file_get_contents('php://input'),true) ?? [];
$uid     = (int)get_auth_user()['id'];
$comps   = array_map('intval', $body['components'] ?? []);
$total   = (float)($body['total_bdt'] ?? 0);
$score   = (float)($body['score']     ?? 0);
$purpose = sanitise($body['purpose']  ?? 'general');
$name    = sanitise($body['name']     ?? 'My Build');
$fps     = (int)($body['fps']         ?? 0);
$wattage = (int)($body['wattage']     ?? 0);
if (empty($comps) || $total <= 0) { json_response(['error'=>'Invalid build data'],400); }

$build_id = (int)db_exec(
    'INSERT INTO `build` (user_id, total_price, fps, wattage, name, purpose, score) VALUES (?,?,?,?,?,?,?)',
    [$uid, $total, $fps, $wattage, $name, $purpose, $score]
);

foreach ($comps as $cid) {
    if ($cid) db_exec('INSERT IGNORE INTO buildcomponent (build_id, component_id) VALUES (?,?)', [$build_id, $cid]);
}
json_response(['success'=>true,'build_id'=>$build_id]);
