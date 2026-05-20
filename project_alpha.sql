-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: project_alpha
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `authentication`
--

DROP TABLE IF EXISTS `authentication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authentication` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` datetime DEFAULT NULL,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`author_id`),
  KEY `fk_user_authentication` (`user_id`),
  CONSTRAINT `fk_user_authentication` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authentication`
--

LOCK TABLES `authentication` WRITE;
/*!40000 ALTER TABLE `authentication` DISABLE KEYS */;
INSERT INTO `authentication` VALUES (101,1,'2026-05-01 02:00:00','2026-05-01 10:00:00','offline'),(102,2,'2026-05-01 03:00:00',NULL,'online'),(103,3,'2026-05-01 04:00:00','2026-05-01 12:00:00','offline'),(104,4,'2026-05-01 05:00:00',NULL,'online'),(105,5,'2026-05-01 06:00:00','2026-05-01 01:00:00','offline'),(106,6,'2026-04-30 19:00:00',NULL,'online'),(107,7,'2026-04-30 20:00:00','2026-05-01 03:00:00','offline'),(108,8,'2026-04-30 21:00:00',NULL,'online'),(109,9,'2026-04-30 22:00:00','2026-05-01 05:00:00','offline'),(110,10,'2026-04-30 23:00:00',NULL,'online');
/*!40000 ALTER TABLE `authentication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `build`
--

DROP TABLE IF EXISTS `build`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `build` (
  `build_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `fps` int(11) NOT NULL,
  `wattage` int(11) NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT 'My Build',
  `purpose` varchar(50) NOT NULL DEFAULT 'general',
  `score` decimal(6,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`build_id`),
  KEY `fk_user_id_build` (`user_id`),
  CONSTRAINT `fk_user_id_build` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `build`
--

LOCK TABLES `build` WRITE;
/*!40000 ALTER TABLE `build` DISABLE KEYS */;
INSERT INTO `build` VALUES (2,2,65000.00,90,550,'My Build','general',0.00,'2026-05-19 10:21:58'),(3,3,220000.00,240,850,'My Build','general',0.00,'2026-05-19 10:21:58'),(4,4,95000.00,120,650,'My Build','general',0.00,'2026-05-19 10:21:58'),(5,5,250000.00,300,1000,'My Build','general',0.00,'2026-05-19 10:21:58'),(6,6,120000.00,144,700,'My Build','general',0.00,'2026-05-19 10:21:58'),(7,7,175000.00,165,750,'My Build','general',0.00,'2026-05-19 10:21:58'),(8,8,280000.00,320,1050,'My Build','general',0.00,'2026-05-19 10:21:58'),(9,9,145000.00,140,650,'My Build','general',0.00,'2026-05-19 10:21:58'),(10,10,70000.00,75,500,'My Build','general',0.00,'2026-05-19 10:21:58'),(11,11,160000.00,170,750,'My Build','general',0.00,'2026-05-19 10:21:58'),(12,12,210000.00,240,850,'My Build','general',0.00,'2026-05-19 10:21:58'),(13,13,130000.00,130,650,'My Build','general',0.00,'2026-05-19 10:21:58'),(14,14,280000.00,320,1000,'My Build','general',0.00,'2026-05-19 10:21:58'),(15,15,90000.00,95,550,'My Build','general',0.00,'2026-05-19 10:21:58'),(16,16,175000.00,180,750,'My Build','general',0.00,'2026-05-19 10:21:58'),(17,17,220000.00,260,900,'My Build','general',0.00,'2026-05-19 10:21:58'),(18,18,145000.00,140,650,'My Build','general',0.00,'2026-05-19 10:21:58'),(19,19,190000.00,200,800,'My Build','general',0.00,'2026-05-19 10:21:58'),(20,20,110000.00,120,600,'My Build','general',0.00,'2026-05-19 10:21:58'),(33,34,16000.00,0,0,'My Build','video_editing',38.97,'2026-05-19 22:13:11'),(34,32,16000.00,0,0,'My Build','gaming',68.97,'2026-05-19 23:50:54'),(35,32,7000.00,0,0,'My Build','gaming',1.67,'2026-05-19 23:51:05'),(36,32,16000.00,0,0,'My Build','video_editing',38.97,'2026-05-19 23:56:52');
/*!40000 ALTER TABLE `build` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buildcomponent`
--

DROP TABLE IF EXISTS `buildcomponent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buildcomponent` (
  `build_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  PRIMARY KEY (`build_id`,`component_id`),
  KEY `fk_component_id_buildComponent` (`component_id`),
  CONSTRAINT `fk_build_id_buildComponent` FOREIGN KEY (`build_id`) REFERENCES `build` (`build_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_component_id_buildComponent` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buildcomponent`
--

LOCK TABLES `buildcomponent` WRITE;
/*!40000 ALTER TABLE `buildcomponent` DISABLE KEYS */;
INSERT INTO `buildcomponent` VALUES (2,2),(2,6),(2,8),(2,10),(33,6),(33,8),(33,12),(33,14),(33,16),(33,20),(34,6),(34,8),(34,12),(34,14),(34,16),(34,20),(35,8),(35,11),(35,13),(35,15),(35,18),(35,20),(36,6),(36,8),(36,12),(36,13),(36,15),(36,20);
/*!40000 ALTER TABLE `buildcomponent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot`
--

DROP TABLE IF EXISTS `chatbot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatbot` (
  `chat_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `query` text NOT NULL,
  `response` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`chat_id`),
  KEY `fk_user_id_chatbot` (`user_id`),
  CONSTRAINT `fk_user_id_chatbot` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot`
--

LOCK TABLES `chatbot` WRITE;
/*!40000 ALTER TABLE `chatbot` DISABLE KEYS */;
INSERT INTO `chatbot` VALUES (1,1,'Best GPU for gaming?','RTX 4070 Super is recommended.','2026-05-16 05:38:52'),(2,2,'Best budget CPU?','Intel Core i5 is a good option.','2026-05-16 05:38:52'),(3,3,'Need editing build','Ryzen 7 build suggested.','2026-05-16 05:38:52'),(4,4,'Best RAM size?','32GB DDR5 recommended.','2026-05-16 05:38:52'),(5,5,'Best PSU wattage?','750W recommended.','2026-05-16 05:38:52'),(6,6,'Need storage advice','1TB NVMe SSD suggested.','2026-05-16 05:38:52'),(7,7,'Best motherboard?','ASUS ROG B650 recommended.','2026-05-16 05:38:52'),(8,8,'Gaming monitor?','144Hz monitor recommended.','2026-05-16 05:38:52'),(9,9,'Cooling suggestion?','Liquid cooling suggested.','2026-05-16 05:38:52'),(10,10,'Streaming build?','RTX + Ryzen combo recommended.','2026-05-16 05:38:52');
/*!40000 ALTER TABLE `chatbot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_rate_limits`
--

DROP TABLE IF EXISTS `chatbot_rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatbot_rate_limits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `request_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `window_start` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rl_user` (`user_id`),
  CONSTRAINT `fk_rl_user2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_rate_limits`
--

LOCK TABLES `chatbot_rate_limits` WRITE;
/*!40000 ALTER TABLE `chatbot_rate_limits` DISABLE KEYS */;
INSERT INTO `chatbot_rate_limits` VALUES (1,1,20,'2026-05-20 12:23:22'),(2,34,3,'2026-05-20 04:09:02'),(3,32,4,'2026-05-20 05:53:53'),(4,35,4,'2026-05-20 04:30:45'),(5,39,5,'2026-05-20 12:09:01');
/*!40000 ALTER TABLE `chatbot_rate_limits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`comment_id`),
  KEY `fk_user_id_comment` (`user_id`),
  KEY `fk_post_id_comment` (`post_id`),
  CONSTRAINT `fk_post_id_comment` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_id_comment` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES (1,2,1,'RTX 4070 Super is excellent.','2026-05-16 05:40:07'),(2,3,2,'Go with Ryzen build.','2026-05-16 05:40:07'),(3,4,3,'RX cards offer better value.','2026-05-16 05:40:07'),(4,5,4,'32GB RAM is enough.','2026-05-16 05:40:07'),(5,6,5,'Samsung 990 Pro recommended.','2026-05-16 05:40:07'),(6,7,6,'Use better cooling.','2026-05-16 05:40:07'),(7,8,7,'Lian Li fans look great.','2026-05-16 05:40:07'),(8,9,8,'Desktop gives better performance.','2026-05-16 05:40:07'),(9,10,9,'Use silent PSU and fans.','2026-05-16 05:40:07'),(10,1,10,'Upgrade GPU first for gaming.','2026-05-16 05:40:07'),(11,32,21,'Ryzen 9 9950X3D','2026-05-19 23:01:49'),(12,32,23,'Test','2026-05-19 23:53:24'),(13,39,23,'Test-replied','2026-05-19 23:59:30');
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comparison`
--

DROP TABLE IF EXISTS `comparison`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comparison` (
  `comparison_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `componentA_id` int(11) NOT NULL,
  `componentB_id` int(11) NOT NULL,
  PRIMARY KEY (`comparison_id`),
  KEY `fk_user_id_comparison` (`user_id`),
  KEY `fk_componentA_comparison` (`componentA_id`),
  KEY `fk_componentB_comparison` (`componentB_id`),
  CONSTRAINT `fk_componentA_comparison` FOREIGN KEY (`componentA_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_componentB_comparison` FOREIGN KEY (`componentB_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_id_comparison` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comparison`
--

LOCK TABLES `comparison` WRITE;
/*!40000 ALTER TABLE `comparison` DISABLE KEYS */;
INSERT INTO `comparison` VALUES (1,1,1,2),(2,2,9,10),(3,3,5,6),(6,6,1,9),(7,7,2,10),(10,10,5,9);
/*!40000 ALTER TABLE `comparison` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `component`
--

DROP TABLE IF EXISTS `component`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `component` (
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `component_name` varchar(30) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL CHECK (`type` in ('CPU (processing)','Motherboard (connection)','RAM (temporary memory)','Storage (HDD/SSD)','GPU (graphics)','PSU (power)','Case (body)','Input devices','Output devices')),
  `brand` varchar(100) NOT NULL DEFAULT '',
  `benchmark_score` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tdp_watts` smallint(5) unsigned NOT NULL DEFAULT 0,
  `socket` varchar(30) NOT NULL DEFAULT '',
  `ram_gen` varchar(10) NOT NULL DEFAULT '',
  `form_factor` varchar(10) NOT NULL DEFAULT '',
  `length_mm` smallint(5) unsigned NOT NULL DEFAULT 0,
  `height_mm` smallint(5) unsigned NOT NULL DEFAULT 0,
  `m2_slots` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `sata_ports` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ram_slots` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `psu_wattage` smallint(5) unsigned NOT NULL DEFAULT 0,
  `storage_interface` varchar(10) NOT NULL DEFAULT '',
  `image_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `component`
--

LOCK TABLES `component` WRITE;
/*!40000 ALTER TABLE `component` DISABLE KEYS */;
INSERT INTO `component` VALUES (1,'Intel Core i5 14600K','CPU (processing)','Intel',72.00,125,'LGA1700','','',0,0,0,0,0,0,'','uploads/components/comp_6a0cef2c7f791.webp'),(2,'AMD Ryzen 7 7800X3D','CPU (processing)','AMD',96.00,120,'AM5','','',0,0,0,0,0,0,'','uploads/components/comp_6a0cee4134643.webp'),(3,'ASUS ROG B650','Motherboard (connection)','',0.00,0,'','DDR5','ATX',0,0,2,4,4,0,'','uploads/components/comp_6a0cf50737c10.webp'),(5,'Corsair 32GB DDR5','RAM (temporary memory)','Corsair',0.00,7,'','DDR5','',0,0,0,0,0,0,'','uploads/components/comp_6a0cf4bfe77af.webp'),(6,'Kingston Fury 16GB','RAM (temporary memory)','Kingston',0.00,5,'','DDR4','',0,0,0,0,0,0,'','uploads/components/comp_6a0cf480e441c.jpg'),(8,'WD Blue 2TB HDD','Storage (HDD/SSD)','WD',0.00,8,'','','',0,0,0,0,0,0,'SATA','uploads/components/comp_6a0cf46c7eaa0.webp'),(9,'RTX 4070 Super','GPU (graphics)','NVIDIA',94.00,200,'','','',336,0,0,0,0,0,'','uploads/components/comp_6a0cf2ef39065.webp'),(10,'RX 7900 XT','GPU (graphics)','AMD',96.00,315,'','','',336,0,0,0,0,0,'','uploads/components/comp_6a0cf5212347f.jpg'),(11,'RTX 5080','GPU (graphics)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/comp_6a0cf5396c1f9.webp'),(12,'RTX 5090','GPU (graphics)','NVIDIA',99.00,575,'','','',336,0,0,0,0,0,'','uploads/components/comp_6a0cf52d74742.webp'),(13,'Ryzen 9 9950X','CPU (processing)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/comp_6a0cf2d1ab8b0.webp'),(14,'Intel Core Ultra 9','CPU (processing)','Intel',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/comp_6a0cef1a8bbe5.webp'),(15,'Gigabyte X870','Motherboard (connection)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/comp_6a0cf4f4347ce.webp'),(16,'ASRock B760','Motherboard (connection)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/comp_6a0cf5121f438.webp'),(17,'G.Skill Trident Z 64GB','RAM (temporary memory)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/comp_6a0cf49824baf.webp'),(18,'Crucial DDR5 32GB','RAM (temporary memory)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/comp_6a0cf4aec2e9c.webp'),(20,'Corsair RM1000x','PSU (power)','Corsair',99.00,0,'','','',0,0,0,0,0,1000,'','uploads/components/comp_6a0cf4d0d6fbf.jpg'),(22,'Test','CPU (processing)','',0.00,0,'','','',0,0,0,0,0,0,'',NULL);
/*!40000 ALTER TABLE `component` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fps_profiles`
--

DROP TABLE IF EXISTS `fps_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fps_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game_slug` varchar(80) NOT NULL,
  `game_name` varchar(160) NOT NULL,
  `difficulty_factor` decimal(6,3) NOT NULL DEFAULT 1.000,
  `resolution` varchar(20) NOT NULL DEFAULT '1080p',
  `quality` varchar(20) NOT NULL DEFAULT 'Medium',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`game_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fps_profiles`
--

LOCK TABLES `fps_profiles` WRITE;
/*!40000 ALTER TABLE `fps_profiles` DISABLE KEYS */;
INSERT INTO `fps_profiles` VALUES (1,'valorant','Valorant',0.300,'1080p','High'),(2,'csgo2','Counter-Strike 2',0.400,'1080p','High'),(3,'pubg','PUBG: Battlegrounds',1.200,'1080p','Medium'),(4,'cyberpunk2077','Cyberpunk 2077',2.000,'1080p','High'),(5,'fortnite','Fortnite',0.700,'1080p','High'),(6,'gta5','GTA V',0.800,'1080p','High'),(7,'elden-ring','Elden Ring',1.400,'1080p','High'),(8,'apex-legends','Apex Legends',0.900,'1080p','High'),(9,'davinci-resolve','DaVinci Resolve',1.800,'4K','Ultra');
/*!40000 ALTER TABLE `fps_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pcnews`
--

DROP TABLE IF EXISTS `pcnews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pcnews` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pcnews`
--

LOCK TABLES `pcnews` WRITE;
/*!40000 ALTER TABLE `pcnews` DISABLE KEYS */;
INSERT INTO `pcnews` VALUES (1,'NVIDIA Launches RTX 5090','New flagship GPU released.','2026-05-16 05:40:07','2026-05-16 05:40:07'),(2,'AMD Ryzen 9000 Series','AMD announces next gen CPUs.','2026-05-16 05:40:07','2026-05-16 05:40:07'),(3,'DDR6 RAM Coming Soon','Faster memory technology incoming.','2026-05-16 05:40:07','2026-05-16 05:40:07'),(4,'Intel New Architecture','Intel reveals future plans.','2026-05-16 05:40:07','2026-05-16 05:40:07'),(5,'Best Gaming Builds 2026','Top builds for gamers.','2026-05-16 05:40:07','2026-05-16 05:40:07'),(6,'AI PCs Trending','AI optimized PCs growing fast.','2026-05-16 05:40:07','2026-05-16 05:40:07'),(7,'Cheaper SSD Prices','SSD market prices dropping.','2026-05-16 05:40:07','2026-05-16 05:40:07'),(8,'New Liquid Coolers','Advanced cooling systems launched.','2026-05-16 05:40:07','2026-05-16 05:40:07'),(9,'Gaming Monitor Trends','OLED gaming monitors becoming popular.','2026-05-16 05:40:07','2026-05-16 05:40:07'),(10,'Windows Optimization Tips','Performance tuning guide.','2026-05-16 05:40:07','2026-05-16 05:40:07');
/*!40000 ALTER TABLE `pcnews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`post_id`),
  KEY `fk_user_id_post` (`user_id`),
  CONSTRAINT `fk_user_id_post` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
INSERT INTO `post` VALUES (1,1,'Best Gaming GPU','Which GPU is best for 1440p?','2026-05-16 05:40:07'),(2,2,'Budget PC Build','Need build under 60k.','2026-05-16 05:40:07'),(3,3,'RTX vs RX','Which one should I buy?','2026-05-16 05:40:07'),(4,4,'Need Editing Setup','Best PC for Adobe Premiere?','2026-05-16 05:40:07'),(5,5,'SSD Recommendation','Suggest fast SSD.','2026-05-16 05:40:07'),(6,6,'CPU Temperature Issue','CPU getting too hot.','2026-05-16 05:40:07'),(7,7,'Best RGB Fans','Need aesthetic fans.','2026-05-16 05:40:07'),(8,8,'Laptop vs Desktop','Which is better for gaming?','2026-05-16 05:40:07'),(9,9,'Need Silent Build','Suggest low-noise setup.','2026-05-16 05:40:07'),(10,10,'Upgrade Advice','Should I upgrade GPU first?','2026-05-16 05:40:07'),(11,11,'Need RTX Build','Suggest RTX 5080 build.','2026-05-16 05:40:07'),(12,12,'Best CPU Cooler','Which cooler is best?','2026-05-16 05:40:07'),(13,13,'Need Budget GPU','GPU under 30k?','2026-05-16 05:40:07'),(14,14,'4K Gaming Setup','Need high-end gaming PC.','2026-05-16 05:40:07'),(15,15,'Best SSD Brand','Samsung or WD?','2026-05-16 05:40:07'),(16,16,'RGB Build Ideas','Need aesthetic setup.','2026-05-16 05:40:07'),(17,17,'High FPS Build','Need 240 FPS build.','2026-05-16 05:40:07'),(18,18,'PC Upgrade Help','Should I upgrade RAM?','2026-05-16 05:40:07'),(19,19,'Best PSU','Need reliable PSU.','2026-05-16 05:40:07'),(20,20,'Streaming PC Advice','Need dual PC setup.','2026-05-16 05:40:07'),(21,1,'What is the best CPU for gaming in 2026?','I am looking to build a new PC and I am torn between the Intel Core i9-14900K and the AMD Ryzen 9 9950X. What are your thoughts on value for money in Bangladesh?','2026-05-19 22:58:59'),(22,32,'Research-Based PC Build Discussion','I am planning to build a high-performance research workstation for:\r\n\r\nAI & Machine Learning\r\nDeep Learning & Diffusion Models\r\nfMRI / EEG / Neuroscience Research\r\nComputer Vision & YOLOv8\r\n3D CNN Training\r\nLarge Dataset Processing\r\nOCR & AI Applications\r\nPyQt6 Development\r\nCUDA & GPU-intensive workloads\r\n\r\nI would highly appreciate opinions from researchers, AI engineers, data scientists, and PC experts regarding the best configuration in 2026.\r\n\r\nCurrent focus:\r\n\r\nHigh VRAM GPU\r\nPowerful multi-core CPU\r\nFast NVMe SSD\r\nFuture-proof motherboard\r\nStable cooling system\r\nLinux + Windows compatibility\r\n\r\nQuestions:\r\n\r\nAMD or Intel for AI research workloads?\r\nBest GPU for deep learning within a reasonable budget?\r\nDDR5 RAM recommendation for large-scale datasets?\r\nIs Threadripper worth it for research purposes?\r\nBest motherboard + PSU combination for long-term stability?\r\nAny bottleneck issues I should avoid?\r\n\r\nPlease share your recommended configurations, experiences, benchmarks, or suggestions. Your expert opinions would be valuable for my research journey.','2026-05-19 23:04:43'),(23,32,'Test01','Hi \r\nThis is Seaman','2026-05-19 23:53:13');
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posttag`
--

DROP TABLE IF EXISTS `posttag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posttag` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`post_id`,`tag_id`),
  KEY `fk_tag_id_postTag` (`tag_id`),
  CONSTRAINT `fk_post_id_postTag` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tag_id_postTag` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posttag`
--

LOCK TABLES `posttag` WRITE;
/*!40000 ALTER TABLE `posttag` DISABLE KEYS */;
INSERT INTO `posttag` VALUES (1,1,'2026-05-16 05:40:07'),(2,2,'2026-05-16 05:40:07'),(3,3,'2026-05-16 05:40:07'),(4,4,'2026-05-16 05:40:07'),(5,7,'2026-05-16 05:40:07'),(6,8,'2026-05-16 05:40:07'),(7,9,'2026-05-16 05:40:07'),(8,1,'2026-05-16 05:40:07'),(9,8,'2026-05-16 05:40:07'),(10,10,'2026-05-16 05:40:07');
/*!40000 ALTER TABLE `posttag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pricetracking`
--

DROP TABLE IF EXISTS `pricetracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pricetracking` (
  `track_id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` int(11) NOT NULL,
  `old_price` decimal(10,2) NOT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`track_id`),
  KEY `fk_component_id_priceTracking` (`component_id`),
  CONSTRAINT `fk_component_id_priceTracking` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pricetracking`
--

LOCK TABLES `pricetracking` WRITE;
/*!40000 ALTER TABLE `pricetracking` DISABLE KEYS */;
INSERT INTO `pricetracking` VALUES (1,1,37000.00,35000.00,'2026-05-16 05:38:20'),(2,2,50000.00,48000.00,'2026-05-16 05:38:20'),(3,3,27000.00,25000.00,'2026-05-16 05:38:20'),(5,5,17000.00,15000.00,'2026-05-16 05:38:20'),(6,6,10000.00,9000.00,'2026-05-16 05:38:20'),(8,8,8000.00,7000.00,'2026-05-16 05:38:20'),(9,9,90000.00,85000.00,'2026-05-16 05:38:20'),(10,10,100000.00,95000.00,'2026-05-16 05:38:20');
/*!40000 ALTER TABLE `pricetracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store`
--

DROP TABLE IF EXISTS `store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_name` varchar(100) NOT NULL,
  `store_location` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`store_id`),
  UNIQUE KEY `store_name` (`store_name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store`
--

LOCK TABLES `store` WRITE;
/*!40000 ALTER TABLE `store` DISABLE KEYS */;
INSERT INTO `store` VALUES (1,'Star Tech','Dhaka','2026-05-16 05:37:54'),(2,'Ryans Computers','Dhaka','2026-05-16 05:37:54'),(3,'TechLand','Dhaka','2026-05-16 05:37:54'),(4,'Binary Logic','Chattogram','2026-05-16 05:37:54'),(5,'PC House','Khulna','2026-05-16 05:37:54'),(6,'Ultra Tech','Sylhet','2026-05-16 05:37:54'),(7,'Game Hub','Rajshahi','2026-05-16 05:37:54'),(8,'Build Zone','Dhaka','2026-05-16 05:37:54'),(9,'Tech Valley','Barisal','2026-05-16 05:37:54'),(10,'Computer Mania','Cumilla','2026-05-16 05:37:54');
/*!40000 ALTER TABLE `store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `storeavailability`
--

DROP TABLE IF EXISTS `storeavailability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `storeavailability` (
  `availability_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  `stock_status` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`availability_id`),
  KEY `fk_store_id_storeAvailability` (`store_id`),
  KEY `fk_component_id_storeAvailability` (`component_id`),
  CONSTRAINT `fk_component_id_storeAvailability` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_store_id_storeAvailability` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `storeavailability`
--

LOCK TABLES `storeavailability` WRITE;
/*!40000 ALTER TABLE `storeavailability` DISABLE KEYS */;
INSERT INTO `storeavailability` VALUES (1,1,1,'In Stock',35000.00),(2,2,2,'Limited',48000.00),(3,3,3,'In Stock',25000.00),(5,5,5,'In Stock',15000.00),(6,6,6,'Limited',9000.00),(8,8,8,'In Stock',7000.00),(9,9,9,'Limited',85000.00),(10,10,10,'In Stock',95000.00);
/*!40000 ALTER TABLE `storeavailability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` VALUES (2,'Budget'),(8,'Cooling'),(4,'CPU'),(1,'Gaming'),(3,'GPU'),(5,'Motherboard'),(6,'RAM'),(9,'RGB'),(7,'Storage'),(10,'Upgrade');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upgradesuggestion`
--

DROP TABLE IF EXISTS `upgradesuggestion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upgradesuggestion` (
  `upgrade_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `build_id` int(11) DEFAULT NULL,
  `component_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`upgrade_id`),
  KEY `fk_user_id_upgradeSuggestion` (`user_id`),
  KEY `fk_build_id_upgradeSuggestion` (`build_id`),
  KEY `fk_component_id_upgradeSuggestion` (`component_id`),
  CONSTRAINT `fk_build_id_upgradeSuggestion` FOREIGN KEY (`build_id`) REFERENCES `build` (`build_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_component_id_upgradeSuggestion` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_id_upgradeSuggestion` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upgradesuggestion`
--

LOCK TABLES `upgradesuggestion` WRITE;
/*!40000 ALTER TABLE `upgradesuggestion` DISABLE KEYS */;
INSERT INTO `upgradesuggestion` VALUES (2,2,2,9,'2026-05-16 05:38:52'),(3,3,3,10,'2026-05-16 05:38:52'),(4,4,4,5,'2026-05-16 05:38:52'),(5,5,5,1,'2026-05-16 05:38:52'),(7,7,7,8,'2026-05-16 05:38:52'),(9,9,9,3,'2026-05-16 05:38:52'),(10,10,10,6,'2026-05-16 05:38:52'),(12,32,NULL,10,'2026-05-20 06:49:17'),(13,32,NULL,2,'2026-05-20 06:49:30');
/*!40000 ALTER TABLE `upgradesuggestion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Shadman Ahammad','shadman1@pcbuild.com','$2y$12$CUV6.EXXxFP4ZDvMJ2ZPn.0gvj.5pTusfAP0aze2dSSRO02ulezKm','admin','2026-05-19 10:21:58'),(2,'Rahim Uddin','rahim2@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(3,'Karim Hasan','karim3@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(4,'Nusrat Jahan','nusrat4@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(5,'Tanvir Ahmed','tanvir5@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(6,'Faria Islam','faria6@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(7,'Sabbir Hossain','sabbir7@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(8,'Mehedi Hasan','mehedi8@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(9,'Tanjila Akter','tanjila9@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(10,'Arifur Rahman','arif10@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(11,'Arian Khan','arian11@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(12,'Sakib Ahmed','sakib12@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(13,'Nabil Hossain','nabil13@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(14,'Jubayer Islam','jubayer14@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(15,'Towsif Rahman','towsif15@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(16,'Fahim Hasan','fahim16@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(17,'Imran Chowdhury','imran17@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(18,'Rifat Karim','rifat18@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(19,'Nahid Hasan','nahid19@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(20,'Mahin Ahmed','mahin20@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(21,'Sarah Johnson','sarah21@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(22,'Michael Lee','michael22@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(23,'Emily Clark','emily23@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(24,'Daniel Smith','daniel24@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(25,'Sophia Brown','sophia25@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(26,'Ethan Walker','ethan26@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(27,'Olivia White','olivia27@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(28,'Noah Harris','noah28@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(29,'Mason Scott','mason29@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(30,'Ava Green','ava30@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(31,'Test User','testuser+reg@example.com','$2y$12$JOiReydyMndOn2bJPS9PR.IHGvMJO0RsIUCZbyiIK/EjTyrsl6xLW','user','2026-05-19 15:51:09'),(32,'Seaman','smseaman7@gmail.com','$2y$12$HVkaG2Yfs15G7IVGlZ0VhOm5HLML.oCip2Q9nd1p.YnjjbKR14.h6','admin','2026-05-19 16:00:50'),(33,'Dr','d@gmail.com','$2y$12$qzCkbXdWQUuVo4Gx3kOojuvL8H.WNSt0ejamyaPglQ99kqmRQRpDi','user','2026-05-19 16:07:10'),(34,'Mr Dr','dr@gmail.com','$2y$12$.1McqjVkaN7dLn2gMj4Kou0VLSUK6uyqsJx3bYtSDEn5zSIA8JVTO','user','2026-05-19 16:08:09'),(35,'Dr M','drm@gmail.com','$2y$12$RqK1qF6jvRTyYP.CKLVDpudBeNDZkLtG6/PDccYV4OAg8ezTcsM3K','user','2026-05-19 16:19:21'),(36,'TestUser1779227871','testuser1779227871@example.com','$2y$12$IX9rJybylq3OpCh4MPgcCObpyCpH9zFXf8097K.7eMpTCZjy6Eq8W','user','2026-05-19 21:57:52'),(37,'Test User','testuser_2e24210c@pcbuild.com','$2y$12$LgHLbfRuko6eIRo23JdQK.HSflQsj/VCZy30svYwzNmzWBvvQqCtW','user','2026-05-19 22:02:11'),(38,'Test User','testuser_1779228374@pcbuild.com','$2y$12$xP5KqDBHewF/.pcDSI9t7ORsozXjffDVp.UgfLqv.p4bME0DEKJpW','user','2026-05-19 22:06:15'),(39,'Test1','test@gmail.com','$2y$12$/GDm1cROGZc24wi43CyCU.yOSrBk4FDnD5lQf9T79T4j4Wdp0guTK','user','2026-05-19 23:57:41');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_preferences`
--

DROP TABLE IF EXISTS `user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_preferences` (
  `preference_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `preference_name` varchar(100) NOT NULL,
  PRIMARY KEY (`preference_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_preferences`
--

LOCK TABLES `user_preferences` WRITE;
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
INSERT INTO `user_preferences` VALUES (1,1,'Gaming PC'),(2,1,'RGB Setup'),(3,1,'Streaming Setup'),(4,1,'Dual Monitor'),(5,2,'Budget Build'),(6,2,'Office PC'),(7,2,'Energy Efficient'),(8,2,'Compact Case'),(9,3,'RGB Setup'),(10,3,'Water Cooling'),(11,3,'Gaming Chair'),(12,3,'Mechanical Keyboard'),(13,4,'Streaming PC'),(14,4,'Content Creation'),(15,4,'Dual GPU'),(16,4,'4K Editing'),(17,5,'High-End Gaming'),(18,5,'RTX Build'),(19,5,'Ultra Wide Monitor'),(20,5,'VR Gaming'),(21,6,'White Theme Build'),(22,6,'Silent PC'),(23,6,'RGB Fans'),(24,6,'Minimal Setup'),(25,7,'Intel Build'),(26,7,'Productivity'),(27,7,'Workstation PC'),(28,7,'Coding Setup'),(29,8,'AMD Ryzen Build'),(30,8,'Gaming PC'),(31,8,'Streaming PC'),(32,8,'Overclocking'),(33,9,'Mini ITX Setup'),(34,9,'Portable Build'),(35,9,'Low Power Usage'),(36,9,'Minimal Desk Setup'),(37,10,'Workstation PC'),(38,10,'Video Editing'),(39,10,'3D Rendering'),(40,10,'Multi Monitor'),(41,1,'Esports Gaming'),(42,2,'Student Build'),(43,3,'Custom RGB'),(44,4,'Podcast Setup'),(45,5,'Liquid Cooling'),(46,6,'Aesthetic Build'),(47,7,'Software Development'),(48,8,'Benchmark Testing'),(49,9,'LAN Party Build'),(50,10,'Professional Editing');
/*!40000 ALTER TABLE `user_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_project`
--

DROP TABLE IF EXISTS `user_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `purpose_type` varchar(100) NOT NULL,
  `budget_amount` decimal(10,2) NOT NULL,
  `component_id` int(11) NOT NULL,
  PRIMARY KEY (`project_id`),
  KEY `fk_user_project_user` (`user_id`),
  KEY `fk_user_project_component` (`component_id`),
  CONSTRAINT `fk_user_project_component` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_project_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_project`
--

LOCK TABLES `user_project` WRITE;
/*!40000 ALTER TABLE `user_project` DISABLE KEYS */;
INSERT INTO `user_project` VALUES (55,1,'Gaming PC',150000.00,1),(56,1,'Gaming PC',150000.00,5),(57,1,'Gaming PC',150000.00,9),(58,2,'Budget Gaming',80000.00,2),(59,2,'Budget Gaming',80000.00,6),(60,2,'Budget Gaming',80000.00,8),(61,3,'Streaming PC',120000.00,2),(62,3,'Streaming PC',120000.00,5),(63,3,'Streaming PC',120000.00,9),(64,4,'Video Editing',200000.00,2),(65,4,'Video Editing',200000.00,5),(67,5,'3D Rendering',250000.00,2),(68,5,'3D Rendering',250000.00,5),(69,5,'3D Rendering',250000.00,10);
/*!40000 ALTER TABLE `user_project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vote`
--

DROP TABLE IF EXISTS `vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `vote_type` enum('upvote','downvote') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `user_id` (`user_id`,`post_id`,`comment_id`),
  KEY `fk_post_id_vote` (`post_id`),
  KEY `fk_comment_id_vote` (`comment_id`),
  CONSTRAINT `fk_comment_id_vote` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_post_id_vote` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_id_vote` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vote`
--

LOCK TABLES `vote` WRITE;
/*!40000 ALTER TABLE `vote` DISABLE KEYS */;
INSERT INTO `vote` VALUES (1,1,1,NULL,'upvote','2026-05-16 05:40:07'),(2,2,2,NULL,'upvote','2026-05-16 05:40:07'),(3,3,3,NULL,'downvote','2026-05-16 05:40:07'),(4,4,4,NULL,'upvote','2026-05-16 05:40:07'),(5,5,5,NULL,'upvote','2026-05-16 05:40:07'),(6,6,NULL,1,'upvote','2026-05-16 05:40:07'),(7,7,NULL,2,'upvote','2026-05-16 05:40:07'),(8,8,NULL,3,'downvote','2026-05-16 05:40:07'),(9,9,NULL,4,'upvote','2026-05-16 05:40:07'),(10,10,NULL,5,'upvote','2026-05-16 05:40:07'),(14,39,23,NULL,'upvote','2026-05-19 23:59:23');
/*!40000 ALTER TABLE `vote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `watchlist`
--

DROP TABLE IF EXISTS `watchlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `watchlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  `added_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_watch` (`user_id`,`component_id`),
  KEY `fk_wl_comp` (`component_id`),
  CONSTRAINT `fk_wl_comp` FOREIGN KEY (`component_id`) REFERENCES `component` (`component_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wl_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `watchlist`
--

LOCK TABLES `watchlist` WRITE;
/*!40000 ALTER TABLE `watchlist` DISABLE KEYS */;
INSERT INTO `watchlist` VALUES (1,1,20,'2026-05-20 04:05:55');
/*!40000 ALTER TABLE `watchlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-20 12:51:12
