-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: smart_pc_build
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `build`
--

LOCK TABLES `build` WRITE;
/*!40000 ALTER TABLE `build` DISABLE KEYS */;
INSERT INTO `build` VALUES (2,2,65000.00,90,550,'My Build','general',0.00,'2026-05-19 10:21:58'),(3,3,220000.00,240,850,'My Build','general',0.00,'2026-05-19 10:21:58'),(4,4,95000.00,120,650,'My Build','general',0.00,'2026-05-19 10:21:58'),(5,5,250000.00,300,1000,'My Build','general',0.00,'2026-05-19 10:21:58'),(6,6,120000.00,144,700,'My Build','general',0.00,'2026-05-19 10:21:58'),(7,7,175000.00,165,750,'My Build','general',0.00,'2026-05-19 10:21:58'),(8,8,280000.00,320,1050,'My Build','general',0.00,'2026-05-19 10:21:58'),(9,9,145000.00,140,650,'My Build','general',0.00,'2026-05-19 10:21:58'),(10,10,70000.00,75,500,'My Build','general',0.00,'2026-05-19 10:21:58'),(11,11,160000.00,170,750,'My Build','general',0.00,'2026-05-19 10:21:58'),(12,12,210000.00,240,850,'My Build','general',0.00,'2026-05-19 10:21:58'),(13,13,130000.00,130,650,'My Build','general',0.00,'2026-05-19 10:21:58'),(14,14,280000.00,320,1000,'My Build','general',0.00,'2026-05-19 10:21:58'),(15,15,90000.00,95,550,'My Build','general',0.00,'2026-05-19 10:21:58'),(16,16,175000.00,180,750,'My Build','general',0.00,'2026-05-19 10:21:58'),(17,17,220000.00,260,900,'My Build','general',0.00,'2026-05-19 10:21:58'),(18,18,145000.00,140,650,'My Build','general',0.00,'2026-05-19 10:21:58'),(19,19,190000.00,200,800,'My Build','general',0.00,'2026-05-19 10:21:58'),(20,20,110000.00,120,600,'My Build','general',0.00,'2026-05-19 10:21:58'),(33,34,16000.00,0,0,'My Build','video_editing',38.97,'2026-05-19 22:13:11'),(34,32,16000.00,0,0,'My Build','gaming',68.97,'2026-05-19 23:50:54'),(35,32,7000.00,0,0,'My Build','gaming',1.67,'2026-05-19 23:51:05'),(36,32,16000.00,0,0,'My Build','video_editing',38.97,'2026-05-19 23:56:52'),(37,1,324500.00,0,297,'Custom Build (6/11/2026)','custom',0.00,'2026-06-11 05:46:14');
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
INSERT INTO `buildcomponent` VALUES (2,2),(2,6),(2,8),(2,10),(33,6),(33,8),(33,12),(33,14),(33,16),(33,20),(34,6),(34,8),(34,12),(34,14),(34,16),(34,20),(35,8),(35,11),(35,13),(35,15),(35,18),(35,20),(36,6),(36,8),(36,12),(36,13),(36,15),(36,20),(37,5),(37,30),(37,48),(37,54),(37,82),(37,92),(37,102),(37,112),(37,116),(37,142);
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
INSERT INTO `comment` VALUES (3,4,3,'RX cards offer better value.','2026-05-16 05:40:07'),(4,5,4,'32GB RAM is enough.','2026-05-16 05:40:07'),(5,6,5,'Samsung 990 Pro recommended.','2026-05-16 05:40:07'),(6,7,6,'Use better cooling.','2026-05-16 05:40:07'),(7,8,7,'Lian Li fans look great.','2026-05-16 05:40:07'),(8,9,8,'Desktop gives better performance.','2026-05-16 05:40:07'),(9,10,9,'Use silent PSU and fans.','2026-05-16 05:40:07'),(10,1,10,'Upgrade GPU first for gaming.','2026-05-16 05:40:07');
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community`
--

DROP TABLE IF EXISTS `community`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community` (
  `community_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`community_id`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_comm_creator` (`created_by`),
  CONSTRAINT `fk_comm_creator` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community`
--

LOCK TABLES `community` WRITE;
/*!40000 ALTER TABLE `community` DISABLE KEYS */;
INSERT INTO `community` VALUES (1,'PCBuilding','Share your PC builds, look for part advice, and show off your setup!',1,'2026-05-29 16:25:30'),(2,'Gaming','Discuss the latest PC games, optimization guides, and gameplay clips.',1,'2026-05-29 16:25:30'),(3,'Overclocking','Push your hardware to the absolute limit. Air, water, or LN2 cooling!',1,'2026-05-29 16:25:31'),(4,'TechSupport','Troubleshoot software, hardware, and performance issues with the community.',1,'2026-05-29 16:25:31'),(5,'WaterCooling','Showcase custom loops, hardline piping, and liquid cooling setups.',1,'2026-05-29 16:25:31'),(6,'GamingComp2026','This Community is for gamers',40,'2026-05-31 16:31:30');
/*!40000 ALTER TABLE `community` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_member`
--

DROP TABLE IF EXISTS `community_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_member` (
  `community_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`community_id`,`user_id`),
  KEY `fk_cm_user` (`user_id`),
  CONSTRAINT `fk_cm_community` FOREIGN KEY (`community_id`) REFERENCES `community` (`community_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cm_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_member`
--

LOCK TABLES `community_member` WRITE;
/*!40000 ALTER TABLE `community_member` DISABLE KEYS */;
INSERT INTO `community_member` VALUES (1,2,'2026-05-29 16:25:31'),(1,3,'2026-05-29 16:25:31'),(1,9,'2026-05-29 16:25:33'),(1,10,'2026-05-29 16:25:34'),(1,15,'2026-05-29 16:25:34'),(1,21,'2026-05-29 16:25:36'),(1,23,'2026-05-29 16:25:37'),(1,28,'2026-05-29 16:25:39'),(1,29,'2026-05-29 16:25:40'),(1,30,'2026-05-29 16:25:40'),(1,33,'2026-05-29 16:25:40'),(1,34,'2026-05-29 16:25:41'),(1,35,'2026-05-29 16:25:41'),(1,37,'2026-05-29 16:25:41'),(1,38,'2026-05-29 16:25:41'),(1,39,'2026-05-29 16:25:41'),(2,1,'2026-05-29 16:25:31'),(2,3,'2026-05-29 16:25:31'),(2,4,'2026-05-29 16:25:31'),(2,5,'2026-05-29 16:25:31'),(2,6,'2026-05-29 16:25:33'),(2,8,'2026-05-29 16:25:33'),(2,13,'2026-05-29 16:25:34'),(2,16,'2026-05-29 16:25:34'),(2,18,'2026-05-29 16:25:35'),(2,20,'2026-05-29 16:25:35'),(2,21,'2026-05-29 16:25:36'),(2,22,'2026-05-29 16:25:36'),(2,23,'2026-05-29 16:25:37'),(2,24,'2026-05-29 16:25:37'),(2,26,'2026-05-29 16:25:39'),(2,31,'2026-05-29 16:25:40'),(2,33,'2026-05-29 16:25:40'),(2,34,'2026-05-29 16:25:41'),(3,1,'2026-05-29 16:25:31'),(3,4,'2026-05-29 16:25:31'),(3,8,'2026-05-29 16:25:33'),(3,9,'2026-05-29 16:25:34'),(3,10,'2026-05-29 16:25:34'),(3,13,'2026-05-29 16:25:34'),(3,16,'2026-05-29 16:25:35'),(3,17,'2026-05-29 16:25:35'),(3,18,'2026-05-29 16:25:35'),(3,20,'2026-05-29 16:25:36'),(3,21,'2026-05-29 16:25:36'),(3,24,'2026-05-29 16:25:37'),(3,25,'2026-05-29 16:25:38'),(3,26,'2026-05-29 16:25:39'),(3,27,'2026-05-29 16:25:39'),(3,28,'2026-05-29 16:25:39'),(3,29,'2026-05-29 16:25:40'),(3,30,'2026-05-29 16:25:40'),(3,31,'2026-05-29 16:25:40'),(3,33,'2026-05-29 16:25:40'),(3,35,'2026-05-29 16:25:41'),(3,37,'2026-05-29 16:25:41'),(4,3,'2026-05-29 16:25:31'),(4,5,'2026-05-29 16:25:32'),(4,11,'2026-05-29 16:25:34'),(4,13,'2026-05-29 16:25:34'),(4,14,'2026-05-29 16:25:34'),(4,15,'2026-05-29 16:25:34'),(4,16,'2026-05-29 16:25:35'),(4,19,'2026-05-29 16:25:35'),(4,20,'2026-05-29 16:25:36'),(4,22,'2026-05-29 16:25:37'),(4,24,'2026-05-29 16:25:37'),(4,25,'2026-05-29 16:25:39'),(4,26,'2026-05-29 16:25:39'),(4,27,'2026-05-29 16:25:39'),(4,28,'2026-05-29 16:25:40'),(4,29,'2026-05-29 16:25:40'),(4,30,'2026-05-29 16:25:40'),(4,31,'2026-05-29 16:25:40'),(4,33,'2026-05-29 16:25:40'),(4,35,'2026-05-29 16:25:41'),(4,38,'2026-05-29 16:25:41'),(5,3,'2026-05-29 16:25:31'),(5,4,'2026-05-29 16:25:31'),(5,5,'2026-05-29 16:25:32'),(5,8,'2026-05-29 16:25:33'),(5,9,'2026-05-29 16:25:34'),(5,11,'2026-05-29 16:25:34'),(5,12,'2026-05-29 16:25:34'),(5,13,'2026-05-29 16:25:34'),(5,16,'2026-05-29 16:25:35'),(5,18,'2026-05-29 16:25:35'),(5,21,'2026-05-29 16:25:36'),(5,23,'2026-05-29 16:25:37'),(5,25,'2026-05-29 16:25:39'),(5,27,'2026-05-29 16:25:39'),(5,29,'2026-05-29 16:25:40'),(5,35,'2026-05-29 16:25:41'),(5,38,'2026-05-29 16:25:41'),(5,39,'2026-05-29 16:25:41'),(6,40,'2026-05-31 16:34:23');
/*!40000 ALTER TABLE `community_member` ENABLE KEYS */;
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
  `component_name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
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
  `startech_url` varchar(255) DEFAULT NULL,
  `ryans_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`component_id`),
  UNIQUE KEY `uq_component_name_type` (`component_name`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `component`
--

LOCK TABLES `component` WRITE;
/*!40000 ALTER TABLE `component` DISABLE KEYS */;
INSERT INTO `component` VALUES (1,'Intel Core i5 14600K','CPU (processing)','Intel',72.00,125,'LGA1700','','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(2,'AMD Ryzen 7 7800X3D','CPU (processing)','AMD',96.00,120,'AM5','','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(3,'ASUS ROG B650','Motherboard (connection)','',0.00,0,'','DDR5','ATX',0,0,2,4,4,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(5,'Corsair 32GB DDR5','RAM (temporary memory)','Corsair',0.00,7,'','DDR5','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(6,'Kingston Fury 16GB','RAM (temporary memory)','Kingston',0.00,5,'','DDR4','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(8,'WD Blue 2TB HDD','Storage (HDD/SSD)','WD',0.00,8,'','','',0,0,0,0,0,0,'SATA','uploads/components/real_storage.jpg',NULL,NULL),(9,'RTX 4070 Super','GPU (graphics)','NVIDIA',94.00,200,'','','',336,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(10,'RX 7900 XT','GPU (graphics)','AMD',96.00,315,'','','',336,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(11,'RTX 5080','GPU (graphics)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(12,'RTX 5090','GPU (graphics)','NVIDIA',99.00,575,'','','',336,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(13,'Ryzen 9 9950X','CPU (processing)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(14,'Intel Core Ultra 9','CPU (processing)','Intel',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(15,'Gigabyte X870','Motherboard (connection)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(16,'ASRock B760','Motherboard (connection)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(17,'G.Skill Trident Z 64GB','RAM (temporary memory)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(18,'Crucial DDR5 32GB','RAM (temporary memory)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(20,'Corsair RM1000x','PSU (power)','Corsair',99.00,0,'','','',0,0,0,0,0,1000,'','uploads/components/real_psu.jpg',NULL,NULL),(22,'Test','CPU (processing)','',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(23,'Intel Core i9 14900K','CPU','Intel',100.00,125,'LGA1700','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(24,'Intel Core i7 14700K','CPU','Intel',95.00,125,'LGA1700','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(25,'Intel Core i5 14600K','CPU','Intel',88.00,125,'LGA1700','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(26,'Intel Core i5 13400F','CPU','Intel',75.00,65,'LGA1700','DDR4','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(27,'Intel Core i3 13100F','CPU','Intel',60.00,58,'LGA1700','DDR4','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(28,'Intel Core i9 13900K','CPU','Intel',98.00,125,'LGA1700','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(29,'Intel Core i7 13700K','CPU','Intel',92.00,125,'LGA1700','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(30,'Intel Core Ultra 9 285K','CPU','Intel',100.00,125,'LGA1851','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(31,'Intel Core Ultra 7 265K','CPU','Intel',96.00,125,'LGA1851','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(32,'AMD Ryzen 9 9950X','CPU','AMD',100.00,170,'AM5','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(33,'AMD Ryzen 9 7950X3D','CPU','AMD',98.00,120,'AM5','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(34,'AMD Ryzen 7 7800X3D','CPU','AMD',95.00,120,'AM5','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(35,'AMD Ryzen 5 7600X','CPU','AMD',85.00,105,'AM5','DDR5','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(36,'AMD Ryzen 5 5600X','CPU','AMD',70.00,65,'AM4','DDR4','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(37,'AMD Ryzen 7 5700X','CPU','AMD',75.00,65,'AM4','DDR4','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(38,'AMD Ryzen 9 5900X','CPU','AMD',85.00,105,'AM4','DDR4','',0,0,0,0,0,0,'','uploads/components/real_cpu.jpg',NULL,NULL),(39,'Noctua NH-D15','CPU Cooler','Noctua',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(40,'Cooler Master Hyper 212','CPU Cooler','Cooler Master',70.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(41,'Corsair iCUE H150i Elite','CPU Cooler','Corsair',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(42,'NZXT Kraken Elite 360','CPU Cooler','NZXT',92.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(43,'DeepCool AK620','CPU Cooler','DeepCool',85.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(44,'Lian Li Galahad II Trinity','CPU Cooler','Lian Li',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(45,'Arctic Liquid Freezer II 360','CPU Cooler','Arctic',94.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(46,'be quiet! Dark Rock Pro 4','CPU Cooler','be quiet!',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(47,'Thermalright Peerless Assassin','CPU Cooler','Thermalright',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(48,'MSI MAG CORELIQUID 240R V2','CPU Cooler','MSI',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(49,'ASUS ROG Maximus Z790 Hero','Motherboard','ASUS',95.00,0,'LGA1700','DDR5','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(50,'MSI MAG Z790 TOMAHAWK WIFI','Motherboard','MSI',88.00,0,'LGA1700','DDR5','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(51,'Gigabyte B760M DS3H','Motherboard','Gigabyte',75.00,0,'LGA1700','DDR4','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(52,'ASUS TUF GAMING B760-PLUS','Motherboard','ASUS',80.00,0,'LGA1700','DDR5','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(53,'ASRock Z690 Phantom Gaming','Motherboard','ASRock',82.00,0,'LGA1700','DDR4','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(54,'Gigabyte Z890 AORUS MASTER','Motherboard','Gigabyte',98.00,0,'LGA1851','DDR5','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(55,'ASUS ROG CROSSHAIR X670E','Motherboard','ASUS',98.00,0,'AM5','DDR5','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(56,'MSI B650M MORTAR WIFI','Motherboard','MSI',85.00,0,'AM5','DDR5','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(57,'Gigabyte B650 AORUS ELITE AX','Motherboard','Gigabyte',88.00,0,'AM5','DDR5','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(58,'ASUS PRIME B650-PLUS','Motherboard','ASUS',80.00,0,'AM5','DDR5','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(59,'MSI B550 TOMAHAWK','Motherboard','MSI',75.00,0,'AM4','DDR4','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(60,'Gigabyte B550M DS3H','Motherboard','Gigabyte',65.00,0,'AM4','DDR4','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(61,'ASUS ROG STRIX B550-F','Motherboard','ASUS',80.00,0,'AM4','DDR4','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(62,'ASRock X570 Steel Legend','Motherboard','ASRock',82.00,0,'AM4','DDR4','',0,0,0,0,0,0,'','uploads/components/real_motherboard.jpg',NULL,NULL),(63,'Corsair Vengeance 32GB (2x16GB) DDR5 6000MHz','RAM','Corsair',90.00,0,'','DDR5','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(64,'G.Skill Trident Z5 RGB 32GB DDR5 6400MHz','RAM','G.Skill',95.00,0,'','DDR5','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(65,'Kingston FURY Beast 16GB DDR5 5200MHz','RAM','Kingston',80.00,0,'','DDR5','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(66,'Corsair Vengeance LPX 16GB DDR4 3200MHz','RAM','Corsair',70.00,0,'','DDR4','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(67,'G.Skill Ripjaws V 32GB DDR4 3600MHz','RAM','G.Skill',80.00,0,'','DDR4','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(68,'TeamGroup T-Force Delta RGB 32GB DDR5 6000','RAM','TeamGroup',88.00,0,'','DDR5','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(69,'Crucial Pro 64GB (2x32GB) DDR5 5600MHz','RAM','Crucial',90.00,0,'','DDR5','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(70,'Patriot Viper Venom 32GB DDR5 6200MHz','RAM','Patriot',92.00,0,'','DDR5','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(71,'AORUS Memory 32GB DDR5 6000MHz','RAM','Gigabyte',88.00,0,'','DDR5','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(72,'Lexar Ares RGB 32GB DDR5 6400MHz','RAM','Lexar',90.00,0,'','DDR5','',0,0,0,0,0,0,'','uploads/components/real_ram.jpg',NULL,NULL),(73,'Samsung 990 PRO 2TB PCIe 4.0 NVMe','Storage','Samsung',98.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(74,'WD Black SN850X 1TB PCIe 4.0 NVMe','Storage','Western Digital',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(75,'Crucial P3 Plus 1TB PCIe 4.0 NVMe','Storage','Crucial',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(76,'Kingston NV2 500GB PCIe 4.0 NVMe','Storage','Kingston',70.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(77,'Seagate Barracuda 2TB 7200RPM HDD','Storage','Seagate',40.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(78,'Samsung 980 1TB NVMe','Storage','Samsung',85.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(79,'Corsair MP600 PRO 2TB PCIe 4.0','Storage','Corsair',96.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(80,'AORUS Gen4 7000s 1TB','Storage','Gigabyte',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(81,'Lexar NM790 2TB PCIe 4.0 NVMe','Storage','Lexar',92.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(82,'WD Blue 1TB SATA SSD','Storage','Western Digital',60.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_storage.jpg',NULL,NULL),(83,'NVIDIA RTX 5090 32GB','Graphics Card','NVIDIA',100.00,500,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(84,'NVIDIA RTX 4090 24GB','Graphics Card','NVIDIA',98.00,450,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(85,'NVIDIA RTX 4080 Super 16GB','Graphics Card','NVIDIA',95.00,320,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(86,'NVIDIA RTX 4070 Super 12GB','Graphics Card','NVIDIA',88.00,220,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(87,'NVIDIA RTX 4060 Ti 8GB','Graphics Card','NVIDIA',80.00,160,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(88,'NVIDIA RTX 4060 8GB','Graphics Card','NVIDIA',75.00,115,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(89,'AMD Radeon RX 7900 XTX 24GB','Graphics Card','AMD',96.00,355,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(90,'AMD Radeon RX 7900 XT 20GB','Graphics Card','AMD',92.00,315,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(91,'AMD Radeon RX 7800 XT 16GB','Graphics Card','AMD',86.00,263,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(92,'AMD Radeon RX 7600 8GB','Graphics Card','AMD',70.00,165,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(93,'Corsair RM1000x 1000W 80+ Gold','Power Supply','Corsair',95.00,0,'','','',0,0,0,0,0,1000,'','uploads/components/real_psu.jpg',NULL,NULL),(94,'SeaSonic FOCUS GX-850 850W 80+ Gold','Power Supply','SeaSonic',92.00,0,'','','',0,0,0,0,0,850,'','uploads/components/real_psu.jpg',NULL,NULL),(95,'Cooler Master MWE Gold 750 V2','Power Supply','Cooler Master',85.00,0,'','','',0,0,0,0,0,750,'','uploads/components/real_cooler.jpg',NULL,NULL),(96,'EVGA SuperNOVA 650 GT 650W','Power Supply','EVGA',80.00,0,'','','',0,0,0,0,0,650,'','uploads/components/real_psu.jpg',NULL,NULL),(97,'Corsair CV550 550W 80+ Bronze','Power Supply','Corsair',65.00,0,'','','',0,0,0,0,0,550,'','uploads/components/real_psu.jpg',NULL,NULL),(98,'MSI MPG A1000G PCIE5 1000W','Power Supply','MSI',96.00,0,'','','',0,0,0,0,0,1000,'','uploads/components/real_psu.jpg',NULL,NULL),(99,'Be Quiet! Straight Power 12 850W','Power Supply','Be Quiet!',94.00,0,'','','',0,0,0,0,0,850,'','uploads/components/real_psu.jpg',NULL,NULL),(100,'Thermaltake Toughpower GF3 1200W','Power Supply','Thermaltake',98.00,0,'','','',0,0,0,0,0,1200,'','uploads/components/real_psu.jpg',NULL,NULL),(101,'DeepCool PQ850M 850W 80+ Gold','Power Supply','DeepCool',88.00,0,'','','',0,0,0,0,0,850,'','uploads/components/real_psu.jpg',NULL,NULL),(102,'Gigabyte P650B 650W 80+ Bronze','Power Supply','Gigabyte',70.00,0,'','','',0,0,0,0,0,650,'','uploads/components/real_psu.jpg',NULL,NULL),(103,'Lian Li PC-O11 Dynamic','Casing','Lian Li',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_case.jpg',NULL,NULL),(104,'NZXT H9 Flow','Casing','NZXT',94.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_case.jpg',NULL,NULL),(105,'Corsair 4000D Airflow','Casing','Corsair',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_case.jpg',NULL,NULL),(106,'Phanteks Eclipse G360A','Casing','Phanteks',85.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_case.jpg',NULL,NULL),(107,'Cooler Master MasterBox TD500','Casing','Cooler Master',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(108,'Montech AIR 903 MAX','Casing','Montech',82.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_case.jpg',NULL,NULL),(109,'Fractal Design North','Casing','Fractal Design',96.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_case.jpg',NULL,NULL),(110,'Antec NX410','Casing','Antec',75.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_case.jpg',NULL,NULL),(111,'DeepCool CH560 Digital','Casing','DeepCool',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_case.jpg',NULL,NULL),(112,'MSI MAG FORGE 112R','Casing','MSI',78.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_case.jpg',NULL,NULL),(113,'LG 27GP850-B 27\" 165Hz 1440p','Monitor','LG',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg',NULL,NULL),(114,'Samsung Odyssey G7 27\" 240Hz','Monitor','Samsung',96.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg',NULL,NULL),(115,'Gigabyte M27Q 27\" 170Hz 1440p','Monitor','Gigabyte',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg',NULL,NULL),(116,'ASUS TUF Gaming VG27AQ 27\"','Monitor','ASUS',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg',NULL,NULL),(117,'AOC 24G2SP 24\" 165Hz 1080p','Monitor','AOC',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg',NULL,NULL),(118,'MSI Optix G241 24\" 144Hz','Monitor','MSI',78.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg',NULL,NULL),(119,'BenQ Zowie XL2546K 24.5\" 240Hz','Monitor','BenQ',92.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg',NULL,NULL),(120,'Dell S2721DGF 27\" 165Hz','Monitor','Dell',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg',NULL,NULL),(121,'Acer Nitro VG271U 27\" 144Hz','Monitor','Acer',0.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg','',''),(122,'ViewSonic VX2758-2KP-MHD 27\"','Monitor','ViewSonic',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_monitor.jpg',NULL,NULL),(123,'Lian Li UNI FAN SL-INF 120 (3-Pack)','Casing Cooler','Lian Li',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(124,'Corsair LL120 RGB (3-Pack)','Casing Cooler','Corsair',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(125,'Noctua NF-A12x25 PWM','Casing Cooler','Noctua',98.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(126,'Arctic P12 PWM PST (5-Pack)','Casing Cooler','Arctic',92.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(127,'Cooler Master MasterFan MF120 Halo','Casing Cooler','Cooler Master',85.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(128,'NZXT F120 RGB (3-Pack)','Casing Cooler','NZXT',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(129,'DeepCool FC120 (3-Pack)','Casing Cooler','DeepCool',82.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(130,'Be Quiet! Silent Wings 4 120mm','Casing Cooler','Be Quiet!',94.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(131,'Thermalright TL-C12C (3-Pack)','Casing Cooler','Thermalright',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(132,'MSI MAG MAX F12A-3H','Casing Cooler','MSI',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(133,'Keychron Q1 Pro Wireless Mechanical','Keyboard','Keychron',96.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_keyboard.jpg',NULL,NULL),(134,'Corsair K70 RGB PRO Mechanical','Keyboard','Corsair',92.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_keyboard.jpg',NULL,NULL),(135,'Logitech G Pro X TKL','Keyboard','Logitech',94.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_keyboard.jpg',NULL,NULL),(136,'Razer Huntsman V3 Pro TKL','Keyboard','Razer',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_keyboard.jpg',NULL,NULL),(137,'SteelSeries Apex Pro TKL','Keyboard','SteelSeries',96.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_keyboard.jpg',NULL,NULL),(138,'Royal Kludge RK61 Wireless','Keyboard','Royal Kludge',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_keyboard.jpg',NULL,NULL),(139,'Redragon K552 Kumara','Keyboard','Redragon',75.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_keyboard.jpg',NULL,NULL),(140,'HyperX Alloy Origins Core','Keyboard','HyperX',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(141,'Asus ROG Azoth Wireless','Keyboard','ASUS',98.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_keyboard.jpg',NULL,NULL),(142,'Akko 3098B Plus Wireless','Keyboard','Akko',85.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_keyboard.jpg',NULL,NULL),(143,'Logitech G Pro X Superlight 2','Mouse','Logitech',98.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(144,'Razer DeathAdder V3 Pro','Mouse','Razer',96.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(145,'Zowie EC2-CW Wireless','Mouse','Zowie',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(146,'Endgame Gear XM2we','Mouse','Endgame Gear',92.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(147,'Lamzu Atlantis Mini','Mouse','Lamzu',94.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(148,'Glorious Model O Wireless','Mouse','Glorious',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(149,'SteelSeries Aerox 3 Wireless','Mouse','SteelSeries',85.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(150,'Corsair Harpoon RGB Wireless','Mouse','Corsair',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(151,'Razer Viper Mini','Mouse','Razer',85.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(152,'Logitech G304 Lightspeed','Mouse','Logitech',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_mouse.jpg',NULL,NULL),(153,'Logitech Z906 5.1 Surround','Speaker & Home Theater','Logitech',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(154,'Edifier R1280DB Powered Bookshelf','Speaker & Home Theater','Edifier',92.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(155,'Creative Pebble V3','Speaker & Home Theater','Creative',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(156,'Razer Leviathan V2 Soundbar','Speaker & Home Theater','Razer',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(157,'Microlab X2 2.1 Speaker','Speaker & Home Theater','Microlab',75.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(158,'Fantech GS203 Beat','Speaker & Home Theater','Fantech',65.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(159,'Logitech Z623 2.1 THX','Speaker & Home Theater','Logitech',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(160,'Edifier S3000Pro','Speaker & Home Theater','Edifier',98.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(161,'HyperX Cloud III Wireless','Headphone','HyperX',92.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_gpu.jpg',NULL,NULL),(162,'Logitech G Pro X 2 Lightspeed','Headphone','Logitech',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(163,'Razer BlackShark V2 Pro','Headphone','Razer',94.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(164,'SteelSeries Arctis Nova 7','Headphone','SteelSeries',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(165,'Corsair HS80 RGB Wireless','Headphone','Corsair',88.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(166,'Audio-Technica ATH-M50x','Headphone','Audio-Technica',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(167,'Sennheiser HD 560S','Headphone','Sennheiser',96.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(168,'Fantech HG11 Captain 7.1','Headphone','Fantech',70.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_cooler.jpg',NULL,NULL),(169,'Havit H2002d Gaming Headset','Headphone','Havit',75.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(170,'TP-Link Archer TX3000E WiFi 6 PCIe','Wifi Adapter / LAN Card','TP-Link',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(171,'Asus PCE-AX58BT WiFi 6 PCIe','Wifi Adapter / LAN Card','ASUS',94.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(172,'TP-Link TL-WN823N Mini USB','Wifi Adapter / LAN Card','TP-Link',75.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(173,'D-Link DWA-182 AC1200 USB','Wifi Adapter / LAN Card','D-Link',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(174,'Netgear Nighthawk A7000 USB','Wifi Adapter / LAN Card','Netgear',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(175,'Mercusys MU6H AC650 USB','Wifi Adapter / LAN Card','Mercusys',70.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(176,'Kaspersky Total Security 1 User 1 Year','Anti Virus','Kaspersky',98.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(177,'Bitdefender Internet Security 1 User','Anti Virus','Bitdefender',96.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(178,'ESET NOD32 Antivirus 1 User','Anti Virus','ESET',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(179,'Norton 360 Deluxe 3 Devices','Anti Virus','Norton',94.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(180,'McAfee Total Protection 1 User','Anti Virus','McAfee',85.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(181,'Apollo 1200VA Offline UPS','UPS','Apollo',80.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(182,'Digital X 1200VA UPS','UPS','Digital X',78.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(183,'Power Guard 1200VA UPS','UPS','Power Guard',75.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(184,'APC Back-UPS 1200VA','UPS','APC',95.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(185,'MaxGreen 1200VA Offline UPS','UPS','MaxGreen',82.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(186,'Vertiv Liebert ItON 1000VA','UPS','Vertiv',90.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL),(187,'Walton WUPS 1200VA','UPS','Walton',75.00,0,'','','',0,0,0,0,0,0,'','uploads/components/real_peripheral.jpg',NULL,NULL);
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
  `image_path` varchar(255) DEFAULT NULL,
  `community_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`post_id`),
  KEY `fk_user_id_post` (`user_id`),
  KEY `fk_post_community` (`community_id`),
  CONSTRAINT `fk_post_community` FOREIGN KEY (`community_id`) REFERENCES `community` (`community_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_user_id_post` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
INSERT INTO `post` VALUES (3,3,'RTX vs RX','Which one should I buy?','2026-05-16 05:40:07',NULL,NULL),(4,4,'Need Editing Setup','Best PC for Adobe Premiere?','2026-05-16 05:40:07',NULL,NULL),(5,5,'SSD Recommendation','Suggest fast SSD.','2026-05-16 05:40:07',NULL,NULL),(6,6,'CPU Temperature Issue','CPU getting too hot.','2026-05-16 05:40:07',NULL,NULL),(7,7,'Best RGB Fans','Need aesthetic fans.','2026-05-16 05:40:07',NULL,NULL),(8,8,'Laptop vs Desktop','Which is better for gaming?','2026-05-16 05:40:07',NULL,NULL),(9,9,'Need Silent Build','Suggest low-noise setup.','2026-05-16 05:40:07',NULL,NULL),(10,10,'Upgrade Advice','Should I upgrade GPU first?','2026-05-16 05:40:07',NULL,NULL),(11,11,'Need RTX Build','Suggest RTX 5080 build.','2026-05-16 05:40:07',NULL,NULL),(12,12,'Best CPU Cooler','Which cooler is best?','2026-05-16 05:40:07',NULL,NULL),(13,13,'Need Budget GPU','GPU under 30k?','2026-05-16 05:40:07',NULL,NULL),(14,14,'4K Gaming Setup','Need high-end gaming PC.','2026-05-16 05:40:07',NULL,NULL),(15,15,'Best SSD Brand','Samsung or WD?','2026-05-16 05:40:07',NULL,NULL),(16,16,'RGB Build Ideas','Need aesthetic setup.','2026-05-16 05:40:07',NULL,NULL),(17,17,'High FPS Build','Need 240 FPS build.','2026-05-16 05:40:07',NULL,NULL),(18,18,'PC Upgrade Help','Should I upgrade RAM?','2026-05-16 05:40:07',NULL,NULL),(19,19,'Best PSU','Need reliable PSU.','2026-05-16 05:40:07',NULL,NULL),(20,20,'Streaming PC Advice','Need dual PC setup.','2026-05-16 05:40:07',NULL,NULL);
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
INSERT INTO `posttag` VALUES (3,3,'2026-05-16 05:40:07'),(4,4,'2026-05-16 05:40:07'),(5,7,'2026-05-16 05:40:07'),(6,8,'2026-05-16 05:40:07'),(7,9,'2026-05-16 05:40:07'),(8,1,'2026-05-16 05:40:07'),(9,8,'2026-05-16 05:40:07'),(10,10,'2026-05-16 05:40:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `storeavailability`
--

LOCK TABLES `storeavailability` WRITE;
/*!40000 ALTER TABLE `storeavailability` DISABLE KEYS */;
INSERT INTO `storeavailability` VALUES (1,1,1,'In Stock',35000.00),(2,2,2,'Limited',48000.00),(3,3,3,'In Stock',25000.00),(5,5,5,'In Stock',15000.00),(6,6,6,'Limited',9000.00),(8,8,8,'In Stock',7000.00),(9,9,9,'Limited',85000.00),(10,10,10,'In Stock',95000.00),(11,1,23,'in_stock',85000.00),(12,1,24,'in_stock',55000.00),(13,1,25,'in_stock',38000.00),(14,1,26,'in_stock',22000.00),(15,1,27,'in_stock',13500.00),(16,1,28,'in_stock',68000.00),(17,1,29,'in_stock',48000.00),(18,1,30,'in_stock',95000.00),(19,1,31,'in_stock',65000.00),(20,1,32,'in_stock',92000.00),(21,1,33,'in_stock',88000.00),(22,1,34,'in_stock',48000.00),(23,1,35,'in_stock',28000.00),(24,1,36,'in_stock',18500.00),(25,1,37,'in_stock',24000.00),(26,1,38,'in_stock',42000.00),(27,1,39,'in_stock',12000.00),(28,1,40,'in_stock',4500.00),(29,1,41,'in_stock',22000.00),(30,1,42,'in_stock',28000.00),(31,1,43,'in_stock',7500.00),(32,1,44,'in_stock',18500.00),(33,1,45,'in_stock',14000.00),(34,1,46,'in_stock',11000.00),(35,1,47,'in_stock',5500.00),(36,1,48,'in_stock',10500.00),(37,1,49,'in_stock',85000.00),(38,1,50,'in_stock',35000.00),(39,1,51,'in_stock',14500.00),(40,1,52,'in_stock',24000.00),(41,1,53,'in_stock',22000.00),(42,1,54,'in_stock',92000.00),(43,1,55,'in_stock',88000.00),(44,1,56,'in_stock',26000.00),(45,1,57,'in_stock',32000.00),(46,1,58,'in_stock',22000.00),(47,1,59,'in_stock',18500.00),(48,1,60,'in_stock',11500.00),(49,1,61,'in_stock',24000.00),(50,1,62,'in_stock',28000.00),(51,1,63,'in_stock',15500.00),(52,1,64,'in_stock',18500.00),(53,1,65,'in_stock',7500.00),(54,1,66,'in_stock',4800.00),(55,1,67,'in_stock',9500.00),(56,1,68,'in_stock',14500.00),(57,1,69,'in_stock',28000.00),(58,1,70,'in_stock',16000.00),(59,1,71,'in_stock',16500.00),(60,1,72,'in_stock',14000.00),(61,1,73,'in_stock',24000.00),(62,1,74,'in_stock',11500.00),(63,1,75,'in_stock',7500.00),(64,1,76,'in_stock',4500.00),(65,1,77,'in_stock',6500.00),(66,1,78,'in_stock',9500.00),(67,1,79,'in_stock',22000.00),(68,1,80,'in_stock',12500.00),(69,1,81,'in_stock',16000.00),(70,1,82,'in_stock',8500.00),(71,1,83,'in_stock',350000.00),(72,1,84,'in_stock',280000.00),(73,1,85,'in_stock',145000.00),(74,1,86,'in_stock',85000.00),(75,1,87,'in_stock',55000.00),(76,1,88,'in_stock',38000.00),(77,1,89,'in_stock',135000.00),(78,1,90,'in_stock',105000.00),(79,1,91,'in_stock',72000.00),(80,1,92,'in_stock',35000.00),(81,1,93,'in_stock',22000.00),(82,1,94,'in_stock',16500.00),(83,1,95,'in_stock',11500.00),(84,1,96,'in_stock',9500.00),(85,1,97,'in_stock',5500.00),(86,1,98,'in_stock',24000.00),(87,1,99,'in_stock',19000.00),(88,1,100,'in_stock',28000.00),(89,1,101,'in_stock',14000.00),(90,1,102,'in_stock',6500.00),(91,1,103,'in_stock',16500.00),(92,1,104,'in_stock',18500.00),(93,1,105,'in_stock',11500.00),(94,1,106,'in_stock',9500.00),(95,1,107,'in_stock',10500.00),(96,1,108,'in_stock',8500.00),(97,1,109,'in_stock',22000.00),(98,1,110,'in_stock',6500.00),(99,1,111,'in_stock',12500.00),(100,1,112,'in_stock',7500.00),(101,1,113,'in_stock',48000.00),(102,1,114,'in_stock',68000.00),(103,1,115,'in_stock',42000.00),(104,1,116,'in_stock',45000.00),(105,1,117,'in_stock',22000.00),(106,1,118,'in_stock',21000.00),(107,1,119,'in_stock',55000.00),(108,1,120,'in_stock',48000.00),(109,1,121,'in_stock',32000.00),(110,1,122,'in_stock',35000.00),(111,1,123,'in_stock',11500.00),(112,1,124,'in_stock',10500.00),(113,1,125,'in_stock',3500.00),(114,1,126,'in_stock',4500.00),(115,1,127,'in_stock',2200.00),(116,1,128,'in_stock',8500.00),(117,1,129,'in_stock',4200.00),(118,1,130,'in_stock',2800.00),(119,1,131,'in_stock',1500.00),(120,1,132,'in_stock',3800.00),(121,1,133,'in_stock',22000.00),(122,1,134,'in_stock',18500.00),(123,1,135,'in_stock',16500.00),(124,1,136,'in_stock',24000.00),(125,1,137,'in_stock',21000.00),(126,1,138,'in_stock',4500.00),(127,1,139,'in_stock',3200.00),(128,1,140,'in_stock',11500.00),(129,1,141,'in_stock',28000.00),(130,1,142,'in_stock',9500.00),(131,1,143,'in_stock',18500.00),(132,1,144,'in_stock',16500.00),(133,1,145,'in_stock',15500.00),(134,1,146,'in_stock',9500.00),(135,1,147,'in_stock',11000.00),(136,1,148,'in_stock',8500.00),(137,1,149,'in_stock',7500.00),(138,1,150,'in_stock',5500.00),(139,1,151,'in_stock',4200.00),(140,1,152,'in_stock',4500.00),(141,1,153,'in_stock',35000.00),(142,1,154,'in_stock',12500.00),(143,1,155,'in_stock',4500.00),(144,1,156,'in_stock',26000.00),(145,1,157,'in_stock',6500.00),(146,1,158,'in_stock',1500.00),(147,1,159,'in_stock',18500.00),(148,1,160,'in_stock',65000.00),(149,1,161,'in_stock',16500.00),(150,1,162,'in_stock',24000.00),(151,1,163,'in_stock',18500.00),(152,1,164,'in_stock',19500.00),(153,1,165,'in_stock',15500.00),(154,1,166,'in_stock',18000.00),(155,1,167,'in_stock',22000.00),(156,1,168,'in_stock',2800.00),(157,1,169,'in_stock',2200.00),(158,1,170,'in_stock',5500.00),(159,1,171,'in_stock',6500.00),(160,1,172,'in_stock',800.00),(161,1,173,'in_stock',2200.00),(162,1,174,'in_stock',7500.00),(163,1,175,'in_stock',950.00),(164,1,176,'in_stock',1200.00),(165,1,177,'in_stock',1100.00),(166,1,178,'in_stock',900.00),(167,1,179,'in_stock',2500.00),(168,1,180,'in_stock',850.00),(169,1,181,'in_stock',6500.00),(170,1,182,'in_stock',5800.00),(171,1,183,'in_stock',5500.00),(172,1,184,'in_stock',12500.00),(173,1,185,'in_stock',6200.00),(174,1,186,'in_stock',9500.00),(175,1,187,'in_stock',5200.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` VALUES (2,'Budget'),(11,'build'),(8,'Cooling'),(4,'CPU'),(1,'Gaming'),(3,'GPU'),(5,'Motherboard'),(6,'RAM'),(9,'RGB'),(7,'Storage'),(10,'Upgrade');
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
  `role` enum('user','admin','moderator') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Shadman Ahammad','shadman1@pcbuild.com','$2y$12$CUV6.EXXxFP4ZDvMJ2ZPn.0gvj.5pTusfAP0aze2dSSRO02ulezKm','admin','2026-05-19 10:21:58'),(2,'Rahim Uddin','rahim2@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(3,'Karim Hasan','karim3@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(4,'Nusrat Jahan','nusrat4@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(5,'Tanvir Ahmed','tanvir5@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(6,'Faria Islam','faria6@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(7,'Sabbir Hossain','sabbir7@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(8,'Mehedi Hasan','mehedi8@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(9,'Tanjila Akter','tanjila9@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(10,'Arifur Rahman','arif10@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(11,'Arian Khan','arian11@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(12,'Sakib Ahmed','sakib12@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(13,'Nabil Hossain','nabil13@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(14,'Jubayer Islam','jubayer14@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(15,'Towsif Rahman','towsif15@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(16,'Fahim Hasan','fahim16@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(17,'Imran Chowdhury','imran17@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(18,'Rifat Karim','rifat18@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(19,'Nahid Hasan','nahid19@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(20,'Mahin Ahmed','mahin20@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(21,'Sarah Johnson','sarah21@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(22,'Michael Lee','michael22@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(23,'Emily Clark','emily23@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(24,'Daniel Smith','daniel24@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(25,'Sophia Brown','sophia25@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(26,'Ethan Walker','ethan26@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(27,'Olivia White','olivia27@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(28,'Noah Harris','noah28@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(29,'Mason Scott','mason29@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(30,'Ava Green','ava30@pcbuild.com','pass1234','user','2026-05-19 10:21:58'),(31,'Test User','testuser+reg@example.com','$2y$12$JOiReydyMndOn2bJPS9PR.IHGvMJO0RsIUCZbyiIK/EjTyrsl6xLW','user','2026-05-19 15:51:09'),(32,'Seaman','smseaman7@gmail.com','$2y$12$HVkaG2Yfs15G7IVGlZ0VhOm5HLML.oCip2Q9nd1p.YnjjbKR14.h6','admin','2026-05-19 16:00:50'),(33,'Dr','d@gmail.com','$2y$12$qzCkbXdWQUuVo4Gx3kOojuvL8H.WNSt0ejamyaPglQ99kqmRQRpDi','user','2026-05-19 16:07:10'),(34,'Mr Dr','dr@gmail.com','$2y$12$.1McqjVkaN7dLn2gMj4Kou0VLSUK6uyqsJx3bYtSDEn5zSIA8JVTO','user','2026-05-19 16:08:09'),(35,'Dr M','drm@gmail.com','$2y$12$RqK1qF6jvRTyYP.CKLVDpudBeNDZkLtG6/PDccYV4OAg8ezTcsM3K','user','2026-05-19 16:19:21'),(36,'TestUser1779227871','testuser1779227871@example.com','$2y$12$IX9rJybylq3OpCh4MPgcCObpyCpH9zFXf8097K.7eMpTCZjy6Eq8W','user','2026-05-19 21:57:52'),(37,'Test User','testuser_2e24210c@pcbuild.com','$2y$12$LgHLbfRuko6eIRo23JdQK.HSflQsj/VCZy30svYwzNmzWBvvQqCtW','user','2026-05-19 22:02:11'),(38,'Test User','testuser_1779228374@pcbuild.com','$2y$12$xP5KqDBHewF/.pcDSI9t7ORsozXjffDVp.UgfLqv.p4bME0DEKJpW','user','2026-05-19 22:06:15'),(39,'Test1','test@gmail.com','$2y$12$IRozdla/QzDEEXfxSfU0pemsn8RmOthhnHTfzBIURk3WxzPrb8VGO','moderator','2026-05-19 23:57:41');
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vote`
--

LOCK TABLES `vote` WRITE;
/*!40000 ALTER TABLE `vote` DISABLE KEYS */;
INSERT INTO `vote` VALUES (3,3,3,NULL,'downvote','2026-05-16 05:40:07'),(4,4,4,NULL,'upvote','2026-05-16 05:40:07'),(5,5,5,NULL,'upvote','2026-05-16 05:40:07'),(8,8,NULL,3,'downvote','2026-05-16 05:40:07'),(9,9,NULL,4,'upvote','2026-05-16 05:40:07'),(10,10,NULL,5,'upvote','2026-05-16 05:40:07');
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

-- Dump completed on 2026-06-11 13:24:45
