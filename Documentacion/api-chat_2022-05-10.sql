# ************************************************************
# Sequel Pro SQL dump
# Versión 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.29)
# Base de datos: api-chat
# Tiempo de Generación: 2022-05-10 14:20:13 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Volcado de tabla conversations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `conversations`;

CREATE TABLE `conversations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('GROUP','INDIVIDUAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id_1` bigint(20) unsigned DEFAULT NULL,
  `user_id_2` bigint(20) unsigned DEFAULT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_user_id_1_foreign` (`user_id_1`),
  KEY `conversations_user_id_2_foreign` (`user_id_2`),
  KEY `conversations_group_id_foreign` (`group_id`),
  CONSTRAINT `conversations_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  CONSTRAINT `conversations_user_id_1_foreign` FOREIGN KEY (`user_id_1`) REFERENCES `users` (`id`),
  CONSTRAINT `conversations_user_id_2_foreign` FOREIGN KEY (`user_id_2`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;

INSERT INTO `conversations` (`id`, `type`, `user_id_1`, `user_id_2`, `group_id`, `created_at`, `updated_at`)
VALUES
	(1,'INDIVIDUAL',1,2,NULL,'2022-04-22 16:11:35','2022-04-22 16:11:35'),
	(2,'INDIVIDUAL',1,3,NULL,'2022-04-28 16:11:35','2022-04-28 16:11:35'),
	(3,'INDIVIDUAL',2,3,NULL,'2022-04-22 16:11:35','2022-04-28 16:11:35'),
	(4,'INDIVIDUAL',3,5,NULL,'2022-04-22 16:11:35','2022-05-02 16:11:35'),
	(5,'GROUP',NULL,NULL,1,'2022-04-22 16:11:35','2022-05-03 16:11:35'),
	(6,'GROUP',NULL,NULL,3,'2022-04-22 16:11:35','2022-05-05 16:11:35');

/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;


# Volcado de tabla failed_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Volcado de tabla file_messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `file_messages`;

CREATE TABLE `file_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Volcado de tabla groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;

INSERT INTO `groups` (`id`, `name`, `created_at`, `updated_at`)
VALUES
	(1,'Grupo CIDESO',NULL,NULL),
	(2,'Grupo CPS',NULL,NULL),
	(3,'Grupo CPS-CIDESO',NULL,NULL);

/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;


# Volcado de tabla message_visualizations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `message_visualizations`;

CREATE TABLE `message_visualizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_visualizations_message_id_foreign` (`message_id`),
  KEY `message_visualizations_user_id_foreign` (`user_id`),
  CONSTRAINT `message_visualizations_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`),
  CONSTRAINT `message_visualizations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `message_visualizations` WRITE;
/*!40000 ALTER TABLE `message_visualizations` DISABLE KEYS */;

INSERT INTO `message_visualizations` (`id`, `message_id`, `user_id`, `created_at`, `updated_at`)
VALUES
	(3,1,2,'2022-03-22 16:11:35','2022-03-22 16:11:35'),
	(4,2,3,'2022-04-27 16:11:35','2022-04-27 16:11:35');

/*!40000 ALTER TABLE `message_visualizations` ENABLE KEYS */;
UNLOCK TABLES;


# Volcado de tabla messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `message_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_conversation_id_foreign` (`conversation_id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_message_type_message_id_index` (`message_type`,`message_id`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`),
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message_type`, `message_id`, `created_at`, `updated_at`)
VALUES
	(1,1,1,'App\\Models\\TextMessage',1,'2022-03-22 16:11:35','2022-03-22 16:11:35'),
	(2,5,1,'App\\Models\\TextMessage',2,'2022-04-22 16:11:35','2022-04-22 16:11:35'),
	(3,1,2,'App\\Models\\TextMessage',3,'2022-04-22 16:11:35','2022-04-22 16:11:35'),
	(4,2,3,'App\\Models\\TextMessage',4,'2022-04-28 16:11:35','2022-04-28 16:11:35'),
	(5,3,2,'App\\Models\\TextMessage',5,'2022-04-28 16:11:35','2022-04-28 16:11:35'),
	(6,4,3,'App\\Models\\TextMessage',6,'2022-05-02 16:11:35','2022-05-02 16:11:35'),
	(7,6,5,'App\\Models\\TextMessage',7,'2022-05-10 16:11:35','2022-05-10 16:11:35');

/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;


# Volcado de tabla migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`id`, `migration`, `batch`)
VALUES
	(1,'0000_00_00_000000_create_websockets_statistics_entries_table',1),
	(2,'2014_10_12_000000_create_users_table',1),
	(3,'2019_08_19_000000_create_failed_jobs_table',1),
	(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
	(5,'2020_05_05_153157_create_groups_table',1),
	(6,'2020_06_18_175818_create_conversations_table',1),
	(7,'2020_06_19_134547_create_messages_table',1),
	(8,'2020_08_21_182649_create_file_messages_table',1),
	(9,'2020_08_21_182710_create_text_messages_table',1),
	(10,'2021_06_09_132504_create_position_messages_table',1),
	(11,'2022_05_05_153257_create_user_contacts_table',1),
	(12,'2022_05_05_153329_create_message_visualizations_table',1);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Volcado de tabla personal_access_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Volcado de tabla position_messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `position_messages`;

CREATE TABLE `position_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lat` decimal(10,6) NOT NULL,
  `lon` decimal(10,6) NOT NULL,
  `alt` decimal(10,6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Volcado de tabla text_messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `text_messages`;

CREATE TABLE `text_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `text_messages` WRITE;
/*!40000 ALTER TABLE `text_messages` DISABLE KEYS */;

INSERT INTO `text_messages` (`id`, `text`)
VALUES
	(1,'Hola Mundo!!'),
	(2,'Hola GRUPO!!'),
	(3,'Hola Vale!'),
	(4,'Hola Vale! Soy Paul!'),
	(5,'Hola Paul! Soy Javi'),
	(6,'Hola Gabriel! Soy Paul'),
	(7,'Hola Grupo CPS-CIDESO! Soy Brian');

/*!40000 ALTER TABLE `text_messages` ENABLE KEYS */;
UNLOCK TABLES;


# Volcado de tabla user_contacts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_contacts`;

CREATE TABLE `user_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `contact_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_contacts_user_id_foreign` (`user_id`),
  KEY `user_contacts_contact_type_contact_id_index` (`contact_type`,`contact_id`),
  CONSTRAINT `user_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `user_contacts` WRITE;
/*!40000 ALTER TABLE `user_contacts` DISABLE KEYS */;

INSERT INTO `user_contacts` (`id`, `user_id`, `contact_type`, `contact_id`, `created_at`, `updated_at`)
VALUES
	(1,2,'App\\User',3,NULL,NULL),
	(2,2,'App\\User',1,NULL,NULL),
	(4,2,'App\\Models\\Group',3,NULL,NULL),
	(5,3,'App\\User',1,NULL,NULL),
	(6,3,'App\\User',2,NULL,NULL),
	(7,3,'App\\Models\\Group',1,NULL,NULL),
	(8,2,'App\\Models\\Group',1,NULL,NULL),
	(9,3,'App\\Models\\Group',3,NULL,NULL),
	(10,1,'App\\User',4,NULL,NULL),
	(11,4,'App\\Models\\Group',1,NULL,NULL),
	(12,1,'App\\User',2,NULL,NULL),
	(13,1,'App\\Models\\Group',3,NULL,NULL),
	(14,1,'App\\Models\\Group',1,NULL,NULL),
	(15,1,'App\\User',3,NULL,NULL),
	(16,5,'App\\User',3,NULL,NULL),
	(17,5,'App\\Models\\Group',2,NULL,NULL),
	(18,5,'App\\Models\\Group',3,NULL,NULL),
	(19,5,'App\\User',6,NULL,NULL),
	(21,6,'App\\User',5,NULL,NULL),
	(22,6,'App\\Models\\Group',2,NULL,NULL),
	(23,6,'App\\Models\\Group',3,NULL,NULL),
	(24,4,'App\\Models\\Group',3,NULL,NULL);

/*!40000 ALTER TABLE `user_contacts` ENABLE KEYS */;
UNLOCK TABLES;


# Volcado de tabla users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `created_at`, `updated_at`)
VALUES
	(1,'Valeria',NULL,NULL),
	(2,'Javier',NULL,NULL),
	(3,'Paul',NULL,NULL),
	(4,'Hernán',NULL,NULL),
	(5,'Gabriel',NULL,NULL),
	(6,'Brian',NULL,NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Volcado de tabla websockets_statistics_entries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `websockets_statistics_entries`;

CREATE TABLE `websockets_statistics_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peak_connection_count` int(11) NOT NULL,
  `websocket_message_count` int(11) NOT NULL,
  `api_message_count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
