<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');
if (!is_logged_in()) { json_response(['error' => 'Unauthorized'], 401); }

$body     = json_decode(file_get_contents('php://input'), true) ?? [];
$category = sanitise($body['category'] ?? input('category', ''));
$budget   = (float)($body['budget_max'] ?? input('budget_max', 0));

$base   = component_base_sql();
$where  = [];
$params = [];

if ($category) {
    $where[]  = 'c.type LIKE ?';
    $params[] = "{$category}%";
}
if ($budget > 0) {
    $where[]  = 'COALESCE(sa.price, 0) <= ?';
    $params[] = $budget;
}

$where_sql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
$sql       = $base . $where_sql . ' ORDER BY c.benchmark_score DESC LIMIT 50';
$rows      = db_query($sql, $params);

foreach ($rows as &$r) {
    $r['stock_status'] = normalize_stock($r['stock_status_raw'] ?? '');
}
unset($r);

json_response(array_values($rows));
