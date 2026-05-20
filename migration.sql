

USE `project_alpha`;

ALTER TABLE `user`
  ADD COLUMN IF NOT EXISTS `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

UPDATE `user` SET `role` = 'admin' WHERE `user_id` = 1;

ALTER TABLE `component`
  ADD COLUMN IF NOT EXISTS `brand`             VARCHAR(100) NOT NULL DEFAULT '',
  ADD COLUMN IF NOT EXISTS `benchmark_score`   DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `tdp_watts`         SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `socket`            VARCHAR(30) NOT NULL DEFAULT '',
  ADD COLUMN IF NOT EXISTS `ram_gen`           VARCHAR(10) NOT NULL DEFAULT '',
  ADD COLUMN IF NOT EXISTS `form_factor`       VARCHAR(10) NOT NULL DEFAULT '',
  ADD COLUMN IF NOT EXISTS `length_mm`         SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `height_mm`         SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `m2_slots`          TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `sata_ports`        TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `ram_slots`         TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `psu_wattage`       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `storage_interface` VARCHAR(10) NOT NULL DEFAULT '';

ALTER TABLE `build`
  ADD COLUMN IF NOT EXISTS `name`       VARCHAR(200) NOT NULL DEFAULT 'My Build',
  ADD COLUMN IF NOT EXISTS `purpose`    VARCHAR(50)  NOT NULL DEFAULT 'general',
  ADD COLUMN IF NOT EXISTS `score`      DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

CREATE TABLE IF NOT EXISTS `watchlist` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      INT NOT NULL,
  `component_id` INT NOT NULL,
  `added_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_watch` (`user_id`,`component_id`),
  CONSTRAINT `fk_wl_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wl_comp` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fps_profiles` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_slug`         VARCHAR(80)  NOT NULL,
  `game_name`         VARCHAR(160) NOT NULL,
  `difficulty_factor` DECIMAL(6,3) NOT NULL DEFAULT 1.000,
  `resolution`        VARCHAR(20)  NOT NULL DEFAULT '1080p',
  `quality`           VARCHAR(20)  NOT NULL DEFAULT 'Medium',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`game_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `fps_profiles` (`game_slug`,`game_name`,`difficulty_factor`,`resolution`,`quality`) VALUES
('valorant','Valorant',0.300,'1080p','High'),
('csgo2','Counter-Strike 2',0.400,'1080p','High'),
('pubg','PUBG: Battlegrounds',1.200,'1080p','Medium'),
('cyberpunk2077','Cyberpunk 2077',2.000,'1080p','High'),
('fortnite','Fortnite',0.700,'1080p','High'),
('gta5','GTA V',0.800,'1080p','High'),
('elden-ring','Elden Ring',1.400,'1080p','High'),
('apex-legends','Apex Legends',0.900,'1080p','High'),
('davinci-resolve','DaVinci Resolve',1.800,'4K','Ultra');

CREATE TABLE IF NOT EXISTS `chatbot_rate_limits` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       INT NOT NULL,
  `request_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `window_start`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rl_user` (`user_id`),
  CONSTRAINT `fk_rl_user2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

UPDATE `component` SET `benchmark_score`=72, `tdp_watts`=125,  `socket`='LGA1700', `brand`='Intel' WHERE `component_name`='Intel Core i5 14600K';
UPDATE `component` SET `benchmark_score`=96, `tdp_watts`=120,  `socket`='AM5',     `brand`='AMD'   WHERE `component_name`='AMD Ryzen 7 7800X3D';
UPDATE `component` SET `benchmark_score`=0,  `tdp_watts`=0,    `ram_gen`='DDR5', `form_factor`='ATX', `ram_slots`=4, `m2_slots`=2, `sata_ports`=4 WHERE `component_name`='ASUS ROG B650';
UPDATE `component` SET `benchmark_score`=0,  `tdp_watts`=0,    `ram_gen`='DDR5', `form_factor`='ATX', `ram_slots`=4, `m2_slots`=2, `sata_ports`=4 WHERE `component_name`='MSI Z790 Tomahawk';
UPDATE `component` SET `benchmark_score`=0,  `tdp_watts`=7,    `ram_gen`='DDR5', `brand`='Corsair' WHERE `component_name`='Corsair 32GB DDR5';
UPDATE `component` SET `benchmark_score`=0,  `tdp_watts`=5,    `ram_gen`='DDR4', `brand`='Kingston' WHERE `component_name`='Kingston Fury 16GB';
UPDATE `component` SET `benchmark_score`=0,  `tdp_watts`=6,    `storage_interface`='NVMe', `brand`='Samsung' WHERE `component_name`='Samsung 990 Pro 1TB';
UPDATE `component` SET `benchmark_score`=0,  `tdp_watts`=8,    `storage_interface`='SATA', `brand`='WD'      WHERE `component_name`='WD Blue 2TB HDD';
UPDATE `component` SET `benchmark_score`=94, `tdp_watts`=200,  `length_mm`=336, `brand`='NVIDIA'   WHERE `component_name`='RTX 4070 Super';
UPDATE `component` SET `benchmark_score`=96, `tdp_watts`=315,  `length_mm`=336, `brand`='AMD'      WHERE `component_name`='RX 7900 XT';
UPDATE `component` SET `benchmark_score`=99, `tdp_watts`=575,  `length_mm`=336, `brand`='NVIDIA'   WHERE `component_name`='RTX 5090';
UPDATE `component` SET `benchmark_score`=99, `tdp_watts`=0,    `psu_wattage`=1000, `brand`='Corsair' WHERE `component_name`='Corsair RM1000x';
