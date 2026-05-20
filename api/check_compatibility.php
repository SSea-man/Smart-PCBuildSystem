<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/compatibility.php';

header('Content-Type: application/json');
if (!is_logged_in()) { json_response(['error'=>'Unauthorized'], 401); }

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$ids  = $body['component_ids'] ?? [];

if (empty($ids)) { json_response(['compatible'=>true,'pass'=>true,'issues'=>[],'errors'=>[]]); }

// Load component rows
$components = [];
foreach ($ids as $cat => $id) {
    $id = (int)$id;
    if (!$id) continue;
    $row = db_row(component_base_sql() . ' WHERE c.component_id = ?', [$id]);
    if ($row) { $row['stock_status'] = normalize_stock($row['stock_status_raw']??''); $components[$cat] = $row; }
}

$result = check_compatibility($components);
// Normalise: return both 'compatible' (test expectation) and 'pass' (legacy key)
json_response([
    'compatible' => $result['pass'],
    'pass'       => $result['pass'],
    'issues'     => $result['errors'],
    'errors'     => $result['errors'],
]);
