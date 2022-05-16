-- MySQL dump 10.19  Distrib 10.3.31-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: api-chat
-- ------------------------------------------------------
-- Server version	10.3.31-MariaDB-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `user_contacts`
--

DROP TABLE IF EXISTS `user_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `contact_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint(20) unsigned NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_contacts_user_id_foreign` (`user_id`),
  KEY `user_contacts_contact_type_contact_id_index` (`contact_type`,`contact_id`),
  CONSTRAINT `user_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_contacts`
--

LOCK TABLES `user_contacts` WRITE;
/*!40000 ALTER TABLE `user_contacts` DISABLE KEYS */;
INSERT INTO `user_contacts` VALUES (1,1,'App\\\\User',2,NULL,NULL,NULL),(2,1,'App\\\\User',3,NULL,NULL,NULL),(3,1,'App\\\\User',4,NULL,NULL,NULL),(4,2,'App\\\\User',1,NULL,NULL,NULL),(5,2,'App\\\\User',3,NULL,NULL,NULL),(6,3,'App\\\\User',1,NULL,NULL,NULL),(7,3,'App\\\\User',2,NULL,NULL,NULL),(8,3,'App\\\\User',5,NULL,NULL,NULL),(9,1,'App\\\\Models\\\\Group',1,NULL,NULL,NULL),(10,1,'App\\\\Models\\\\Group',3,NULL,NULL,NULL),(11,2,'App\\\\Models\\\\Group',1,NULL,NULL,NULL),(12,2,'App\\\\Models\\\\Group',3,NULL,NULL,NULL),(13,3,'App\\\\Models\\\\Group',1,NULL,NULL,NULL),(14,3,'App\\\\Models\\\\Group',3,NULL,NULL,NULL),(15,5,'App\\\\Models\\\\Group',2,NULL,NULL,NULL),(16,5,'App\\\\Models\\\\Group',3,NULL,NULL,NULL),(17,6,'App\\\\Models\\\\Group',2,NULL,NULL,NULL),(18,6,'App\\\\Models\\\\Group',3,NULL,NULL,NULL),(19,5,'App\\\\User',3,NULL,NULL,NULL),(20,5,'App\\\\User',6,NULL,NULL,NULL),(21,6,'App\\\\User',5,NULL,NULL,NULL);
/*!40000 ALTER TABLE `user_contacts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-05-16 11:40:07