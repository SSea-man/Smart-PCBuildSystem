<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$pdo = get_db();
try {
    // Drop check constraints if they exist. We might need to find the name first.
    $stmt = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'component' AND CONSTRAINT_TYPE = 'CHECK'");
    $constraints = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($constraints as $c) {
        $pdo->exec("ALTER TABLE component DROP CONSTRAINT `$c`");
        echo "Dropped constraint $c\n";
    }

    // Now fix the types directly!
    $pdo->exec("UPDATE component SET type = 'Keyboard' WHERE type = 'Input devices' AND (component_name LIKE '%Keyboard%' OR component_name LIKE '%Kumara%' OR component_name LIKE '%Azoth%')");
    $pdo->exec("UPDATE component SET type = 'Mouse' WHERE type = 'Input devices' AND type != 'Keyboard'"); // The rest are mice/adapters
    $pdo->exec("UPDATE component SET type = 'Cooling' WHERE type = 'Output devices' AND (component_name LIKE '%Cooler%' OR component_name LIKE '%Fan%' OR component_name LIKE '%Liquid%' OR component_name LIKE '%Noctua%' OR component_name LIKE '%Kraken%')");
    $pdo->exec("UPDATE component SET type = 'Monitor' WHERE type = 'Output devices' AND (component_name LIKE '%\"%' OR component_name LIKE '%Hz%') AND type != 'Cooling'");
    $pdo->exec("UPDATE component SET type = 'Case' WHERE type = 'Case (body)' OR type = 'Casing'");

    echo "Database types fixed!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
