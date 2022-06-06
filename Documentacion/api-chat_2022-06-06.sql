-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 06-06-2022 a las 15:16:33
-- Versión del servidor: 8.0.29-0ubuntu0.20.04.3
-- Versión de PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `api-chat`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conversations`
--

DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('GROUP','INDIVIDUAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id_1` bigint UNSIGNED DEFAULT NULL,
  `user_id_2` bigint UNSIGNED DEFAULT NULL,
  `group_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `conversations`
--

INSERT INTO `conversations` (`id`, `type`, `user_id_1`, `user_id_2`, `group_id`, `created_at`, `updated_at`) VALUES
(1, 'GROUP', NULL, NULL, 1, NULL, '2022-06-02 13:21:43'),
(2, 'INDIVIDUAL', 1, 2, NULL, NULL, '2022-05-30 14:39:08'),
(3, 'INDIVIDUAL', 1, 3, NULL, NULL, '2022-06-06 14:47:54'),
(4, 'INDIVIDUAL', 2, 3, NULL, NULL, '2022-06-06 13:53:03'),
(5, 'INDIVIDUAL', 3, 5, NULL, NULL, NULL),
(6, 'GROUP', NULL, NULL, 3, NULL, NULL),
(7, 'GROUP', NULL, NULL, 2, NULL, NULL),
(8, 'INDIVIDUAL', 1, 4, NULL, NULL, NULL),
(9, 'INDIVIDUAL', 5, 7, NULL, '2022-05-30 09:10:20', '2022-05-30 09:10:20'),
(10, 'INDIVIDUAL', 6, 7, NULL, '2022-05-30 09:10:20', '2022-05-30 09:10:20'),
(11, 'INDIVIDUAL', 8, 1, NULL, '2022-06-06 12:10:33', '2022-06-06 13:21:33'),
(12, 'INDIVIDUAL', 8, 2, NULL, '2022-06-06 12:10:33', '2022-06-06 13:21:33'),
(13, 'INDIVIDUAL', 8, 3, NULL, '2022-06-06 12:10:33', '2022-06-06 13:21:33'),
(14, 'INDIVIDUAL', 8, 4, NULL, '2022-06-06 12:10:33', '2022-06-06 13:21:33'),
(15, 'INDIVIDUAL', 8, 5, NULL, '2022-06-06 12:10:33', '2022-06-06 13:21:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `file_messages`
--

DROP TABLE IF EXISTS `file_messages`;
CREATE TABLE `file_messages` (
  `id` int UNSIGNED NOT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `file_messages`
--

INSERT INTO `file_messages` (`id`, `file`, `original_file`, `description`, `created_at`, `updated_at`) VALUES
(1, 'files/buscar-1548554_1653921548.png', 'buscar-1548554.png', 'buscar-1548554.png', '2022-05-30 14:39:08', '2022-05-30 14:39:08'),
(2, 'files/PruebaArchivoTexto-0898170_1654030898.pdf', 'PruebaArchivoTexto-0898170.pdf', 'PruebaArchivoTexto-0898170.pdf', '2022-05-31 21:01:38', '2022-05-31 21:01:38'),
(3, 'files/SATAC Estampado-1509029_1654031509.png', 'SATAC Estampado-1509029.png', 'SATAC Estampado-1509029.png', '2022-05-31 21:11:49', '2022-05-31 21:11:49'),
(4, 'files/buscar-6052812_1654176052.png', 'buscar-6052812.png', 'buscar-6052812.png', '2022-06-02 13:20:52', '2022-06-02 13:20:52'),
(5, 'files/buscar-6075004_1654176075.png', 'buscar-6075004.png', 'buscar-6075004.png', '2022-06-02 13:21:15', '2022-06-02 13:21:15'),
(6, 'files/enviar-6080182_1654176080.png', 'enviar-6080182.png', 'enviar-6080182.png', '2022-06-02 13:21:20', '2022-06-02 13:21:20'),
(7, 'files/contacto-6087881_1654176087.png', 'contacto-6087881.png', 'contacto-6087881.png', '2022-06-02 13:21:27', '2022-06-02 13:21:27'),
(8, 'files/img-6103307_1654176103.zip', 'img-6103307.zip', 'img-6103307.zip', '2022-06-02 13:21:43', '2022-06-02 13:21:43'),
(9, 'files/Captura de Pantalla PRUEBA-7867869_1654177867.png', 'Captura de Pantalla PRUEBA-7867869.png', 'Captura de Pantalla PRUEBA-7867869.png', '2022-06-02 13:51:07', '2022-06-02 13:51:07'),
(10, 'files/img-0741045_1_1654260740.zip', 'img-0741045.zip', 'img-0741045.zip', '2022-06-03 12:52:20', '2022-06-03 12:52:20'),
(11, 'files/Captura de pantalla_2022-06-03_09-47-05-0889759_0_1654260889.png', 'Captura de pantalla_2022-06-03_09-47-05-0889759.png', 'Captura de pantalla_2022-06-03_09-47-05-0889759.png', '2022-06-03 12:54:49', '2022-06-03 12:54:49'),
(12, 'files/enviar-0956831_0_1654260956.png', 'enviar-0956831.png', 'enviar-0956831.png', '2022-06-03 12:55:56', '2022-06-03 12:55:56'),
(13, 'files/enviar-0971725_0_1654260971.png', 'enviar-0971725.png', 'enviar-0971725.png', '2022-06-03 12:56:11', '2022-06-03 12:56:11'),
(14, 'files/enviar-0978617_0_1654260978.png', 'enviar-0978617.png', 'enviar-0978617.png', '2022-06-03 12:56:18', '2022-06-03 12:56:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `groups`
--

INSERT INTO `groups` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'CIDESO', NULL, NULL),
(2, 'CPS', NULL, NULL),
(3, 'CPS-CIDESO', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL,
  `conversation_id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `message_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message_type`, `message_id`, `created_at`, `updated_at`) VALUES
