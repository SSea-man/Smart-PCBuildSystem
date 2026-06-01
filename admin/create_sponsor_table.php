<?php

require_once __DIR__ . '/../includes/db.php';

$sql = "CREATE TABLE IF NOT EXISTS sponsor_ads (
  ad_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  image_url VARCHAR(512) NOT NULL,
  link_url VARCHAR(512) NOT NULL,
  description TEXT,
  active TINYINT(1) DEFAULT 1,
  start_date DATE NULL,
  end_date DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (db_exec($sql)) {
    echo "Table sponsor_ads created successfully.";
} else {
    echo "Failed to create sponsor_ads table.";
}
?>
