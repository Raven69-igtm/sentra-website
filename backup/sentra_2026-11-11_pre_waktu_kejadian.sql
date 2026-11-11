-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sentra
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `akun_id` bigint unsigned DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `instansi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'berhasil',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_instansi_created_at_index` (`instansi`,`created_at`),
  KEY `activity_logs_akun_id_index` (`akun_id`),
  KEY `activity_logs_activity_type_index` (`activity_type`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,17,'Superadmin Sentra','superadmin','Superadmin','data','akun','akun-16','Memperbarui data akun \'amba\' (contactabiyyuza@gmail.com, role admin_dinsos).','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 02:46:56','2026-11-09 02:46:56'),(2,17,'Superadmin Sentra','superadmin','Superadmin','logout',NULL,NULL,'Logout dari panel sistem.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 03:42:16','2026-11-09 03:42:16'),(3,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 03:42:27','2026-11-09 03:42:27'),(4,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','logout',NULL,NULL,'Logout dari panel sistem.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 03:42:58','2026-11-09 03:42:58'),(5,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 03:43:04','2026-11-09 03:43:04'),(6,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','logout',NULL,NULL,'Logout dari panel sistem.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 03:43:10','2026-11-09 03:43:10'),(7,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 03:43:19','2026-11-09 03:43:19'),(8,17,'Superadmin Sentra','superadmin','Superadmin','logout',NULL,NULL,'Logout dari panel sistem.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 04:40:33','2026-11-09 04:40:33'),(9,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 04:40:45','2026-11-09 04:40:45'),(10,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) QoderIDE/1.39.1 Chrome/148.0.7778.280 Electron/42.11.18 Safari/537.36','2026-11-09 04:45:41','2026-11-09 04:45:41'),(11,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 05:07:43','2026-11-09 05:07:43'),(12,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 05:08:02','2026-11-09 05:08:02'),(13,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 05:08:36','2026-11-09 05:08:36'),(14,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 05:08:45','2026-11-09 05:08:45'),(15,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 05:09:02','2026-11-09 05:09:02'),(16,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 05:09:21','2026-11-09 05:09:21'),(17,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 05:09:40','2026-11-09 05:09:40'),(18,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:38:09','2026-11-09 07:38:09'),(19,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:39:16','2026-11-09 07:39:16'),(20,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:39:26','2026-11-09 07:39:26'),(21,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:39:33','2026-11-09 07:39:33'),(22,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:46:51','2026-11-09 07:46:51'),(23,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:47:14','2026-11-09 07:47:14'),(24,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:48:48','2026-11-09 07:48:48'),(25,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:49:10','2026-11-09 07:49:10'),(26,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:49:45','2026-11-09 07:49:45'),(27,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:50:09','2026-11-09 07:50:09'),(28,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:50:31','2026-11-09 07:50:31'),(29,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:50:53','2026-11-09 07:50:53'),(30,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:51:19','2026-11-09 07:51:19'),(31,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:52:53','2026-11-09 07:52:53'),(32,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:54:27','2026-11-09 07:54:27'),(33,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:54:37','2026-11-09 07:54:37'),(34,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:55:16','2026-11-09 07:55:16'),(35,NULL,'Tidak Dikenal','tidak_dikenal','Tidak Dikenal','login',NULL,NULL,'Percobaan login gagal: email atau password salah (email: adminppa@sentra.test).','gagal','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:55:52','2026-11-09 07:55:52'),(36,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:56:08','2026-11-09 07:56:08'),(37,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:56:59','2026-11-09 07:56:59'),(38,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:57:21','2026-11-09 07:57:21'),(39,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:58:01','2026-11-09 07:58:01'),(40,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 07:59:21','2026-11-09 07:59:21'),(41,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:00:45','2026-11-09 08:00:45'),(42,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:00:54','2026-11-09 08:00:54'),(43,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:01:40','2026-11-09 08:01:40'),(44,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:02:24','2026-11-09 08:02:24'),(45,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:02:51','2026-11-09 08:02:51'),(46,17,'Superadmin Sentra','superadmin','Superadmin','logout',NULL,NULL,'Logout dari panel sistem.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 08:03:06','2026-11-09 08:03:06'),(47,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:03:20','2026-11-09 08:03:20'),(48,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 08:03:23','2026-11-09 08:03:23'),(49,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:03:52','2026-11-09 08:03:52'),(50,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:04:37','2026-11-09 08:04:37'),(51,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:05:39','2026-11-09 08:05:39'),(52,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:05:53','2026-11-09 08:05:53'),(53,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:06:36','2026-11-09 08:06:36'),(54,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:06:44','2026-11-09 08:06:44'),(55,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:07:47','2026-11-09 08:07:47'),(56,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:08:42','2026-11-09 08:08:42'),(57,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:27:26','2026-11-09 08:27:26'),(58,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:28:07','2026-11-09 08:28:07'),(59,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:28:35','2026-11-09 08:28:35'),(60,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:29:23','2026-11-09 08:29:23'),(61,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:29:52','2026-11-09 08:29:52'),(62,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:30:26','2026-11-09 08:30:26'),(63,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:32:18','2026-11-09 08:32:18'),(64,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:32:55','2026-11-09 08:32:55'),(65,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:35:00','2026-11-09 08:35:00'),(66,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:55:24','2026-11-09 08:55:24'),(67,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/107.0.5296.0 Safari/537.36','2026-11-09 08:55:35','2026-11-09 08:55:35'),(68,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','logout',NULL,NULL,'Logout dari panel sistem.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:02:38','2026-11-09 09:02:38'),(69,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:03:15','2026-11-09 09:03:15'),(70,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','logout',NULL,NULL,'Logout dari panel sistem.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:19:00','2026-11-09 09:19:00'),(71,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:19:12','2026-11-09 09:19:12'),(72,17,'Superadmin Sentra','superadmin','Superadmin','data','setting',NULL,'Memperbarui konfigurasi sistem (SENTRA).','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:20:59','2026-11-09 09:20:59'),(73,17,'Superadmin Sentra','superadmin','Superadmin','data','setting',NULL,'Memperbarui konfigurasi sistem (SENTRA).','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:21:15','2026-11-09 09:21:15'),(74,17,'Superadmin Sentra','superadmin','Superadmin','logout',NULL,NULL,'Logout dari panel sistem.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:21:22','2026-11-09 09:21:22'),(75,17,'Superadmin Sentra','superadmin','Superadmin','login',NULL,NULL,'Login berhasil ke panel Superadmin.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:21:35','2026-11-09 09:21:35'),(76,17,'Superadmin Sentra','superadmin','Superadmin','data','setting',NULL,'Memperbarui konfigurasi sistem (SENTRA).','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:21:59','2026-11-09 09:21:59'),(77,17,'Superadmin Sentra','superadmin','Superadmin','logout',NULL,NULL,'Logout dari panel sistem.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:24:48','2026-11-09 09:24:48'),(78,18,'Admin Dinsos','admin_dinsos','Dinas Sosial','login',NULL,NULL,'Login berhasil ke panel Dinas Sosial.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-09 09:24:58','2026-11-09 09:24:58'),(79,NULL,'Tidak Dikenal','tidak_dikenal','Tidak Dikenal','login',NULL,NULL,'Percobaan login gagal: email atau password salah (email: sentraapplication@gmail.com).','gagal','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','2026-11-10 17:03:22','2026-11-10 17:03:22'),(80,NULL,'Tidak Dikenal','tidak_dikenal','Tidak Dikenal','login',NULL,NULL,'Percobaan login gagal: email atau password salah (email: sentraapplication@gmail.com).','gagal','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','2026-11-10 17:03:30','2026-11-10 17:03:30'),(81,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','curl/8.23.0','2026-11-10 17:07:31','2026-11-10 17:07:31'),(82,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','login',NULL,NULL,'Login berhasil ke panel UPTD PPA.','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','2026-11-10 17:10:01','2026-11-10 17:10:01'),(83,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','data','informasi',NULL,'Menghapus informasi publik \"amba\".','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','2026-11-10 17:10:31','2026-11-10 17:10:31'),(84,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','dokumen','informasi','josjis-1794355861.jpg','Mengunggah gambar informasi \"JOSJIS\" (josjis-1794355861.jpg).','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','2026-11-10 17:11:01','2026-11-10 17:11:01'),(85,15,'Admin PPA (Sentra)','admin_ppa','UPTD PPA','data','informasi',NULL,'Menambahkan informasi publik \"JOSJIS\".','berhasil','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','2026-11-10 17:11:01','2026-11-10 17:11:01');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `akun`
--

DROP TABLE IF EXISTS `akun`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `akun` (
  `id_akun` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notelp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_kelamin` enum('Pria','Wanita') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'guest',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `emerquest` enum('Apa Warna Favorit anda ?','Apa Hewan Favorit anda ?','Dimana anda Lahir ?') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answquest` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `fcm_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_akun`),
  UNIQUE KEY `akun_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `akun`
--

LOCK TABLES `akun` WRITE;
/*!40000 ALTER TABLE `akun` DISABLE KEYS */;
INSERT INTO `akun` VALUES (1,'085247490726','aripJMK48','arip@gmali.com','Pria','Ngringin, 64393, Lengkong, Nganjuk, East Java, Indonesia','$2y$12$pUdBxtKVXiT6utFiHjlqV.HD2qTbrR/Le5xuwSAtg66FrDRqrvEte','user',1,NULL,NULL,NULL,NULL,NULL,'2026-09-03 03:08:52','2026-09-24 12:49:43'),(2,'081234567890','User Mobile Tester','user.mobile@test.com',NULL,NULL,'secret','user',1,NULL,NULL,NULL,NULL,NULL,'2026-09-24 09:39:07','2026-09-24 09:39:07'),(14,'081234567814','User Mobile 14','user14@example.com',NULL,NULL,'secret','user',1,NULL,NULL,NULL,NULL,NULL,'2026-09-24 11:16:21','2026-09-24 11:16:21'),(15,'081234567890','Admin PPA (Sentra)','admin.ppa@sentra.com',NULL,NULL,'$2y$12$GSJOMLDdIyh0a86qjf6eQuPFPEFI94Xf4w..pk.UEgiSYqX/skp2W','admin_ppa',1,NULL,NULL,NULL,NULL,NULL,'2026-09-24 05:50:53','2026-11-10 17:07:20'),(16,'eyJpdiI6Im5yQVhjSW5HQlExM1ZrMTlrazFFSHc9PSIsInZhbHVlIjoiQXROTW80SU54MmRJcnVOLzI0eHVOUT09IiwibWFjIjoiODY2N2M2OWY3M2MxNjI0YTU0NmE5YzQ3NDExNTdjZTZmZDlkNWZhOTExZjRlZjk3NDllN2QwYjAzNTg5Zjk3MiIsInRhZyI6IiJ9','amba','contactabiyyuza@gmail.com','Pria','Ngringin, 64393, Lengkong, Nganjuk, East Java, Indonesia','$2y$12$tmD/bk10.Bjp8r/NNeQiBuRXS.iAW8C9FjofUJvLamQLe370W2V4G','admin_dinsos',1,'Apa Hewan Favorit anda ?','asu','236085','2026-09-24 13:59:56',NULL,'2026-09-24 06:54:56','2026-11-09 02:46:56'),(17,'eyJpdiI6Indlb091WkpkcFFnSU8zZXNGUkYyTnc9PSIsInZhbHVlIjoiNXdCWHFsZFBWNHgvcmFDbkZYTHl3QT09IiwibWFjIjoiMGJlMjQyNmJjNTE3MGFhYWYxMGMzMjI5NGRiMmQ5OTY0YmJmZjQ4NzkwOGY0MGQ4NWIxMDk4ZTVmZDAyNGQyNiIsInRhZyI6IiJ9','Superadmin Sentra','superadmin@sentra.com',NULL,NULL,'$2y$12$F96oEOn4/bUEqUshI7zAvurPECapbL16lsAZ6V9tPv4M7kmApZ03e','superadmin',1,NULL,NULL,NULL,NULL,NULL,'2026-11-01 22:35:33','2026-11-01 22:35:33'),(18,'eyJpdiI6IjhCRDFhRndaL254THhTSmcxSCtBdHc9PSIsInZhbHVlIjoieDVMRG5lTTMyRnVTcEhlaVNFMUYrZz09IiwibWFjIjoiNWYzMWE3NWZiZWExOTU0Y2VjNmExNzAwZGYwZmU3Njc3ODhjZDZkMGRiMDY3YzY5Mjc3ZWM4NDZiNzU4ODI4MCIsInRhZyI6IiJ9','Admin Dinsos','admin.dinsos@sentra.com',NULL,NULL,'$2y$12$.CucJnBD8vRko7TlntCPqOFbdlqNv/ioqEuEcFTEXbRYrH4Gwb/bq','admin_dinsos',1,NULL,NULL,NULL,NULL,NULL,'2026-11-01 23:34:25','2026-11-01 23:34:25'),(19,'081200000001','E2E Mobile Test',NULL,'Wanita','Jl. Test Updated','$2y$12$Q8XYNL/TVN9ENb7947H22eN/YibRKDQ5/l5AlMLKFFjH2URvEFzUy','user',1,'Apa Warna Favorit anda ?','biru','886553','2026-11-10 23:04:59',NULL,'2026-11-10 15:59:59','2026-11-10 16:01:03'),(20,'081200000099','E2E Register Test',NULL,NULL,NULL,'$2y$12$7obdiIS520j001SYXuQVauGK7IvVnl4L8NMWuMSZAybB74sk9zlPG','user',1,NULL,NULL,'978489','2026-11-10 23:55:23',NULL,'2026-11-10 16:50:23','2026-11-10 16:50:23'),(21,'081528821611','Arieff',NULL,'Pria','Nganjuk, East Java, Indonesia','$2y$12$DwNcq16VIxgKq40P.StDPOc3RPkz3.O3hI5ocH0e9GX2c9Apu3r0G','user',1,'Apa Hewan Favorit anda ?','kucing','403132','2026-11-10 23:59:17',NULL,'2026-11-10 16:54:17','2026-11-10 16:54:17');
/*!40000 ALTER TABLE `akun` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel_cache_adminppa@sentra.test|127.0.0.1','i:1;',1794236212),('laravel_cache_adminppa@sentra.test|127.0.0.1:timer','i:1794236212;',1794236212),('laravel_cache_sentraapplication@gmail.com|127.0.0.1','i:2;',1794355462),('laravel_cache_sentraapplication@gmail.com|127.0.0.1:timer','i:1794355462;',1794355462),('laravel_cache_system_setting_app_description','s:87:\"Sistem Informasi dan Pelayanan Terpadu Penanganan Kekerasan terhadap Perempuan dan Anak\";',2109601319),('laravel_cache_system_setting_app_logo','N;',2109719259),('laravel_cache_system_setting_app_name','s:6:\"SENTRA\";',2109601319),('laravel_cache_system_setting_auto_archive_months','s:2:\"24\";',2109601319),('laravel_cache_system_setting_emergency_call','s:9:\"112 / 129\";',2109601319),('laravel_cache_system_setting_enable_chat_consultation','s:1:\"1\";',2109601319),('laravel_cache_system_setting_footer_copyright','s:36:\"© 2026 SENTRA. All rights reserved.\";',2109601319),('laravel_cache_system_setting_hotline_number','s:14:\"0812-3456-7890\";',2109601319),('laravel_cache_system_setting_instansi_name','s:50:\"Dinas Pemberdayaan Perempuan dan Perlindungan Anak\";',2109601319),('laravel_cache_system_setting_max_login_attempts','s:1:\"5\";',2109601319),('laravel_cache_system_setting_office_address','s:50:\"Jl. Pemuda No. 45, Kompleks Perkantoran Pemerintah\";',2109601319),('laravel_cache_system_setting_office_email','s:27:\"sentraapplication@gmail.com\";',2109601319),('laravel_cache_system_setting_otp_expiry_minutes','s:1:\"5\";',2109601319),('laravel_cache_system_setting_report_sla_days','s:1:\"3\";',2109601319),('laravel_cache_system_setting_session_lifetime_minutes','s:3:\"120\";',2109601319),('laravel_cache_system_setting_test_key','s:10:\"test_value\";',2109568885);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chats` (
  `id_chat` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint unsigned NOT NULL,
  `receiver_id` bigint unsigned NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `is_notified` tinyint(1) NOT NULL DEFAULT '0',
  `replied_to_id` bigint unsigned DEFAULT NULL,
  `created_at` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_chat`),
  KEY `chats_sender_id_foreign` (`sender_id`),
  KEY `chats_receiver_id_foreign` (`receiver_id`),
  KEY `chats_replied_to_id_foreign` (`replied_to_id`),
  CONSTRAINT `chats_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `akun` (`id_akun`) ON DELETE CASCADE,
  CONSTRAINT `chats_replied_to_id_foreign` FOREIGN KEY (`replied_to_id`) REFERENCES `chats` (`id_chat`) ON DELETE SET NULL,
  CONSTRAINT `chats_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `akun` (`id_akun`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chats`
--

LOCK TABLES `chats` WRITE;
/*!40000 ALTER TABLE `chats` DISABLE KEYS */;
INSERT INTO `chats` VALUES (1,2,15,'Halo Admin, saya butuh konsultasi dari aplikasi mobile!','2026-09-24 09:44:43',1,0,NULL,NULL,'2026-09-24 09:56:52'),(2,15,2,'Halo, saya Admin Sentra. Ada yang bisa kami bantu?','2026-09-24 09:44:43',0,0,NULL,NULL,NULL),(3,15,2,'MANANANA','2026-09-24 10:04:46',0,0,NULL,NULL,NULL),(4,14,15,'Halo Admin, ini tes dari User 14 di local','2026-09-24 11:28:10',1,0,NULL,NULL,'2026-09-24 12:26:56'),(9,2,15,'Halo Admin, saya dari aplikasi mobile','2026-09-24 05:25:52',1,1,NULL,NULL,NULL),(10,15,2,'Halo, ada yang bisa kami bantu?','2026-09-24 05:26:52',1,1,NULL,NULL,NULL),(11,2,15,'Saya ingin konsultasi tentang anak','2026-09-24 05:30:52',1,1,NULL,NULL,NULL),(12,14,15,'Permisi admin','2026-09-24 05:32:52',1,0,NULL,NULL,'2026-09-24 13:01:25'),(16,1,1,'boss','2026-09-24 13:01:36',0,0,NULL,NULL,NULL),(17,1,15,'Halo Admin, saya aripJMK48 dari mobile','2026-09-24 06:05:04',1,0,NULL,NULL,'2026-09-25 01:18:31'),(18,16,15,'tes','2026-09-24 13:55:21',1,0,NULL,NULL,'2026-09-24 13:55:35'),(19,16,15,'tes','2026-09-24 13:55:28',1,0,NULL,NULL,'2026-09-24 13:55:35'),(20,15,16,'ya ada apa','2026-09-24 13:55:44',0,0,NULL,NULL,NULL),(21,15,16,'ya ada apa','2026-09-24 13:55:51',0,0,NULL,NULL,NULL),(22,16,15,'halo','2026-09-24 15:35:05',1,0,NULL,NULL,'2026-09-24 15:35:33'),(23,16,15,'kontol','2026-09-24 15:36:26',1,0,NULL,NULL,'2026-09-24 16:27:56'),(24,15,16,'ya','2026-09-24 15:36:34',0,0,NULL,NULL,NULL),(25,15,1,'halo jomokkkkersssss','2026-09-25 01:19:02',0,0,NULL,NULL,NULL),(26,16,15,'woii','2026-09-25 01:21:47',1,0,NULL,NULL,'2026-09-25 01:22:13'),(27,19,15,'Halo admin, ini uji integrasi chat','2026-11-10 23:01:09',1,0,NULL,'2026-11-10 23:01:09','2026-11-11 01:05:23'),(28,19,15,'Pesan E2E dari aplikasi mobile','2026-11-10 23:46:19',1,0,NULL,'2026-11-10 23:46:19','2026-11-11 01:05:23'),(29,21,15,'adminn','2026-11-10 23:58:07',1,0,NULL,'2026-11-10 23:58:07','2026-11-11 00:15:39'),(30,21,15,'MIN apa kabar','2026-11-11 01:07:02',1,0,NULL,'2026-11-11 01:07:02','2026-11-11 01:07:38');
/*!40000 ALTER TABLE `chats` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_chat_insert` AFTER INSERT ON `chats` FOR EACH ROW BEGIN
                INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                VALUES (
                    NEW.receiver_id,
                    "Pesan Baru",
                    CONCAT("Anda menerima pesan baru dari ", 
                           (SELECT nama FROM akun WHERE id_akun = NEW.sender_id), 
                           ": ", NEW.message),
                    "user",
                    "terkirim",
                    NOW()
                );
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `detail_kasus`
--

DROP TABLE IF EXISTS `detail_kasus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_kasus` (
  `id_kasus` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_laporan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date DEFAULT NULL,
  `tempat_kejadian` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kronologi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_kasus`),
  KEY `detail_kasus_id_laporan_foreign` (`id_laporan`),
  CONSTRAINT `detail_kasus_id_laporan_foreign` FOREIGN KEY (`id_laporan`) REFERENCES `laporan` (`id_laporan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_kasus`
--

LOCK TABLES `detail_kasus` WRITE;
/*!40000 ALTER TABLE `detail_kasus` DISABLE KEYS */;
INSERT INTO `detail_kasus` VALUES (1,'RKN266786732','2026-09-24','Jalan Melati No 5','Ini adalah kronologi kejadian pengaduan dari pengujian sistem.'),(2,'RKN267238640','2026-09-23','Kos Kurnia','anjing saya melakukan kekerasan'),(3,'RKN267729809','2026-09-24','Jalan Mawar No 10','Ini adalah laporan uji coba untuk verifikasi tampilan di web admin.'),(4,'RKN268147997','2026-09-11','Wew','weqwnb'),(5,'RKN268765103','2026-09-24','Lokasi Test','Kronologi pengujian otomatis'),(6,'RKN268791933','2026-09-24','Lokasi Test','Kronologi test'),(7,'RKN269075940','2026-09-24','Rumah Pribadi Nganjuk','Laporan kekerasan dalam rumah tangga dari Siti Rahayu.'),(8,'RKN270032372','2026-09-16','Ffs','qwq'),(10,'RKN351626310','2026-11-11','Nganjuk','Uji integrasi end-to-end mobile'),(11,'RKN352377863','2026-11-11','Nganjuk','Uji kontrak payload integrasi Flutter.'),(12,'RKN354029237','2026-11-11','Rumah Korban, Jl. E2E Test No. 1, Nganjuk','Pada 10 November 2026 pukul 10.00 WIB, terjadi dugaan kekerasan verbal terhadap anak di rumah korban. Saksi melihat kejadian tersebut.'),(13,'RKN354938754','2026-11-11','Jalan Jendral Gatot Subroto, Nganjuk, Jawa Timur','di keroyok pasukan  s'),(14,'RKN356101984','2026-11-11','Jalan Jendral Gatot Subroto, Nganjuk, Jawa Timur','di keroyok PSHT DI A YANI');
/*!40000 ALTER TABLE `detail_kasus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_pelapor`
--

DROP TABLE IF EXISTS `detail_pelapor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_pelapor` (
  `id_pelapor` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_laporan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hubungan_dengan_korban` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_pelapor`),
  KEY `detail_pelapor_id_laporan_foreign` (`id_laporan`),
  CONSTRAINT `detail_pelapor_id_laporan_foreign` FOREIGN KEY (`id_laporan`) REFERENCES `laporan` (`id_laporan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_pelapor`
--

LOCK TABLES `detail_pelapor` WRITE;
/*!40000 ALTER TABLE `detail_pelapor` DISABLE KEYS */;
INSERT INTO `detail_pelapor` VALUES (8,'RKN266786732','eyJpdiI6IlJhVTVraU1oUWtmMTBJVkdNMlRPTXc9PSIsInZhbHVlIjoieWUvM25yQWRKa0NtWklVRGVWUENZOURFMVByYUdWOGg1UENCb0gyZm9Zaz0iLCJtYWMiOiI2ZWI5OTNkNmI4Y2ZkM2Q1ZGZiYjZkYmE3OTcxNWZjNmY0MzU0ODAwYjRmMjc0ZjEzMmU2OGQ5OWFlYTkxNTllIiwidGFnIjoiIn0=','Budi Santoso','Nganjuk','Pelapor / Korban','eyJpdiI6IkgvQ0FpMXJSVXVHRTlkU2crUW94Smc9PSIsInZhbHVlIjoic21FY2ZHY2hHaXZsVEpQR3MvVnFHQT09IiwibWFjIjoiZmEwMTY3MDNmZmUxMGM5Zjc0ZmNkNzg1NzI1MGUwMGNmYmFkODRlNDhlNDE3YzA5N2VlMzczNzAwOWVmNjU0MSIsInRhZyI6IiJ9'),(9,'RKN267238640','eyJpdiI6Ii9kYzlpaWhoVUpBelc4c3Iza1h6TVE9PSIsInZhbHVlIjoicXRYU3lSSGY2cFFSVEpvNkM4SDFpeUlETllOR25SUndDY1grTERTb0t0WT0iLCJtYWMiOiJlZTM2Yzc2MzU5NTljMjUzNjk2MDQyOTQ4MjdhNjBiNGE1NDBlMTNjZTBjZDA1YWNiOTZmNmI4OWFmNzRiMWY4IiwidGFnIjoiIn0=','Amba','Ngringin, 64393, Lengkong, Nganjuk, East Java, Indonesia','Pelapor / Korban','eyJpdiI6IjZ2VjZ2VGZ1NklBT0NkWEhmRE9peGc9PSIsInZhbHVlIjoiNm54ODdIWmloVW44cTJGVmlTQ2ZFZz09IiwibWFjIjoiYzU5ODM3ODZjNjIyYjY0Njc4Y2E1ZWY1MmE0MzM2N2VmMjEzNTNhMTU0ZDM2MDlkYjI5MTNmOGVlM2RhYjlhOSIsInRhZyI6IiJ9'),(10,'RKN267729809','eyJpdiI6IjBhSUxNZXVkUlJCcXV3dU5XVWFBbUE9PSIsInZhbHVlIjoidVRPckxLYTBRdVQyTlFBenZaNEVtcVFicnd6ZUZCWDQ5cmJjeU5BdDEwYz0iLCJtYWMiOiJiZGFjNWEwMDUxMzBiZjc3YTczYjY5N2U2ZTRjZTkwMDEwNTQ2ODMzYzU0NDRlNDZhYmVjZDg3ZjJmMWM0NDAzIiwidGFnIjoiIn0=','Test Laporan Baru','Nganjuk Test','Pelapor / Korban','eyJpdiI6Im03TWJhKzZCdy9mWWppVGRvZzZ6THc9PSIsInZhbHVlIjoiTWJHOUlmZ3ZuK2ZJclgrSnhaa2NpUT09IiwibWFjIjoiNmUyZmY0NjFlNGVlZjUxNGU0YWQyMGFlMzE2OGIyZjFkNGFhY2Y2NzBjMWVjMTFjZmUzNmFkYTJlOTA0ZTgxNiIsInRhZyI6IiJ9'),(11,'RKN268147997','eyJpdiI6IkZodTd3S1BPblpVRTdwQnBoTHpTMEE9PSIsInZhbHVlIjoiQldzZTN3ZzA4bXBHMVkrbTBsMDZVcFlmTUJjR1hoTzVsdGMvbFpFSTdIOD0iLCJtYWMiOiI1OTAzYjNiY2Y4MDYxMTQ0NzEzNmZmNDQ4NWVjNmQzN2FlYWEyYWE5MjhjNmExOGI1YjczZjVkYmYwZWU1OTdjIiwidGFnIjoiIn0=','Amba','Ngrimbi, 61474, Bareng, Jombang, East Java, Indonesia','Pelapor / Korban','eyJpdiI6Im9NcDFLV0NaYjViMytGdkNEMm41TFE9PSIsInZhbHVlIjoiN2VLNUFhbkV4YjFmSUJINDMzaWE1dz09IiwibWFjIjoiZjQ0ZmQ5MmI2MTE3NzYzMjU2Y2U3YjBlYzgwYTIxMzA4YjI0YTY3ZjFmZGJmMjY1ZjI0YTUzMDg5MDNiNDVmMSIsInRhZyI6IiJ9'),(12,'RKN268765103','eyJpdiI6Im5PR3VtenpiUVYxUEpXb2RxNlJrRkE9PSIsInZhbHVlIjoiY0JQR3F5UEdYRURtUXc5U3dEMkcySStrOU00b2lJZ2x2YzFZV3RJZWRuVT0iLCJtYWMiOiJlZGYwYTYzYTBlZDIwMmI0NjYxYmYzMzNhMTlhYjZiZDVkM2RkYzU5YjFhYjhmOWE1OGM1OWMxMTYzZWQ0ZDE0IiwidGFnIjoiIn0=','Test API','Nganjuk','Pelapor / Korban','eyJpdiI6Im9McjQzUnlrYjhnQ1p1bTdXZkl6aEE9PSIsInZhbHVlIjoiYks4VGtLZHhTcXdhSUZKUlo0aUJ2dz09IiwibWFjIjoiMDg5YWY0NTg0YjhhYjJkMzE3NzFiOTUwYTc0YzUzOGFjMjcxZThkZmZmNmI2YzIwNzI3OGE4OTYwNmQ1YzliYiIsInRhZyI6IiJ9'),(13,'RKN268791933','eyJpdiI6IjhYM1FWay8vMVRBd1hFczR1TGxmakE9PSIsInZhbHVlIjoianhsMkhsR1ZXaHRUWkRibmpLTkpLdz09IiwibWFjIjoiZjJiNDAyNTEzNTcyY2Y3OWU3NDFmMjFhZTk3MWUzY2IwMGU5Y2U5YjA1N2VmNTRiYjNhYmJjZjNhNDVmMzc3YSIsInRhZyI6IiJ9','Test Detail Terlapor','Nganjuk','Pelapor / Korban','eyJpdiI6Ik1TYklnRGhwNFFrREF0eDBtSys1dFE9PSIsInZhbHVlIjoieFRUUVhMK1QxMU1MNVJPVkVraWVZdz09IiwibWFjIjoiYjI5YmE5NmIyZjJjNjU1M2U2MDQ4ZjNiNDQ1ZmJmOTU4YTJhMzE3ODRjZWNkZmZlMWY5OTgxOTg4NGJjYWUxNSIsInRhZyI6IiJ9'),(14,'RKN269075940','eyJpdiI6InI4QWFlNVBXOEZFOTlaWm5VcGs1VWc9PSIsInZhbHVlIjoiN1hKeG5OSExGU1ZVcEtTK0F4QkFhN2JOMXRlZXhTSndlc2ZiVWc2aXdsaz0iLCJtYWMiOiIyOGQwNTI1OWYzZWYxYTYxMzdmYzhiY2Q5N2FhZTczNzE5OTk3YjZlOTRmZjdlOGIzYTM4OTNkZmQxN2FjYTg3IiwidGFnIjoiIn0=','Siti Rahayu','Jl. Merdeka No 5 Nganjuk','Pelapor / Korban','eyJpdiI6ImVnQmNId3htN1N6cTZCVFNFQ3hha3c9PSIsInZhbHVlIjoiUXcyUVlrNlE5RXBUY2ZvZGlZMzFuUT09IiwibWFjIjoiODNhODlkM2RkNDE4NWQ1YjMxODFmMTJiMzk2NWI5ZDM4ZWE2MWM1OTY5Njk5NTYxZDc4ZjcwMTVlYjBiOTk1NiIsInRhZyI6IiJ9'),(15,'RKN270032372','eyJpdiI6InZWMVVkb2lyR1AwbHBid0dHdVBobUE9PSIsInZhbHVlIjoidzh5MkQ1MlhFNE4remdPOW5jOERjbThNSFl6VnVjTnVKTVUyTkdpdGZCUT0iLCJtYWMiOiI3ZWQyZDlhNzA2ZWJkMDM2ODI5ZWIyMDViYjcwZjgzZTM3N2RhNGZjZmM4NDUwZmZhYmZiOTQyOTUyNzkyNWFhIiwidGFnIjoiIn0=','AmbaSING','Banjardowo, 64393, Lengkong, Nganjuk, East Java, Indonesia','Pelapor / Korban','eyJpdiI6IlVPNlNuNGU0THFuNGZTYWVWTVpBWVE9PSIsInZhbHVlIjoiLzgvUHBIMHMzMmxRbVk2S3Z1YTFqUT09IiwibWFjIjoiNzkzMjRmNDRhMGE2NjA4MjY4M2Q1NmVkNGVkYmM2NzllZWNjZjE4NzVjOWEyNzc1MWYzNDRlMGYwNjNjMzUwNiIsInRhZyI6IiJ9'),(17,'RKN351626310','eyJpdiI6ImM1QklteWNoUHVmY21KK0F2NGl0bUE9PSIsInZhbHVlIjoiRTd6S21jRzBYSFFiY0NwUHcrWlphTm9KZDNTbXR2MEN4U1l1dm84Tzl4az0iLCJtYWMiOiJiNTY4YjQzMjRiMzgwNmQyMzdlOGVhNjc5NmRiZjNhM2UyM2FiMDY3ZGRhYzAzZWE3NDYwNDA5MmZhODVlYTM1IiwidGFnIjoiIn0=','E2E Mobile Test','Jl. Test','Pelapor / Korban','eyJpdiI6IjhPenVYNEhhVVlzSXFaQ29tR0thK3c9PSIsInZhbHVlIjoiNVhwS1pDWjMzL1NvMHRhYzdLRjZsZz09IiwibWFjIjoiM2I3MTVhYjI0MzE5OTMzZDY1ZDVhMmQ2ZjgwOTcwNDE2ZWExNDMxZDQ2Y2I1NDUwYmZkZjFhODI2ZjJmZjNiZiIsInRhZyI6IiJ9'),(18,'RKN352377863','eyJpdiI6Iis1dzB6cjVjQnllT1UzbTVNa3lBdUE9PSIsInZhbHVlIjoieTNMbnVEZ0tFQWN6dHMzTVhHSHNGN3FpTy9wclE2VVJOVG9BSjdiVWMvOD0iLCJtYWMiOiIxZWI2YmY4ZjRkYjY0N2Y2MWM2Yjk5ZGI0MjdiMTIzNjZlOThmMTA2NzY0ZjFjZTA2OTYyMDNlOWM0ZGZlNzAxIiwidGFnIjoiIn0=','Uji Integrasi Payload','Nganjuk','-','eyJpdiI6IndQaEV3NXJLTFNpRmZDZ1Mva29Eanc9PSIsInZhbHVlIjoiZzJ3akdYTDl0RE1mTkN2MmxMY3Rodz09IiwibWFjIjoiNGQxM2Y5YTBiNWQ5MjI5OTYxNjViMjRiNTAwNGU2N2EyY2E4MWQ0YmVkMDM4YzUzMzI0NzE0YjZiMTUzODFhNCIsInRhZyI6IiJ9'),(19,'RKN354029237','eyJpdiI6IkJ5eEI3MVl1ZkV6SXN4eUdTWFM0b0E9PSIsInZhbHVlIjoia3Z6dC9kbEFDVTlUTWV0dGxnRnNucHN4MlFkb3l4anhpaGY4cUljcjJ4Zz0iLCJtYWMiOiI1NmU5Y2IxYWY2NWFiZmQzYWQ5YjBkOWRlNDU2ZWQzMDkzZDk4YTViOTczMGU2Mzg3ODU1NDRlNjk3MDRiZGNhIiwidGFnIjoiIn0=','E2E Mobile Test','Jl. E2E Test No. 1, Nganjuk','-','eyJpdiI6IlZmdHMvdDh6UkdxWGZoN05Jc1lmaFE9PSIsInZhbHVlIjoiaGpvSVZjd2dDVFBOK0JMam43L3ZqUT09IiwibWFjIjoiZTFjNTBiMzgzNGU2ZmEyODJmNmFjZGViODdjMmYxMGM5ZGFmMzE3MzRkMTc2ZWJmZjQzMWI0MjA3NzFkMGY1MSIsInRhZyI6IiJ9'),(20,'RKN354938754','eyJpdiI6Ik01V00xMWJudmgweUIwRzBhUXdrcnc9PSIsInZhbHVlIjoiM1gyNWE5eEYxbFdVZVVGejZDdiszZzBWVlRRc1U4bi8yb2RkUGNYVlJvOD0iLCJtYWMiOiJkOWU3Y2VjNDNjYzc3ZTAxM2NmZDdhZTZiYWM3MmY1N2M1MzEwOTgxYjE5ZWZkYmUxMWRiMGM4ZjE0YzZmYWJmIiwidGFnIjoiIn0=','Arieff','Nganjuk, East Java, Indonesia','-','eyJpdiI6ImFRTTRCMFRjRURDUjZtSDE0d1E0Vmc9PSIsInZhbHVlIjoiZG4raUpjWWpBZ0ZFbjAvZTVKajY4dz09IiwibWFjIjoiZTM0ZTg4MzYyN2Q3M2I2ZjEwZGRhNTMzYTViNWYyYWM2MDE1NDNkMjdmYzVjYTBjODc3MDM3NTI0NjYyODBjNCIsInRhZyI6IiJ9'),(21,'RKN356101984','eyJpdiI6ImxDU0hTQVVUVkpMRXM5VHJ6djZUS3c9PSIsInZhbHVlIjoiaFR5Z2tDVjZPZUVUT1ZEbGFsekd3VGxpMlY0TUJZWnBtYml1dVIzNEZkTT0iLCJtYWMiOiIwZDIwMDM2MWYwZjI5ZDk3YjE1MTM0ZmY3NzBkZmM4NDFmNDMyOWQ2N2U4ZTJmODFiMmQyYjNjMWNhNmFjZDNjIiwidGFnIjoiIn0=','Arieff','Nganjuk, East Java, Indonesia','-','eyJpdiI6Ikk4aHMrZDVidnFMamZ5dWU1ZEFTV0E9PSIsInZhbHVlIjoidngxSk5FMEdjKzlHZW5PaENWbE9JQT09IiwibWFjIjoiYWNkODlmNzJkZmQ1ODFlZWU0ODE4ZmYyYzFlOTE5Mzc1YjM5NTY5Y2U4ZDZkNTBiNzFlNjI4ZWI3NmQxOTJhMiIsInRhZyI6IiJ9');
/*!40000 ALTER TABLE `detail_pelapor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_penerima_manfaat`
--

DROP TABLE IF EXISTS `detail_penerima_manfaat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_penerima_manfaat` (
  `id_penerima` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_laporan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `umur` int DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pekerjaan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agama` enum('Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pendidikan` enum('Tidak Sekolah','SD','SMP','SMA','Diploma','S1','S2','S3','Lainnya') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hubungan_dengan_terlapor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notelp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `informasi_tambahan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_penerima`),
  KEY `detail_penerima_manfaat_id_laporan_foreign` (`id_laporan`),
  CONSTRAINT `detail_penerima_manfaat_id_laporan_foreign` FOREIGN KEY (`id_laporan`) REFERENCES `laporan` (`id_laporan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_penerima_manfaat`
--

LOCK TABLES `detail_penerima_manfaat` WRITE;
/*!40000 ALTER TABLE `detail_penerima_manfaat` DISABLE KEYS */;
INSERT INTO `detail_penerima_manfaat` VALUES (1,'RKN266786732','eyJpdiI6IjJRaEpkTURBQWhINzZQUDZIWmFzTEE9PSIsInZhbHVlIjoiNEVrbWowY2JpSjVEbFVqYUlpSmZGQVBGTnBBQUI2dDAzelp6M1FXdFVqaz0iLCJtYWMiOiJkYWQ4N2E1ZjRlNTAzYjE2MWNlMDUzZjg0MzU1OTNlMDUxY2ZkN2I0ZjBlMzQ2NGFjOGYxMWZjMjQ0ZmViZTIyIiwidGFnIjoiIn0=','Budi Santoso','-','2000-01-01',0,'Laki-laki','-','Islam','Nganjuk','Tidak Sekolah','-','eyJpdiI6IjlXVloxK1Jhd21qbmF3K25HTitqWnc9PSIsInZhbHVlIjoiV3NrSEkrKzkyRkxUdUZiSUhkTVpZdz09IiwibWFjIjoiYTBlZTU0NDVhOTQxMjllMWNhN2VhOTcxMmVjMmY0OGE5NzljMzJlOTI1NDMzNDVkNmI1MjlmMDBhNWI5NTM3MSIsInRhZyI6IiJ9','-'),(2,'RKN267238640','eyJpdiI6IjRtRDhjQUNLN3ZISWxlTXhnL0w4Z1E9PSIsInZhbHVlIjoiRjI3QTBja0F0SlNoZ1pRNG41NEZ0eDdqSmVORXZ4MitlRVd4ODg5MnA4ND0iLCJtYWMiOiJiZGMxM2FkNDJjZTdkOGM0N2IxNGRmZGE3ZGU4OWM1ZjgyMmQ5MWE2ZmE4MmY5ZGI3MzViYjQyN2E4MGU2ZTE1IiwidGFnIjoiIn0=','Amba','-','2000-01-01',0,'Laki-laki','-','Islam','Ngringin, 64393, Lengkong, Nganjuk, East Java, Indonesia','Tidak Sekolah','-','eyJpdiI6IlRheEhpTFczN0I2ajNwYlVKVjVYM0E9PSIsInZhbHVlIjoieklWYTBYb3JwOWFxTHNzL1g0a0RyZz09IiwibWFjIjoiOGM0ZmE1ZGMwZmE5Y2M3NzAyODUxMTQ4OGI1MDEzZWUwOTY5OGY3YzM0YWJjM2NiYTQ2MGRiMTEwZWZiMWE5OSIsInRhZyI6IiJ9','-'),(3,'RKN267729809','eyJpdiI6ImpQQlBDMStOamVBZUxwcTd6Z2NEbnc9PSIsInZhbHVlIjoiZ09QQXhNdEpPeW9tdG5mU2F2bDZKWUZ0NVFKVm5uc0haQ3p3U2pKTnA3MD0iLCJtYWMiOiI5NDZjMjQ3YTZlODIyMTE1YWY2OWRlOGFiMDQ5NjI3NGY3YjZlYWUyOWM5MjlhYTc4NmNkODQ4OWM5NmRmMTYxIiwidGFnIjoiIn0=','Test Laporan Baru','-','2000-01-01',0,NULL,'-','Islam','Nganjuk Test','Tidak Sekolah','-','eyJpdiI6Ik94YkpsZ1lZLzNkYU5PMGhHT09Rcnc9PSIsInZhbHVlIjoiRmJwd0hJRHpNdlF1VFltR2VEM3FPUT09IiwibWFjIjoiYTBhYzRjYjgwMzkxZTRmYTg0ZmYwZTE2ZjcxZWZiNDg1NTJiZDJkYzU0ZmU1MTI0OWQyODcyYmY5NGE4ZjVkYyIsInRhZyI6IiJ9','-'),(4,'RKN268147997','eyJpdiI6IktKUjNwc3A3a1Q5eWdWclFpNTgwcUE9PSIsInZhbHVlIjoiL0ttd2tqZ1E2OWdCaFZRd1RaTXpWQ3krUE9ZNTlqNnhMYXlLQ3E5OXF6bz0iLCJtYWMiOiJlNTA1YzJmNmU0OGExNmJhYjExMWI5MWE1NzVlZjI3ZjZmOWZiNmMxNjgzOWE2OGZmNTcyMzJjOTQyYmI1Y2M1IiwidGFnIjoiIn0=','Amba','-','2000-01-01',0,NULL,'-','Islam','Ngrimbi, 61474, Bareng, Jombang, East Java, Indonesia','Tidak Sekolah','-','eyJpdiI6ImU2TWM2YWFMQkVndmlTR0VSbTJEZ1E9PSIsInZhbHVlIjoiYzNtbnh5VmJhWU1URXArMXBnUDFMZz09IiwibWFjIjoiNWMzOThmMjNhY2I2NzhjMjFjYzlkODY3M2E3ZmEzNTFlNDE4OTYyMjlmOTc3OTE0OTMyNTIwMTFhN2NjYWE4NCIsInRhZyI6IiJ9','-'),(5,'RKN268765103','eyJpdiI6Ik5HZXZpc1NuVC9kSnVaVm1tcFRkVVE9PSIsInZhbHVlIjoiTy9aejc1V00xNm1tcmhjSXZSNjhncmszK2V6QXg0dmRIdU5LLzBpckZ1UT0iLCJtYWMiOiI3ZjgzYjA1YjlkZTFmMDQ4MDhhOGNhNTNkMzQxYzQ4MmExODA2YWJlM2IzZDRiNzU0MjM2MDk4MDBmNTY3MGVjIiwidGFnIjoiIn0=','Test API','-','2000-01-01',0,NULL,'-','Islam','Nganjuk','Tidak Sekolah','-','eyJpdiI6Ink4T3Q1cDZmNUpuZDRKTXdML0JrTFE9PSIsInZhbHVlIjoiRDZKY1VxeXl3d2g0SG45Y3JCK1ZVQT09IiwibWFjIjoiMzUwMTcxMWIzMTdkNzhhYTI3NDhkNTEyOTU0YmVjY2IyODU3MWQ3Yzc5OGI2Yzc2OTU4NDQ1N2U0ZDNhMDA3NiIsInRhZyI6IiJ9','-'),(6,'RKN268791933','eyJpdiI6ImxFNkUxelNCWFNLa1BZSmN3dExUYnc9PSIsInZhbHVlIjoiTm54THAyUXpMbUplSU1SZ245U08rQT09IiwibWFjIjoiYzA3ZmYxNWM3YjAyZWQ2ZWFlMGMzM2EwMTk3MGQzMTI2Y2NhZTgzZTY4ZTU4MjNhZWE3ZjU2MjRkNDAxOTY3YSIsInRhZyI6IiJ9','Test','-','2000-01-01',0,'Laki-laki','-','Islam','Nganjuk','Tidak Sekolah','-','eyJpdiI6InQ1YmtwckRBS1Q2Q1FtREVwLzFOWXc9PSIsInZhbHVlIjoibGVwOGdqTkl1SHhHRStDRnE5cFFJQT09IiwibWFjIjoiZWRlZWQzZGE3ODgxMDgxYTVhNTg2NmQwYzMzZWJkYmMzMzJkMjUzYmQyMjJkOGJiYzhkZTMwNmI2OGMxNTNmMSIsInRhZyI6IiJ9','-'),(7,'RKN269075940','eyJpdiI6Ik96ZmZpd3U0NlNFZWdYci9tN2lIV1E9PSIsInZhbHVlIjoiMkp0bCt5T3VaSDJ3NlBVSjZhdWxXUjNZeG9TMXR0c1dqeUhTbUdNUTUwST0iLCJtYWMiOiI5YWQwZDQ1ZDVkMjBlMTJkODliZmZhMWNhMzMwYjZlZDAwM2NjNTU0ZDNjNGFhMGFkMGZlMDYxN2NiNWMxM2E3IiwidGFnIjoiIn0=','Siti Rahayu','Nganjuk','1995-05-20',30,NULL,'Ibu Rumah Tangga','Islam','Jl. Merdeka No 5 Nganjuk','SMA','Suami/Istri','eyJpdiI6Ii90M3lnUjZoWjZqdzJYTFg0dlpOV0E9PSIsInZhbHVlIjoiVnpmSmJwem0yaFVBUFl0bEJuN2wxUT09IiwibWFjIjoiY2UwZTJmZDhmMDhjYWViYWRmOWY1MDkxNDUyMmJmN2EyOTgxMTEyZjVlYWFjYzM5MWY0NTQyODg1ZDY5ZTYyNSIsInRhZyI6IiJ9','-'),(8,'RKN270032372','eyJpdiI6IjM1amh6YkpDSkxSb0JNR2xZQ0ZxZkE9PSIsInZhbHVlIjoiN0NuYlN6Sk1lTXdCeEFQMityVGQ3aG1CVStJT2lhNEcvVTEwS25la1RyQT0iLCJtYWMiOiJlY2Q4MTdlODdlMDRhNTk0ZTE0YmI2NmM0NWY0N2JhYTJhMzVjMzRjMTNhYjI4YmFjY2Y1ZDA5YzIyMjc5YWE2IiwidGFnIjoiIn0=','AmbaSING','-','2000-01-01',0,NULL,'-','Islam','Banjardowo, 64393, Lengkong, Nganjuk, East Java, Indonesia','Tidak Sekolah','-','eyJpdiI6Ik91Lyt1Z0JBY2EvVXFORWJRR2t2Vnc9PSIsInZhbHVlIjoiMURENS9OR0U3TUxWVWlka09wbTNXdz09IiwibWFjIjoiMzViYmU0MGZmOGYyNmVkZWQ4MWE4ZDVkZWM0YzMxMGU0Mzk0ZmI1NTIyNjMwOWJjNDQ1YzJlZDBmZTdmZDM5ZCIsInRhZyI6IiJ9','-'),(10,'RKN351626310','eyJpdiI6ImpleGVtNzU5Q1lTUURVODFzdFRUaHc9PSIsInZhbHVlIjoiclVlSnRkbmRUMHd6cU9zSmVnbXJuUUhFUC9iV1BjMEVrUEtXWGI2N1VmUT0iLCJtYWMiOiIyNjgwMWU5N2IyNWIwNzc2YThhOWZiMTI3YzI5ZWMyMzRmNThiZWFmYjg3YjVlNjE0MzZmYmZmMDI2Nzk1NTY1IiwidGFnIjoiIn0=','E2E Mobile Test','-','2000-01-01',0,'Laki-laki','-','Islam','Jl. Test','Tidak Sekolah','-','eyJpdiI6IkFzd2Y3dXJmSGdOcTdadndoOWQ3MVE9PSIsInZhbHVlIjoiUFRYTXhITXBRVndUbnp2T3AzalBUUT09IiwibWFjIjoiNDIyNmRjNWZiYTg1MjFjODlhMmNhNmYyMjIzZDkzZGI5MDYxMTZhMGE2MGZiYzIzMjU1NjEzMzQ1ZjkzYWI2MiIsInRhZyI6IiJ9','-'),(11,'RKN352377863','eyJpdiI6ImpKWjNIWFJNNFMxUFk5bUFTR3BPRnc9PSIsInZhbHVlIjoiN1JJL0tBTmtoV1VXeSsvV053VERQN0ZhcmJpaS9EWXdQMlBoeTdua0w5OD0iLCJtYWMiOiI4MzczYmJhMTBjZWE4ZWU2ZjE3OThmNDk0NmVkZWExYjJmNmI1NmE0YzM4YmJlN2Y5MTE2ZWYzMGRjNDU3NDM0IiwidGFnIjoiIn0=','Uji Integrasi Payload',NULL,NULL,NULL,NULL,NULL,NULL,'Nganjuk',NULL,NULL,'eyJpdiI6Im1WUytxVDJhclFzSXpEK09pR3pUTkE9PSIsInZhbHVlIjoiZXg3Tk56VmZFaS9QRlk2S2psN0VUQT09IiwibWFjIjoiYWU2MmE3Y2MyZTVmNjBjNjExNWE0Nzg0MzE0ZmY1ODM2YjJiOGIxZTQ0NzA2OWQ0MzQyMTM5Y2MwNzAyMGM2NCIsInRhZyI6IiJ9',NULL),(12,'RKN354029237','eyJpdiI6IkZOZGhFdUNLWDdaQXErZnZNVjk4VUE9PSIsInZhbHVlIjoiZTU0WnFsT1JCK1BiODAwVVhmanJGNDhpYzhyK0lnVW0vQ2lRLytYMHBmOD0iLCJtYWMiOiI1M2E0ZjI3ZDg4YmQ5MTcxOGNmNDg5MWQzMDhjZWY3MzdlMTRiMDhhOTNlNGM3MjNmMmFlOTZjY2Q2ZTRhMDJiIiwidGFnIjoiIn0=','E2E Mobile Test',NULL,NULL,NULL,NULL,NULL,NULL,'Jl. E2E Test No. 1, Nganjuk',NULL,NULL,'eyJpdiI6ImNuTEc3Q1h4bUZUOHZ3WWRobGhSOUE9PSIsInZhbHVlIjoiYW5IejhMeVhtZ3plNktrZWFmRHdPZz09IiwibWFjIjoiYjIyYmFjMTI5MjJjMjVkZWVhZWY4YmVhNzgzZGMwNjllOTUyYjhiOGE2YzUzOWY2YzlmOTM5MzBiNGYyMGViMiIsInRhZyI6IiJ9',NULL),(13,'RKN354938754','eyJpdiI6IjBUNFVvUFFQVjBnWnRMY29rRnE5UlE9PSIsInZhbHVlIjoicm1wRjY0VFdId2JITjNEZ2IvOElhZjM0aGhMQWVsMk5ZZ0VKYlE4VUw2VT0iLCJtYWMiOiI3YjRmMmQ3YTc5Yzk5ZjI5NmI2ZTRkNTMxZGY1ZmMyODFiODc3YmI0MzAzZjhkOGNmZmM0ZmFhNTQ1NDIyN2Q1IiwidGFnIjoiIn0=','Arieff',NULL,NULL,NULL,NULL,NULL,NULL,'Nganjuk, East Java, Indonesia',NULL,NULL,'eyJpdiI6IlRGOVNhc1NIdUIwb011VldKQUYxaHc9PSIsInZhbHVlIjoia00zNWg5L2Q0N0VqS2VaRVU5VStiQT09IiwibWFjIjoiMDk2Mzc5ZTk1YmQzM2YwOGUyMTNhN2E2NjlkNDZkOGQ5M2U5YzFkZTVlMDY3NDBmMzIzNGFlYzE2MDlhNTliMCIsInRhZyI6IiJ9',NULL),(14,'RKN356101984','eyJpdiI6Ii9GOFpMM2s3MXYyc3Myc04veG5iOXc9PSIsInZhbHVlIjoieDd0aXR3SWtBN29ENWNlU0kxZnFIMkdWUExFSUdSZHdQTkw4NVpMMEpWbz0iLCJtYWMiOiJkMGIyM2NhM2FjZWU2NTdlOWQ5YTQ5NmY1MTQ0OGI1OTkxMGRhYmRhYWIxNTNmZjkwZjhjODk2ZTAzMWZlMjkyIiwidGFnIjoiIn0=','Arieff',NULL,NULL,NULL,NULL,NULL,NULL,'Nganjuk, East Java, Indonesia',NULL,NULL,'eyJpdiI6IlU3K29jclNRYTQ2NGlTTkxuWnpXOEE9PSIsInZhbHVlIjoieExRajV2dE9rOEtqNnhKeTlNbnBTUT09IiwibWFjIjoiODIwZThjZjI3ZGI0ZjkyY2IwMGJiNWRiNWYyNjhlZWNiMmZkNWUyZTliOTU4NTMyM2FmODIyNTM2M2E3YTRkMCIsInRhZyI6IiJ9',NULL);
/*!40000 ALTER TABLE `detail_penerima_manfaat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_terlapor`
--

DROP TABLE IF EXISTS `detail_terlapor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_terlapor` (
  `id_terlapor` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_laporan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `umur` int DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `jenis_kelamin` enum('Laki-laki','Perempuan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hubungan_dengan_korban` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `informasi_tambahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_terlapor`),
  KEY `detail_terlapor_id_laporan_foreign` (`id_laporan`),
  CONSTRAINT `detail_terlapor_id_laporan_foreign` FOREIGN KEY (`id_laporan`) REFERENCES `laporan` (`id_laporan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_terlapor`
--

LOCK TABLES `detail_terlapor` WRITE;
/*!40000 ALTER TABLE `detail_terlapor` DISABLE KEYS */;
INSERT INTO `detail_terlapor` VALUES (1,'RKN269075940','eyJpdiI6ImR5V2tLRzJ5blp0VTlQRGphRkp2Z1E9PSIsInZhbHVlIjoidTFEL2x2WHI2MXpCU0tJdmlqTzBZZz09IiwibWFjIjoiN2M0ZmZiMjU5NGE0NTBmMjVlMGU3MTVmNzRmYTNlZmQ4ZWEyYTg0ZmVlNDBhMGNiMjY1NWJiYjNiMjE2NjdiMCIsInRhZyI6IiJ9','Dalam Penyelidikan',0,'-',NULL,'-','-'),(2,'RKN270032372','eyJpdiI6InRPVmtwRkdZczgvWS9MVyt4YmxIQUE9PSIsInZhbHVlIjoiVjlYaW5XQUJVaFRDeWMxRWZDMXlidz09IiwibWFjIjoiNmE2MGY4YjZlMzUyNDk2MTIzYTllZjc4Yjk3NGE4ZmU0ZDY0MzFlODEwNmFmZDE3ZWM2OWQ0ODI3Mzc0NWVlZCIsInRhZyI6IiJ9','Dalam Penyelidikan',0,'-',NULL,'-','-'),(3,'RKN351626310','eyJpdiI6Ijk5dks2ZzZzOWsvL1FISXJVV0YvSHc9PSIsInZhbHVlIjoiT2hlRUpZbFNrc1gyNTdkOG83M01Ldz09IiwibWFjIjoiNDI1MDFiZDQ0ODdiY2QzZWJkOTc0ZjY5NGY5N2JmNzZjNTg3NWFjNzNkYWFiYzMyZTQxNTBiZjFiYzk2M2M3MCIsInRhZyI6IiJ9','Dalam Penyelidikan',0,'-','Laki-laki','-','-');
/*!40000 ALTER TABLE `detail_terlapor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `informasi`
--

DROP TABLE IF EXISTS `informasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `informasi` (
  `id_informasi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_informasi`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `informasi`
--

LOCK TABLES `informasi` WRITE;
/*!40000 ALTER TABLE `informasi` DISABLE KEYS */;
INSERT INTO `informasi` VALUES (3,'JOSJIS','YIHUU','josjis-1794355861.jpg','2026-11-10 17:11:01','2026-11-10 17:11:01');
/*!40000 ALTER TABLE `informasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `informasi_anak`
--

DROP TABLE IF EXISTS `informasi_anak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `informasi_anak` (
  `id_anak` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_penerima` bigint unsigned NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `umur` int DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan` enum('Tidak Sekolah','PAUD','TK','SD','SMP','SMA','Lainnya') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agama` enum('Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Anak Kandung','Anak Angkat') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_anak`),
  KEY `informasi_anak_id_penerima_foreign` (`id_penerima`),
  CONSTRAINT `informasi_anak_id_penerima_foreign` FOREIGN KEY (`id_penerima`) REFERENCES `detail_penerima_manfaat` (`id_penerima`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `informasi_anak`
--

LOCK TABLES `informasi_anak` WRITE;
/*!40000 ALTER TABLE `informasi_anak` DISABLE KEYS */;
/*!40000 ALTER TABLE `informasi_anak` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laporan`
--

DROP TABLE IF EXISTS `laporan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laporan` (
  `id_laporan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_akun` bigint unsigned NOT NULL,
  `judul` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kecamatan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori` enum('Kekerasan Fisik','Kekerasan Psikis','Kekerasan Seksual','Penelantaran','Eksploitasi','TPPO','Lainnya','unset') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unset',
  `layanan_pjsrk` enum('rehabilitasi_sosial','pengaduan_bencana','permasalahan_sosial') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('dikirim','diterima','diproses','dirujuk','selesai','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dikirim',
  `alasan_penolakan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_laporan`),
  KEY `laporan_id_akun_foreign` (`id_akun`),
  CONSTRAINT `laporan_id_akun_foreign` FOREIGN KEY (`id_akun`) REFERENCES `akun` (`id_akun`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laporan`
--

LOCK TABLES `laporan` WRITE;
/*!40000 ALTER TABLE `laporan` DISABLE KEYS */;
INSERT INTO `laporan` VALUES ('RKN266786732',1,NULL,NULL,'unset',NULL,'dikirim',NULL,'2026-09-24 09:19:46','2026-09-24 09:19:46'),('RKN267238640',16,NULL,NULL,'unset',NULL,'dikirim',NULL,'2026-09-24 09:27:18','2026-09-24 09:27:18'),('RKN267729809',1,NULL,NULL,'unset',NULL,'dirujuk',NULL,'2026-09-24 09:35:29','2026-09-24 18:19:45'),('RKN268147997',16,NULL,NULL,'unset',NULL,'dirujuk',NULL,'2026-09-24 09:42:27','2026-09-24 10:06:38'),('RKN268765103',1,NULL,NULL,'Kekerasan Psikis',NULL,'dirujuk',NULL,'2026-09-24 09:52:45','2026-10-21 07:00:25'),('RKN268791933',1,NULL,NULL,'unset',NULL,'diterima',NULL,'2026-09-24 09:53:11','2026-09-24 09:53:11'),('RKN269075940',1,NULL,NULL,'Kekerasan Psikis',NULL,'dirujuk',NULL,'2026-09-24 09:57:55','2026-09-24 10:24:31'),('RKN270032372',16,NULL,NULL,'Kekerasan Seksual',NULL,'dirujuk',NULL,'2026-09-24 10:13:52','2026-09-24 10:20:15'),('RKN351626310',19,NULL,'Nganjuk','unset',NULL,'diterima',NULL,'2026-11-10 16:00:26','2026-11-10 16:00:26'),('RKN352377863',19,NULL,'Nganjuk','unset',NULL,'dikirim',NULL,'2026-11-10 16:12:57','2026-11-10 16:12:57'),('RKN354029237',19,NULL,'Rumah Korban, Jl. E2E Test No. 1, Nganjuk','unset',NULL,'dikirim',NULL,'2026-11-10 16:40:29','2026-11-10 16:40:29'),('RKN354938754',21,NULL,'Jalan Jendral Gatot Subroto, Nganjuk, Jawa Timur','unset',NULL,'dikirim',NULL,'2026-11-10 16:55:38','2026-11-10 16:55:38'),('RKN356101984',21,NULL,'Jalan Jendral Gatot Subroto, Nganjuk, Jawa Timur','unset',NULL,'dikirim',NULL,'2026-11-10 17:15:01','2026-11-10 17:15:01');
/*!40000 ALTER TABLE `laporan` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_laporan_insert_id` BEFORE INSERT ON `laporan` FOR EACH ROW BEGIN
    DECLARE timestamp_code BIGINT;
    DECLARE random_suffix INT;
    DECLARE new_kode VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

    -- Ambil UNIX timestamp (detik sejak epoch) dan konversi ke format 6 digit terakhir
    SET timestamp_code = UNIX_TIMESTAMP() % 1000000;

    -- Tambahkan nomor acak 3 digit untuk tambahan keunikan
    SET random_suffix = FLOOR(100 + RAND() * 900);

    -- Format kode: RKNtimestamp_random
    SET new_kode = CONCAT('RKN', timestamp_code, random_suffix);

    -- Pastikan kode unik
    WHILE EXISTS (SELECT 1 FROM `laporan` WHERE `id_laporan` = new_kode) DO
        SET random_suffix = FLOOR(100 + RAND() * 900);
        SET new_kode = CONCAT('RKN', timestamp_code, random_suffix);
    END WHILE;

    -- Set nilai id_laporan
    SET NEW.id_laporan = new_kode;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_laporan_insert_admin` AFTER INSERT ON `laporan` FOR EACH ROW BEGIN
                INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                SELECT 
                    id_akun,
                    CONCAT(NEW.id_laporan, " - Laporan Baru"),
                    CONCAT("Laporan baru dengan ID ", NEW.id_laporan, " telah dibuat oleh ",
                           (SELECT nama FROM akun WHERE id_akun = NEW.id_akun)),
                    "admin",
                    "terkirim",
                    NOW()
                FROM akun
                WHERE id_akun = 1;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_laporan_insert_user` AFTER INSERT ON `laporan` FOR EACH ROW BEGIN
                INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                VALUES (
                    NEW.id_akun,
                    CONCAT(NEW.id_laporan, " - Laporan Terkirim"),
                    CONCAT("Laporan Anda dengan ID ", NEW.id_laporan, " telah berhasil dikirim dan sedang menunggu verifikasi."),
                    "user",
                    "terkirim",
                    NOW()
                );
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_05_11_154920_create_akun_table',1),(2,'2025_05_11_154931_create_chats_table',1),(3,'2025_05_11_154932_create_laporan_table',1),(4,'2025_05_11_154933_create_detail_kasus_table',1),(5,'2025_05_11_154935_create_detail_pelapor_table',1),(6,'2025_05_11_154937_create_detail_penerima_manfaat_table',1),(7,'2025_05_11_154939_create_detail_terlapor_table',1),(8,'2025_05_11_154941_create_informasi_anak_table',1),(9,'2025_05_11_154943_create_informasi_table',1),(10,'2025_05_11_154946_create_notifikasi_table',1),(11,'2025_05_11_154948_create_sessions_table',1),(12,'2025_05_15_153029_create_personal_access_tokens_table',1),(13,'2025_05_16_150909_add_fcm_token_to_akun_table',1),(14,'2025_05_16_210803_nambbah_create_update_chat',1),(15,'2025_05_18_121107_add_all_notifikasi_triggers',1),(16,'2025_05_20_042823_remove_id_laporan_foreign_keys',1),(17,'2025_05_20_045907_update_id_laporan_to_varchar',1),(18,'2025_05_20_135014_update_relation_laporan',1),(19,'2026_09_24_081450_add_replied_to_id_to_chats_table',2),(20,'2026_11_02_100000_update_akun_add_superadmin_role_and_status',3),(21,'2026_11_02_100001_create_roles_and_permissions_tables',3),(22,'2026_11_02_100002_create_system_settings_table',3),(23,'2026_11_02_053643_create_cache_table',4),(24,'2026_11_06_000000_add_dinsos_pjsrk_fields_to_laporan_table',5),(25,'2026_11_09_000001_create_activity_logs_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifikasi`
--

DROP TABLE IF EXISTS `notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifikasi` (
  `id_notif` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_akun` bigint unsigned NOT NULL,
  `judul` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('terkirim','dibaca') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'terkirim',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notif`),
  KEY `notifikasi_id_akun_foreign` (`id_akun`),
  CONSTRAINT `notifikasi_id_akun_foreign` FOREIGN KEY (`id_akun`) REFERENCES `akun` (`id_akun`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifikasi`
--

LOCK TABLES `notifikasi` WRITE;
/*!40000 ALTER TABLE `notifikasi` DISABLE KEYS */;
INSERT INTO `notifikasi` VALUES (1,1,'Pesan Baru','Anda menerima pesan baru dari User Mobile Tester: Halo Admin, saya butuh konsultasi dari aplikasi mobile!','user','terkirim','2026-09-24 09:44:43'),(2,2,'Pesan Baru','Anda menerima pesan baru dari Administrator: Halo, saya Admin Sentra. Ada yang bisa kami bantu?','user','terkirim','2026-09-24 09:44:43'),(3,2,'Pesan Baru','Anda menerima pesan baru dari Administrator: MANANANA','user','terkirim','2026-09-24 10:04:46'),(4,1,'Pesan Baru','Anda menerima pesan baru dari User Mobile 14: Halo Admin, ini tes dari User 14 di local','user','terkirim','2026-09-24 11:28:10'),(5,1,'Pesan Baru','Anda menerima pesan baru dari Administrator: hlo','user','terkirim','2026-09-24 12:27:59'),(6,1,'Pesan Baru','Anda menerima pesan baru dari Administrator: bos','user','terkirim','2026-09-24 12:28:18'),(7,1,'Pesan Baru','Anda menerima pesan baru dari Administrator: oo','user','terkirim','2026-09-24 12:28:22'),(8,1,'Pesan Baru','Anda menerima pesan baru dari Administrator: kontol;','user','terkirim','2026-09-24 12:28:27'),(9,1,'Pesan Baru','Anda menerima pesan baru dari User Mobile Tester: Halo Admin, saya dari aplikasi mobile','user','terkirim','2026-09-24 12:35:52'),(10,2,'Pesan Baru','Anda menerima pesan baru dari aripJMK48: Halo, ada yang bisa kami bantu?','user','terkirim','2026-09-24 12:35:52'),(11,1,'Pesan Baru','Anda menerima pesan baru dari User Mobile Tester: Saya ingin konsultasi tentang anak','user','terkirim','2026-09-24 12:35:52'),(12,1,'Pesan Baru','Anda menerima pesan baru dari User Mobile 14: Permisi admin','user','terkirim','2026-09-24 12:35:52'),(13,1,'Pesan Baru','Anda menerima pesan baru dari aripJMK48: cokk','user','terkirim','2026-09-24 12:39:35'),(14,1,'Pesan Baru','Anda menerima pesan baru dari aripJMK48: woyy','user','terkirim','2026-09-24 12:39:57'),(15,1,'Pesan Baru','Anda menerima pesan baru dari aripJMK48: boss','user','terkirim','2026-09-24 12:56:54'),(16,1,'Pesan Baru','Anda menerima pesan baru dari aripJMK48: boss','user','terkirim','2026-09-24 13:01:36'),(17,15,'Pesan Baru','Anda menerima pesan baru dari aripJMK48: Halo Admin, saya aripJMK48 dari mobile','user','terkirim','2026-09-24 13:05:04'),(18,15,'Pesan Baru','Anda menerima pesan baru dari amba: tes','user','terkirim','2026-09-24 13:55:21'),(19,15,'Pesan Baru','Anda menerima pesan baru dari amba: tes','user','terkirim','2026-09-24 13:55:28'),(20,16,'Pesan Baru','Anda menerima pesan baru dari Admin Sentra: ya ada apa','user','terkirim','2026-09-24 13:55:44'),(21,16,'Pesan Baru','Anda menerima pesan baru dari Admin Sentra: ya ada apa','user','terkirim','2026-09-24 13:55:51'),(22,15,'Pesan Baru','Anda menerima pesan baru dari amba: halo','user','terkirim','2026-09-24 15:35:05'),(23,15,'Pesan Baru','Anda menerima pesan baru dari amba: kontol','user','terkirim','2026-09-24 15:36:26'),(24,16,'Pesan Baru','Anda menerima pesan baru dari Admin Sentra: ya','user','terkirim','2026-09-24 15:36:34'),(39,1,'RKN266786732 - Laporan Baru','Laporan baru dengan ID RKN266786732 telah dibuat oleh aripJMK48','admin','terkirim','2026-09-24 16:19:46'),(40,1,'RKN266786732 - Laporan Terkirim','Laporan Anda dengan ID RKN266786732 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-09-24 16:19:46'),(41,1,'RKN267238640 - Laporan Baru','Laporan baru dengan ID RKN267238640 telah dibuat oleh amba','admin','terkirim','2026-09-24 16:27:18'),(42,16,'RKN267238640 - Laporan Terkirim','Laporan Anda dengan ID RKN267238640 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-09-24 16:27:18'),(43,1,'RKN267729809 - Laporan Baru','Laporan baru dengan ID RKN267729809 telah dibuat oleh aripJMK48','admin','terkirim','2026-09-24 16:35:29'),(44,1,'RKN267729809 - Laporan Terkirim','Laporan Anda dengan ID RKN267729809 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-09-24 16:35:29'),(45,1,'RKN268147997 - Laporan Baru','Laporan baru dengan ID RKN268147997 telah dibuat oleh amba','admin','terkirim','2026-09-24 16:42:27'),(46,16,'RKN268147997 - Laporan Terkirim','Laporan Anda dengan ID RKN268147997 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-09-24 16:42:27'),(47,1,'RKN268765103 - Laporan Baru','Laporan baru dengan ID RKN268765103 telah dibuat oleh aripJMK48','admin','terkirim','2026-09-24 16:52:45'),(48,1,'RKN268765103 - Laporan Terkirim','Laporan Anda dengan ID RKN268765103 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-09-24 16:52:45'),(49,1,'RKN268791933 - Laporan Baru','Laporan baru dengan ID RKN268791933 telah dibuat oleh aripJMK48','admin','terkirim','2026-09-24 16:53:11'),(50,1,'RKN268791933 - Laporan Terkirim','Laporan Anda dengan ID RKN268791933 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-09-24 16:53:11'),(51,1,'RKN269075940 - Laporan Baru','Laporan baru dengan ID RKN269075940 telah dibuat oleh aripJMK48','admin','terkirim','2026-09-24 16:57:55'),(52,1,'RKN269075940 - Laporan Terkirim','Laporan Anda dengan ID RKN269075940 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-09-24 16:57:55'),(53,1,'RKN270032372 - Laporan Baru','Laporan baru dengan ID RKN270032372 telah dibuat oleh amba','admin','terkirim','2026-09-24 17:13:52'),(54,16,'RKN270032372 - Laporan Terkirim','Laporan Anda dengan ID RKN270032372 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-09-24 17:13:52'),(55,1,'Pesan Baru','Anda menerima pesan baru dari Admin Sentra: halo jomokkkkersssss','user','terkirim','2026-09-25 01:19:02'),(56,15,'Pesan Baru','Anda menerima pesan baru dari amba: woii','user','terkirim','2026-09-25 01:21:47'),(57,1,'RKN865375689 - Laporan Baru','Laporan baru dengan ID RKN865375689 telah dibuat oleh aripJMK48','admin','terkirim','2026-11-05 07:56:15'),(58,1,'RKN865375689 - Laporan Terkirim','Laporan Anda dengan ID RKN865375689 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-11-05 07:56:15'),(71,1,'RKN351626310 - Laporan Baru','Laporan baru dengan ID RKN351626310 telah dibuat oleh E2E Mobile Test','admin','terkirim','2026-11-10 23:00:26'),(72,19,'RKN351626310 - Laporan Terkirim','Laporan Anda dengan ID RKN351626310 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-11-10 23:00:26'),(73,15,'Pesan Baru','Anda menerima pesan baru dari E2E Mobile Test: Halo admin, ini uji integrasi chat','user','terkirim','2026-11-10 23:01:09'),(74,1,'RKN352377863 - Laporan Baru','Laporan baru dengan ID RKN352377863 telah dibuat oleh E2E Mobile Test','admin','terkirim','2026-11-10 23:12:57'),(75,19,'RKN352377863 - Laporan Terkirim','Laporan Anda dengan ID RKN352377863 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-11-10 23:12:57'),(76,1,'RKN354029237 - Laporan Baru','Laporan baru dengan ID RKN354029237 telah dibuat oleh E2E Mobile Test','admin','terkirim','2026-11-10 23:40:29'),(77,19,'RKN354029237 - Laporan Terkirim','Laporan Anda dengan ID RKN354029237 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-11-10 23:40:29'),(78,15,'Pesan Baru','Anda menerima pesan baru dari E2E Mobile Test: Pesan E2E dari aplikasi mobile','user','terkirim','2026-11-10 23:46:19'),(79,1,'RKN354938754 - Laporan Baru','Laporan baru dengan ID RKN354938754 telah dibuat oleh Arieff','admin','terkirim','2026-11-10 23:55:38'),(80,21,'RKN354938754 - Laporan Terkirim','Laporan Anda dengan ID RKN354938754 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-11-10 23:55:38'),(81,15,'Pesan Baru','Anda menerima pesan baru dari Arieff: adminn','user','terkirim','2026-11-10 23:58:07'),(82,1,'RKN356101984 - Laporan Baru','Laporan baru dengan ID RKN356101984 telah dibuat oleh Arieff','admin','terkirim','2026-11-11 00:15:01'),(83,21,'RKN356101984 - Laporan Terkirim','Laporan Anda dengan ID RKN356101984 telah berhasil dikirim dan sedang menunggu verifikasi.','user','terkirim','2026-11-11 00:15:01'),(84,15,'Pesan Baru','Anda menerima pesan baru dari Arieff: MIN apa kabar','user','terkirim','2026-11-11 01:07:02');
/*!40000 ALTER TABLE `notifikasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Umum',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'akun.view','Melihat Daftar Akun','Manajemen Akun','2026-11-01 22:35:31','2026-11-01 22:35:31'),(2,'akun.create','Membuat Akun Baru','Manajemen Akun','2026-11-01 22:35:31','2026-11-01 22:35:31'),(3,'akun.edit','Mengubah Data Akun','Manajemen Akun','2026-11-01 22:35:31','2026-11-01 22:35:31'),(4,'akun.delete','Menghapus Akun','Manajemen Akun','2026-11-01 22:35:31','2026-11-01 22:35:31'),(5,'akun.reset_password','Mereset Password Akun','Manajemen Akun','2026-11-01 22:35:31','2026-11-01 22:35:31'),(6,'role.view','Melihat Daftar Role','Role & Permission','2026-11-01 22:35:31','2026-11-01 22:35:31'),(7,'role.create','Membuat Role Baru','Role & Permission','2026-11-01 22:35:31','2026-11-01 22:35:31'),(8,'role.edit','Mengubah Role & Hak Akses','Role & Permission','2026-11-01 22:35:31','2026-11-01 22:35:31'),(9,'role.delete','Menghapus Role','Role & Permission','2026-11-01 22:35:31','2026-11-01 22:35:31'),(10,'setting.view','Melihat Pengaturan Sistem','Konfigurasi Sistem','2026-11-01 22:35:31','2026-11-01 22:35:31'),(11,'setting.edit','Mengubah Pengaturan Sistem','Konfigurasi Sistem','2026-11-01 22:35:31','2026-11-01 22:35:31'),(12,'laporan.view','Melihat Data Laporan','Laporan','2026-11-01 22:35:31','2026-11-01 22:35:31'),(13,'laporan.process','Memproses Status Laporan','Laporan','2026-11-01 22:35:31','2026-11-01 22:35:31'),(14,'laporan.rujuk','Merujuk Laporan ke Lembaga Lain','Laporan','2026-11-01 22:35:31','2026-11-01 22:35:31'),(15,'laporan.delete','Menghapus Laporan','Laporan','2026-11-01 22:35:31','2026-11-01 22:35:31'),(16,'informasi.manage','Mengelola Konten Informasi & Edukasi','Informasi','2026-11-01 22:35:31','2026-11-01 22:35:31'),(17,'chat.manage','Mengelola Chat & Konsultasi','Chat Konsultasi','2026-11-01 22:35:31','2026-11-01 22:35:31'),(18,'rekapan.export','Mengunduh Export Rekapan Laporan','Rekapan','2026-11-01 22:35:31','2026-11-01 22:35:31'),(19,'laporan.view_ppa','Melihat Laporan Kasus PPA','Laporan PPA','2026-11-01 23:34:25','2026-11-01 23:34:25'),(20,'laporan.process_ppa','Memproses Laporan Kasus PPA','Laporan PPA','2026-11-01 23:34:25','2026-11-01 23:34:25'),(21,'laporan.rujuk_dinsos','Merujuk Laporan ke Dinas Sosial','Laporan PPA','2026-11-01 23:34:25','2026-11-01 23:34:25'),(22,'laporan.view_dinsos','Melihat Laporan Dinsos & Kasus Dirujuk','Laporan Dinsos','2026-11-01 23:34:25','2026-11-01 23:34:25'),(23,'laporan.process_dinsos','Memproses Laporan Dinsos & Kasus Dirujuk','Laporan Dinsos','2026-11-01 23:34:25','2026-11-01 23:34:25'),(24,'dinsos.dashboard.view','Melihat Dashboard Dinsos','Dinsos PJSRK','2026-11-05 23:35:02','2026-11-05 23:35:02'),(25,'dinsos.laporan.verify','Memverifikasi Laporan Dinsos','Dinsos PJSRK','2026-11-05 23:35:02','2026-11-05 23:35:02'),(26,'dinsos.laporan.finish','Menyelesaikan Laporan Dinsos','Dinsos PJSRK','2026-11-05 23:35:02','2026-11-05 23:35:02'),(27,'dinsos.laporan.reject','Menolak Laporan Dinsos','Dinsos PJSRK','2026-11-05 23:35:02','2026-11-05 23:35:02');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_has_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(2,12),(5,12),(6,12),(1,13),(2,13),(5,13),(6,13),(1,14),(2,14),(5,14),(1,15),(1,16),(2,16),(5,16),(6,16),(1,17),(2,17),(5,17),(6,17),(1,18),(2,18),(5,18),(6,18),(1,19),(5,19),(1,20),(5,20),(1,21),(5,21),(1,22),(6,22),(1,23),(6,23),(1,24),(6,24),(1,25),(6,25),(1,26),(6,26),(1,27),(6,27);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'superadmin','Superadmin','Akses penuh mengelola seluruh akun, role, dan konfigurasi sistem.',1,'2026-11-01 22:35:31','2026-11-01 22:35:31'),(2,'admin','Admin Operasional','Petugas operasional yang menangani laporan, rekapan, informasi, dan konsultasi chat.',1,'2026-11-01 22:35:31','2026-11-01 22:35:31'),(3,'user','User / Pelapor','Masyarakat pelapor pengaduan kasus kekerasan perempuan dan anak.',1,'2026-11-01 22:35:31','2026-11-01 22:35:31'),(4,'guest','Guest','Akses publik terbatas untuk penelusuran status laporan.',1,'2026-11-01 22:35:31','2026-11-01 22:35:31'),(5,'admin_ppa','Admin PPA','Petugas Unit Pelaksana Teknis Perlindungan Perempuan dan Anak.',1,'2026-11-01 23:34:25','2026-11-01 23:34:25'),(6,'admin_dinsos','Admin Dinsos','Petugas Dinas Sosial untuk penanganan masalah sosial, penelantaran, dan rujukan.',1,'2026-11-01 23:34:25','2026-11-01 23:34:25');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('gsEKNaW7gcBdpa0Ca5ooXLP11r4E4dNhCh5DYg1J',15,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVVZNRGV1OXpLWWtBTGZ2MjlBN0pGOWZ2ZmY1Vkx0czB0eGZNR3d2TCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGF0LzIxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTU7fQ==',1794359314),('jh4Q8LIarj6d3BocOVo5tPRXeIjqvSR72FfWCMZG',15,'127.0.0.1','curl/8.23.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSkdZNUg4R002bFF1VUtHTkJvMnRPQXNZWXg2U2trS3RQb1pxRjV0TSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNTt9',1794355652),('r7yIyFkEJO8fmNCogU7l98tWmOOI8TvjmE7leCFt',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.3 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmpsaVJ1VHpNZjBvZTNJYXlTczVMMjRQZXVHUkdyQXNROWp1MlphdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1794354983);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `label` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'app_name','SENTRA','general','text','Nama Aplikasi',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(2,'instansi_name','Dinas Pemberdayaan Perempuan dan Perlindungan Anak','general','text','Nama Instansi',NULL,'2026-11-01 22:35:33','2026-11-09 09:21:59'),(3,'app_description','Sistem Informasi dan Pelayanan Terpadu Penanganan Kekerasan terhadap Perempuan dan Anak','general','textarea','Deskripsi Aplikasi',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(4,'footer_copyright','© 2026 SENTRA. All rights reserved.','general','text','Hak Cipta Footer',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(5,'hotline_number','0812-3456-7890','kontak','text','Nomor WhatsApp Hotline',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(6,'emergency_call','112 / 129','kontak','text','Call Center Darurat',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(7,'office_email','sentraapplication@gmail.com','kontak','text','Email Layanan',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(8,'office_address','Jl. Pemuda No. 45, Kompleks Perkantoran Pemerintah','kontak','textarea','Alamat Kantor',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(9,'report_sla_days','3','laporan','number','Target Respon Laporan (Hari)',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(10,'auto_archive_months','24','laporan','number','Retensi Arsip (Bulan)',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(11,'enable_chat_consultation','1','laporan','boolean','Layanan Konsultasi Chat',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(12,'session_lifetime_minutes','120','keamanan','number','Durasi Sesi Login (Menit)',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(13,'max_login_attempts','5','keamanan','number','Maksimal Percobaan Login Salah',NULL,'2026-11-01 22:35:33','2026-11-09 01:05:13'),(14,'otp_expiry_minutes','5','keamanan','number','Masa Berlaku OTP (Menit)',NULL,'2026-11-01 22:35:33','2026-11-09 00:48:38'),(20,'test_key','test_value','test','text','Test Key',NULL,'2026-11-09 00:21:25','2026-11-09 00:21:25');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'sentra'
--

--
-- Dumping routines for database 'sentra'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-11 10:49:05
