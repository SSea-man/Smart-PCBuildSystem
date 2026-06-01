<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

$sql = "SELECT * FROM sponsor_ads WHERE active = 1 
        AND (start_date IS NULL OR start_date <= CURDATE()) 
        AND (end_date IS NULL OR end_date >= CURDATE()) 
        ORDER BY created_at DESC LIMIT 1";
$ad = db_row($sql);
if ($ad) {
    echo json_encode(['success' => true, 'ad' => $ad]);
} else {
    echo json_encode(['success' => false, 'ad' => null]);
}
?>