(1, 4, 2, 'App\\Models\\TextMessage', 1, '2022-05-30 14:20:47', '2022-05-30 14:20:47'),
(2, 2, 2, 'App\\Models\\FileMessage', 1, '2022-05-30 14:39:08', '2022-05-30 14:39:08'),
(3, 1, 3, 'App\\Models\\TextMessage', 2, '2022-05-31 20:49:30', '2022-05-31 20:49:30'),
(4, 1, 1, 'App\\Models\\TextMessage', 3, '2022-05-31 20:52:03', '2022-05-31 20:52:03'),
(5, 1, 1, 'App\\Models\\FileMessage', 2, '2022-05-31 21:01:38', '2022-05-31 21:01:38'),
(6, 1, 3, 'App\\Models\\FileMessage', 3, '2022-05-31 21:11:49', '2022-05-31 21:11:49'),
(7, 4, 3, 'App\\Models\\TextMessage', 4, '2022-05-31 21:12:31', '2022-05-31 21:12:31'),
(8, 4, 2, 'App\\Models\\FileMessage', 4, '2022-06-02 13:20:52', '2022-06-02 13:20:52'),
(9, 4, 2, 'App\\Models\\FileMessage', 5, '2022-06-02 13:21:15', '2022-06-02 13:21:15'),
(10, 4, 2, 'App\\Models\\FileMessage', 6, '2022-06-02 13:21:20', '2022-06-02 13:21:20'),
(11, 1, 2, 'App\\Models\\FileMessage', 7, '2022-06-02 13:21:27', '2022-06-02 13:21:27'),
(12, 1, 2, 'App\\Models\\TextMessage', 5, '2022-06-02 13:21:36', '2022-06-02 13:21:36'),
(13, 1, 2, 'App\\Models\\FileMessage', 8, '2022-06-02 13:21:43', '2022-06-02 13:21:43'),
(14, 3, 1, 'App\\Models\\FileMessage', 9, '2022-06-02 13:51:07', '2022-06-02 13:51:07'),
(15, 3, 3, 'App\\Models\\TextMessage', 6, '2022-06-03 12:44:11', '2022-06-03 12:44:11'),
(16, 3, 3, 'App\\Models\\TextMessage', 7, '2022-06-03 12:45:31', '2022-06-03 12:45:31'),
(17, 3, 3, 'App\\Models\\FileMessage', 10, '2022-06-03 12:52:20', '2022-06-03 12:52:20'),
(18, 4, 3, 'App\\Models\\FileMessage', 11, '2022-06-03 12:54:49', '2022-06-03 12:54:49'),
(19, 4, 3, 'App\\Models\\TextMessage', 8, '2022-06-03 12:55:08', '2022-06-03 12:55:08'),
(20, 4, 2, 'App\\Models\\TextMessage', 9, '2022-06-03 12:55:30', '2022-06-03 12:55:30'),
(21, 4, 2, 'App\\Models\\TextMessage', 10, '2022-06-03 12:55:43', '2022-06-03 12:55:43'),
(22, 4, 2, 'App\\Models\\TextMessage', 11, '2022-06-03 12:55:48', '2022-06-03 12:55:48'),
(23, 4, 2, 'App\\Models\\TextMessage', 12, '2022-06-03 12:55:51', '2022-06-03 12:55:51'),
(24, 4, 2, 'App\\Models\\FileMessage', 12, '2022-06-03 12:55:56', '2022-06-03 12:55:56'),
(25, 4, 2, 'App\\Models\\FileMessage', 13, '2022-06-03 12:56:11', '2022-06-03 12:56:11'),
(26, 4, 2, 'App\\Models\\FileMessage', 14, '2022-06-03 12:56:18', '2022-06-03 12:56:18'),
(27, 4, 2, 'App\\Models\\TextMessage', 13, '2022-06-03 12:56:19', '2022-06-03 12:56:19'),
(28, 4, 2, 'App\\Models\\TextMessage', 14, '2022-06-03 12:56:34', '2022-06-03 12:56:34'),
(29, 4, 2, 'App\\Models\\TextMessage', 15, '2022-06-03 12:56:41', '2022-06-03 12:56:41'),
(30, 4, 3, 'App\\Models\\TextMessage', 16, '2022-06-03 12:56:43', '2022-06-03 12:56:43'),
(31, 4, 2, 'App\\Models\\TextMessage', 17, '2022-06-03 12:56:53', '2022-06-03 12:56:53'),
(32, 4, 2, 'App\\Models\\TextMessage', 18, '2022-06-03 12:56:58', '2022-06-03 12:56:58'),
(33, 4, 2, 'App\\Models\\TextMessage', 19, '2022-06-03 12:57:04', '2022-06-03 12:57:04'),
(34, 4, 3, 'App\\Models\\TextMessage', 20, '2022-06-03 12:57:14', '2022-06-03 12:57:14'),
(35, 4, 3, 'App\\Models\\TextMessage', 21, '2022-06-03 12:57:36', '2022-06-03 12:57:36'),
(36, 4, 2, 'App\\Models\\TextMessage', 22, '2022-06-03 12:57:50', '2022-06-03 12:57:50'),
(37, 4, 2, 'App\\Models\\TextMessage', 23, '2022-06-03 12:58:23', '2022-06-03 12:58:23'),
(38, 4, 2, 'App\\Models\\TextMessage', 24, '2022-06-03 12:58:40', '2022-06-03 12:58:40'),
(39, 4, 2, 'App\\Models\\TextMessage', 25, '2022-06-03 12:58:51', '2022-06-03 12:58:51'),
(40, 4, 2, 'App\\Models\\TextMessage', 26, '2022-06-03 13:00:25', '2022-06-03 13:00:25'),
(41, 4, 3, 'App\\Models\\TextMessage', 27, '2022-06-03 13:00:36', '2022-06-03 13:00:36'),
(42, 4, 2, 'App\\Models\\TextMessage', 28, '2022-06-03 13:04:51', '2022-06-03 13:04:51'),
(43, 4, 2, 'App\\Models\\TextMessage', 29, '2022-06-03 13:05:01', '2022-06-03 13:05:01'),
(44, 4, 2, 'App\\Models\\TextMessage', 30, '2022-06-03 13:05:11', '2022-06-03 13:05:11'),
(45, 4, 3, 'App\\Models\\TextMessage', 31, '2022-06-03 13:05:42', '2022-06-03 13:05:42'),
(46, 3, 1, 'App\\Models\\TextMessage', 32, '2022-06-06 11:28:59', '2022-06-06 11:28:59'),
(47, 4, 2, 'App\\Models\\TextMessage', 33, '2022-06-06 13:53:03', '2022-06-06 13:53:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `message_visualizations`
--

DROP TABLE IF EXISTS `message_visualizations`;
CREATE TABLE `message_visualizations` (
  `id` bigint UNSIGNED NOT NULL,
  `message_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0000_00_00_000000_create_websockets_statistics_entries_table', 1),
(2, '2014_10_12_000000_create_users_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2020_05_05_153157_create_groups_table', 1),
(6, '2020_06_18_175818_create_conversations_table', 1),
(7, '2020_06_19_134547_create_messages_table', 1),
(8, '2020_08_21_182649_create_file_messages_table', 1),
(9, '2020_08_21_182710_create_text_messages_table', 1),
(10, '2021_06_09_132504_create_position_messages_table', 1),
(11, '2022_05_05_153257_create_user_contacts_table', 1),
(12, '2022_05_05_153329_create_message_visualizations_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `position_messages`
--

DROP TABLE IF EXISTS `position_messages`;
CREATE TABLE `position_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `lat` decimal(10,6) NOT NULL,
  `lon` decimal(10,6) NOT NULL,
  `alt` decimal(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `text_messages`
--

DROP TABLE IF EXISTS `text_messages`;
CREATE TABLE `text_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `text_messages`
--

INSERT INTO `text_messages` (`id`, `text`) VALUES
(1, 'hola'),
(2, 'probando grupo'),
(3, 'recibido desde Vale!'),
(4, 'hola, Javi'),
(5, 'pruebo archivos'),
(6, 'hola'),
(7, 'test?'),
(8, 'test'),
(9, 'dsghsfrht'),
(10, 'srtudeywae'),
(11, 'a'),
(12, 'a'),
(13, 'q000000'),
(14, 'q000000'),
(15, 'q0001'),
(16, 'q0000'),
(17, 'q00000'),
(18, 'q0000'),
(19, 'q000000'),
(20, 'check'),
(21, 'check2'),
(22, 'seirjhea'),
(23, '#00000'),
(24, '#ffffffff'),
(25, 'q0000'),
(26, 'q'),
(27, 'que mal anda eso...'),
(28, 'qqqq'),
(29, 'otro'),
(30, 'ahora anda mejor?'),
(31, 'si, anda bien despues de limpiar cache de firefox'),
(32, 'Hola Paul! Vale desde el CIDESO'),
(33, 'prueba');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Valeria', '2022-05-30 09:02:43', '2022-05-30 09:02:43'),
(2, 'Javi', '2022-05-30 09:02:43', '2022-05-30 09:02:43'),
(3, 'Paul', '2022-05-30 09:02:43', '2022-05-30 09:02:43'),
(4, 'Hernán', '2022-05-30 09:02:43', '2022-05-30 09:02:43'),
(5, 'Gabriel', '2022-05-30 09:02:43', '2022-05-30 09:02:43'),
(6, 'Brian', '2022-05-30 09:02:43', '2022-05-30 09:02:43'),
(7, 'Rubén', '2022-05-30 09:02:43', '2022-05-30 09:02:43'),
(8, 'Ramiro', '2022-05-30 09:02:43', '2022-05-30 09:02:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_contacts`
--

DROP TABLE IF EXISTS `user_contacts`;
CREATE TABLE `user_contacts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `contact_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_contacts`
--

INSERT INTO `user_contacts` (`id`, `user_id`, `contact_type`, `contact_id`, `created_at`, `updated_at`, `last_read_at`) VALUES
(1, 1, 'App\\User', 2, NULL, '2022-05-31 21:12:06', '2022-05-31 21:12:06'),
(2, 1, 'App\\User', 3, NULL, '2022-06-06 14:49:14', '2022-06-06 14:49:14'),
(3, 1, 'App\\Models\\Group', 1, NULL, '2022-06-02 13:45:57', '2022-06-02 13:45:57'),
(4, 1, 'App\\Models\\Group', 3, NULL, NULL, '0000-00-00 00:00:00'),
(5, 2, 'App\\User', 1, NULL, '2022-05-31 20:41:49', '2022-05-31 20:41:49'),
(6, 2, 'App\\User', 3, NULL, '2022-06-06 14:02:49', '2022-06-06 14:02:49'),
(7, 2, 'App\\Models\\Group', 1, NULL, '2022-06-03 12:56:05', '2022-06-03 12:56:05'),
(8, 2, 'App\\Models\\Group', 3, NULL, NULL, '0000-00-00 00:00:00'),
(9, 3, 'App\\User', 1, NULL, '2022-06-03 13:07:03', '2022-06-03 13:07:03'),
(10, 3, 'App\\User', 2, NULL, '2022-06-03 13:27:30', '2022-06-03 13:27:30'),
(11, 3, 'App\\Models\\Group', 1, NULL, '2022-06-03 12:53:09', '2022-06-03 12:53:09'),
(12, 3, 'App\\Models\\Group', 3, NULL, '2022-05-31 21:12:23', '2022-05-31 21:12:23'),
(13, 3, 'App\\User', 5, NULL, NULL, '0000-00-00 00:00:00'),
(14, 5, 'App\\User', 3, NULL, NULL, '0000-00-00 00:00:00'),
(15, 5, 'App\\User', 6, NULL, NULL, '0000-00-00 00:00:00'),
(16, 6, 'App\\User', 5, NULL, NULL, '0000-00-00 00:00:00'),
(17, 5, 'App\\Models\\Group', 2, NULL, NULL, '0000-00-00 00:00:00'),
(18, 5, 'App\\Models\\Group', 3, NULL, NULL, '0000-00-00 00:00:00'),
(19, 6, 'App\\Models\\Group', 2, NULL, NULL, '0000-00-00 00:00:00'),
(20, 6, 'App\\Models\\Group', 3, NULL, NULL, '0000-00-00 00:00:00'),
(21, 7, 'App\\User', 5, '2022-05-30 09:05:28', '2022-05-30 09:05:28', '0000-00-00 00:00:00'),
(22, 7, 'App\\User', 6, '2022-05-30 09:05:28', '2022-05-30 09:05:28', '0000-00-00 00:00:00'),
(23, 7, 'App\\Models\\Group', 3, '2022-05-30 09:06:44', '2022-05-30 09:06:44', '0000-00-00 00:00:00'),
(24, 7, 'App\\Models\\Group', 2, '2022-05-30 09:06:44', '2022-05-30 09:06:44', '0000-00-00 00:00:00'),
(25, 5, 'App\\User', 7, '2022-05-30 09:09:16', '2022-05-30 09:09:16', '0000-00-00 00:00:00'),
(26, 6, 'App\\User', 7, '2022-05-30 09:09:16', '2022-05-30 09:09:16', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `websockets_statistics_entries`
--

DROP TABLE IF EXISTS `websockets_statistics_entries`;
CREATE TABLE `websockets_statistics_entries` (
  `id` int UNSIGNED NOT NULL,
  `app_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `peak_connection_count` int NOT NULL,
  `websocket_message_count` int NOT NULL,
  `api_message_count` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversations_user_id_1_foreign` (`user_id_1`),
  ADD KEY `conversations_user_id_2_foreign` (`user_id_2`),
  ADD KEY `conversations_group_id_foreign` (`group_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `file_messages`
--
ALTER TABLE `file_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_conversation_id_foreign` (`conversation_id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_message_type_message_id_index` (`message_type`,`message_id`);

--
-- Indices de la tabla `message_visualizations`
--
ALTER TABLE `message_visualizations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_visualizations_message_id_foreign` (`message_id`),
  ADD KEY `message_visualizations_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `position_messages`
--
ALTER TABLE `position_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `text_messages`
--
ALTER TABLE `text_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user_contacts`
--
ALTER TABLE `user_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_contacts_user_id_foreign` (`user_id`),
  ADD KEY `user_contacts_contact_type_contact_id_index` (`contact_type`,`contact_id`);

--
-- Indices de la tabla `websockets_statistics_entries`
--
ALTER TABLE `websockets_statistics_entries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `file_messages`
--
ALTER TABLE `file_messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `message_visualizations`
--
ALTER TABLE `message_visualizations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `position_messages`
--
ALTER TABLE `position_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `text_messages`
--
ALTER TABLE `text_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `user_contacts`
--
ALTER TABLE `user_contacts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `websockets_statistics_entries`
--
ALTER TABLE `websockets_statistics_entries`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `conversations_user_id_1_foreign` FOREIGN KEY (`user_id_1`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `conversations_user_id_2_foreign` FOREIGN KEY (`user_id_2`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`),
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `message_visualizations`
--
ALTER TABLE `message_visualizations`
  ADD CONSTRAINT `message_visualizations_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`),
  ADD CONSTRAINT `message_visualizations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `user_contacts`
--
ALTER TABLE `user_contacts`
  ADD CONSTRAINT `user_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
