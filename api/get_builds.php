<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/scoring.php';

header('Content-Type: application/json');
if (!is_logged_in()) { json_response(['error'=>'Unauthorized'], 401); }

$body    = json_decode(file_get_contents('php://input'), true) ?? [];
$purpose = sanitise($body['purpose'] ?? input('purpose','general'));
$budget  = (float)($body['budget']   ?? input('budget', 80000));

json_response(get_top_builds($purpose, $budget));
