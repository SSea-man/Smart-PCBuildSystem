<?php
// api/price_history.php — project_alpha: uses pricetracking table, new_price column
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');
if (!is_logged_in()) { json_response(['error'=>'Unauthorized'],401); }
$body  = json_decode(file_get_contents('php://input'),true) ?? [];
$id    = (int)(input('component_id',0) ?: ($body['component_id'] ?? 0));
$range = (int)(input('range',30) ?: 30);
$range = in_array($range,[30,90,180]) ? $range : 30;
if (!$id) { json_response(['error'=>'component_id required'],400); }
// Use pricetracking (old_price/new_price/changed_at)
$rows = db_query(
    'SELECT DATE(changed_at) as label, new_price as value FROM pricetracking
     WHERE component_id=? AND changed_at >= DATE_SUB(NOW(), INTERVAL ? DAY) ORDER BY changed_at',
    [$id, $range]
);
json_response(['labels'=>array_column($rows,'label'),'values'=>array_map(fn($r)=>(float)$r['value'],$rows)]);
