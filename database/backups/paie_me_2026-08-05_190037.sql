-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: paie_me
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
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `action` varchar(50) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_entity` (`entity_type`,`entity_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
INSERT INTO `audit_log` VALUES (1,1,'login','user',1,'Connexion utilisateur','::1','2026-08-05 13:35:12'),(2,1,'login','user',1,'Connexion utilisateur','::1','2026-08-05 13:39:00'),(3,1,'login','user',1,'Connexion utilisateur','::1','2026-08-05 13:51:26'),(4,1,'login','user',1,'Connexion utilisateur','::1','2026-08-05 13:56:26'),(5,1,'update','salarie',8,'Modification salari├®: ZIRI MOHAMED','::1','2026-08-05 15:43:38'),(6,1,'update','salarie',8,'Modification salari├®: ZIRI MOHAMED','::1','2026-08-05 15:47:04'),(7,1,'calculate','periode',2,'Recalcul paies p├®riode','::1','2026-08-05 15:47:42'),(8,1,'rouvrir','periode',2,'R├®ouverture p├®riode','::1','2026-08-05 16:44:38'),(9,1,'login','user',1,'Connexion utilisateur','::1','2026-08-05 18:32:33');
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bareme_anciennete`
--

DROP TABLE IF EXISTS `bareme_anciennete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bareme_anciennete` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned DEFAULT NULL,
  `annees_min` tinyint(3) unsigned NOT NULL,
  `annees_max` tinyint(3) unsigned NOT NULL,
  `taux` decimal(5,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `bareme_anciennete_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bareme_anciennete`
--

LOCK TABLES `bareme_anciennete` WRITE;
/*!40000 ALTER TABLE `bareme_anciennete` DISABLE KEYS */;
INSERT INTO `bareme_anciennete` VALUES (1,1,0,1,0.00,'2026-08-05 13:35:05'),(2,1,2,4,5.00,'2026-08-05 13:35:05'),(3,1,5,11,10.00,'2026-08-05 13:35:05'),(4,1,12,19,15.00,'2026-08-05 13:35:05'),(5,1,20,24,20.00,'2026-08-05 13:35:05'),(6,1,25,99,25.00,'2026-08-05 13:35:05'),(7,NULL,0,2,0.00,'2026-08-05 13:43:01'),(8,NULL,2,5,5.00,'2026-08-05 13:43:01'),(9,NULL,5,10,10.00,'2026-08-05 13:43:01'),(10,NULL,10,15,15.00,'2026-08-05 13:43:01'),(11,NULL,15,20,20.00,'2026-08-05 13:43:01'),(12,NULL,20,25,25.00,'2026-08-05 13:43:01'),(13,NULL,25,99,30.00,'2026-08-05 13:43:01'),(20,3,0,1,0.00,'2026-08-05 15:31:29'),(21,3,2,4,5.00,'2026-08-05 15:31:29'),(22,3,5,11,10.00,'2026-08-05 15:31:29'),(23,3,12,19,15.00,'2026-08-05 15:31:29'),(24,3,20,24,20.00,'2026-08-05 15:31:29'),(25,3,25,99,25.00,'2026-08-05 15:31:29');
/*!40000 ALTER TABLE `bareme_anciennete` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bareme_heures_sup`
--

DROP TABLE IF EXISTS `bareme_heures_sup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bareme_heures_sup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned DEFAULT NULL,
  `taux_normal` decimal(5,2) NOT NULL DEFAULT 25.00,
  `taux_majore` decimal(5,2) NOT NULL DEFAULT 50.00,
  `taux_jour_ferie` decimal(5,2) NOT NULL DEFAULT 100.00,
  `seuil_heures` tinyint(3) unsigned NOT NULL DEFAULT 8,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `societe_id` (`societe_id`),
  CONSTRAINT `bareme_heures_sup_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bareme_heures_sup`
--

LOCK TABLES `bareme_heures_sup` WRITE;
/*!40000 ALTER TABLE `bareme_heures_sup` DISABLE KEYS */;
INSERT INTO `bareme_heures_sup` VALUES (1,1,25.00,50.00,100.00,8,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(2,NULL,25.00,50.00,100.00,8,'2026-08-05 13:43:01','2026-08-05 13:43:01'),(4,3,25.00,50.00,100.00,8,'2026-08-05 15:31:29','2026-08-05 15:31:29');
/*!40000 ALTER TABLE `bareme_heures_sup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bareme_ir`
--

DROP TABLE IF EXISTS `bareme_ir`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bareme_ir` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `min` decimal(10,2) NOT NULL,
  `max` decimal(10,2) NOT NULL,
  `taux` decimal(5,2) NOT NULL,
  `deduction` decimal(10,2) NOT NULL,
  `type` enum('mensuel','annuel') NOT NULL DEFAULT 'mensuel',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bareme_ir`
--

LOCK TABLES `bareme_ir` WRITE;
/*!40000 ALTER TABLE `bareme_ir` DISABLE KEYS */;
INSERT INTO `bareme_ir` VALUES (1,0.00,3333.33,0.00,0.00,'mensuel'),(2,3333.34,5000.00,10.00,333.33,'mensuel'),(3,5000.01,6666.67,20.00,833.33,'mensuel'),(4,6666.68,8333.33,30.00,1500.00,'mensuel'),(5,8333.34,15000.00,34.00,1833.33,'mensuel'),(6,15000.01,999999.99,37.00,2283.33,'mensuel'),(7,0.00,40000.00,0.00,0.00,'annuel'),(8,40001.00,60000.00,10.00,4000.00,'annuel'),(9,60001.00,80000.00,20.00,10000.00,'annuel'),(10,80001.00,100000.00,30.00,18000.00,'annuel'),(11,100001.00,180000.00,34.00,22000.00,'annuel'),(12,180000.01,9999999.99,37.00,27400.00,'annuel'),(13,0.00,3333.33,0.00,0.00,'mensuel'),(14,3333.34,5000.00,10.00,333.33,'mensuel'),(15,5000.01,6666.67,20.00,833.33,'mensuel'),(16,6666.68,8333.33,30.00,1500.00,'mensuel'),(17,8333.34,15000.00,34.00,1833.33,'mensuel'),(18,15000.01,999999.99,37.00,2283.33,'mensuel'),(19,0.00,40000.00,0.00,0.00,'annuel'),(20,40001.00,60000.00,10.00,4000.00,'annuel'),(21,60001.00,80000.00,20.00,10000.00,'annuel'),(22,80001.00,100000.00,30.00,18000.00,'annuel'),(23,100001.00,180000.00,34.00,22000.00,'annuel'),(24,180000.01,9999999.99,37.00,27400.00,'annuel'),(25,0.00,3333.33,0.00,0.00,'mensuel'),(26,3333.34,5000.00,10.00,333.33,'mensuel'),(27,5000.01,6666.67,20.00,833.33,'mensuel'),(28,6666.68,8333.33,30.00,1500.00,'mensuel'),(29,8333.34,15000.00,34.00,1833.33,'mensuel'),(30,15000.01,999999.99,37.00,2283.33,'mensuel'),(31,0.00,40000.00,0.00,0.00,'annuel'),(32,40001.00,60000.00,10.00,4000.00,'annuel'),(33,60001.00,80000.00,20.00,10000.00,'annuel'),(34,80001.00,100000.00,30.00,18000.00,'annuel'),(35,100001.00,180000.00,34.00,22000.00,'annuel'),(36,180000.01,9999999.99,37.00,27400.00,'annuel'),(37,0.00,3333.33,0.00,0.00,'mensuel'),(38,3333.34,5000.00,10.00,333.33,'mensuel'),(39,5000.01,6666.67,20.00,833.33,'mensuel'),(40,6666.68,8333.33,30.00,1500.00,'mensuel'),(41,8333.34,15000.00,34.00,1833.33,'mensuel'),(42,15000.01,999999.99,37.00,2283.33,'mensuel'),(43,0.00,40000.00,0.00,0.00,'annuel'),(44,40001.00,60000.00,10.00,4000.00,'annuel'),(45,60001.00,80000.00,20.00,10000.00,'annuel'),(46,80001.00,100000.00,30.00,18000.00,'annuel'),(47,100001.00,180000.00,34.00,22000.00,'annuel'),(48,180000.01,9999999.99,37.00,27400.00,'annuel'),(49,0.00,3333.33,0.00,0.00,'mensuel'),(50,3333.34,5000.00,10.00,333.33,'mensuel'),(51,5000.01,6666.67,20.00,833.33,'mensuel'),(52,6666.68,8333.33,30.00,1500.00,'mensuel'),(53,8333.34,15000.00,34.00,1833.33,'mensuel'),(54,15000.01,999999.99,37.00,2283.33,'mensuel'),(55,0.00,40000.00,0.00,0.00,'annuel'),(56,40001.00,60000.00,10.00,4000.00,'annuel'),(57,60001.00,80000.00,20.00,10000.00,'annuel'),(58,80001.00,100000.00,30.00,18000.00,'annuel'),(59,100001.00,180000.00,34.00,22000.00,'annuel'),(60,180000.01,9999999.99,37.00,27400.00,'annuel'),(61,0.00,3333.33,0.00,0.00,'mensuel'),(62,3333.34,5000.00,10.00,333.33,'mensuel'),(63,5000.01,6666.67,20.00,833.33,'mensuel'),(64,6666.68,8333.33,30.00,1500.00,'mensuel'),(65,8333.34,15000.00,34.00,1833.33,'mensuel'),(66,15000.01,999999.99,37.00,2283.33,'mensuel'),(67,0.00,40000.00,0.00,0.00,'annuel'),(68,40001.00,60000.00,10.00,4000.00,'annuel'),(69,60001.00,80000.00,20.00,10000.00,'annuel'),(70,80001.00,100000.00,30.00,18000.00,'annuel'),(71,100001.00,180000.00,34.00,22000.00,'annuel'),(72,180000.01,9999999.99,37.00,27400.00,'annuel');
/*!40000 ALTER TABLE `bareme_ir` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bareme_smig_smag`
--

DROP TABLE IF EXISTS `bareme_smig_smag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bareme_smig_smag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned DEFAULT NULL,
  `annee` int(11) NOT NULL,
  `type` enum('SMIG','SMAG') NOT NULL,
  `horaire` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mensuel` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date_effet` date DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_societe_annee_type` (`societe_id`,`annee`,`type`),
  CONSTRAINT `bareme_smig_smag_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bareme_smig_smag`
--

LOCK TABLES `bareme_smig_smag` WRITE;
/*!40000 ALTER TABLE `bareme_smig_smag` DISABLE KEYS */;
INSERT INTO `bareme_smig_smag` VALUES (1,1,2021,'SMIG',14.13,2698.83,'2021-01-01','2026-08-05 13:35:05'),(2,1,2021,'SMAG',73.05,1899.30,'2021-01-01','2026-08-05 13:35:05'),(3,1,2022,'SMIG',14.81,2828.71,'2022-01-01','2026-08-05 13:35:05'),(4,1,2022,'SMAG',76.70,1994.20,'2022-01-01','2026-08-05 13:35:05'),(5,1,2023,'SMIG',15.55,2970.05,'2023-01-01','2026-08-05 13:35:05'),(6,1,2023,'SMAG',84.37,2193.62,'2023-01-01','2026-08-05 13:35:05'),(7,1,2024,'SMIG',16.29,3111.39,'2024-01-01','2026-08-05 13:35:05'),(8,1,2024,'SMAG',88.58,2303.08,'2024-01-01','2026-08-05 13:35:05'),(9,1,2025,'SMIG',17.10,3266.10,'2025-01-01','2026-08-05 13:35:05'),(10,1,2025,'SMAG',93.00,2418.00,'2025-04-01','2026-08-05 13:35:05'),(11,1,2026,'SMIG',17.92,3422.72,'2026-01-01','2026-08-05 13:35:05'),(12,1,2026,'SMAG',97.44,2533.44,'2026-04-01','2026-08-05 13:35:05'),(13,NULL,2021,'SMIG',14.13,2698.83,'2021-01-01','2026-08-05 13:43:01'),(14,NULL,2021,'SMAG',73.05,1899.30,'2021-01-01','2026-08-05 13:43:01'),(15,NULL,2022,'SMIG',14.81,2828.71,'2022-01-01','2026-08-05 13:43:01'),(16,NULL,2022,'SMAG',76.70,1994.20,'2022-01-01','2026-08-05 13:43:01'),(17,NULL,2023,'SMIG',15.55,2970.05,'2023-01-01','2026-08-05 13:43:01'),(18,NULL,2023,'SMAG',84.37,2193.62,'2023-01-01','2026-08-05 13:43:01'),(19,NULL,2024,'SMIG',16.29,3111.39,'2024-01-01','2026-08-05 13:43:01'),(20,NULL,2024,'SMAG',88.58,2303.08,'2024-01-01','2026-08-05 13:43:01'),(21,NULL,2025,'SMIG',17.10,3266.10,'2025-01-01','2026-08-05 13:43:01'),(22,NULL,2025,'SMAG',93.00,2418.00,'2025-04-01','2026-08-05 13:43:01'),(23,NULL,2026,'SMIG',17.92,3422.72,'2026-01-01','2026-08-05 13:43:01'),(24,NULL,2026,'SMAG',97.44,2533.44,'2026-04-01','2026-08-05 13:43:01'),(37,3,2021,'SMIG',14.13,2698.83,'2021-01-01','2026-08-05 15:31:29'),(38,3,2021,'SMAG',73.05,1899.30,'2021-01-01','2026-08-05 15:31:29'),(39,3,2022,'SMIG',14.81,2828.71,'2022-01-01','2026-08-05 15:31:29'),(40,3,2022,'SMAG',76.70,1994.20,'2022-01-01','2026-08-05 15:31:29'),(41,3,2023,'SMIG',15.55,2970.05,'2023-01-01','2026-08-05 15:31:29'),(42,3,2023,'SMAG',84.37,2193.62,'2023-01-01','2026-08-05 15:31:29'),(43,3,2024,'SMIG',16.29,3111.39,'2024-01-01','2026-08-05 15:31:29'),(44,3,2024,'SMAG',88.58,2303.08,'2024-01-01','2026-08-05 15:31:29'),(45,3,2025,'SMIG',17.10,3266.10,'2025-01-01','2026-08-05 15:31:29'),(46,3,2025,'SMAG',93.00,2418.00,'2025-04-01','2026-08-05 15:31:29'),(47,3,2026,'SMIG',17.92,3422.72,'2026-01-01','2026-08-05 15:31:29'),(48,3,2026,'SMAG',97.44,2533.44,'2026-04-01','2026-08-05 15:31:29');
/*!40000 ALTER TABLE `bareme_smig_smag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bulletins`
--

DROP TABLE IF EXISTS `bulletins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bulletins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paie_id` int(10) unsigned NOT NULL,
  `numero` varchar(20) NOT NULL,
  `date_emission` date NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `paie_id` (`paie_id`),
  CONSTRAINT `bulletins_ibfk_1` FOREIGN KEY (`paie_id`) REFERENCES `paies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bulletins`
--

LOCK TABLES `bulletins` WRITE;
/*!40000 ALTER TABLE `bulletins` DISABLE KEYS */;
INSERT INTO `bulletins` VALUES (1,1,'TEC-00001','2026-08-05',NULL,'2026-08-05 13:35:06'),(2,2,'TEC-00002','2026-08-05',NULL,'2026-08-05 13:35:06'),(3,3,'TEC-00003','2026-08-05',NULL,'2026-08-05 13:35:06'),(4,4,'TEC-00004','2026-08-05',NULL,'2026-08-05 13:35:06'),(5,5,'TEC-00005','2026-08-05',NULL,'2026-08-05 13:35:06'),(6,6,'TEC-00006','2026-08-05',NULL,'2026-08-05 13:35:06'),(57,57,'TOU-00057','2026-07-31',NULL,'2026-08-05 16:20:03'),(58,58,'TOU-00058','2026-07-31',NULL,'2026-08-05 16:20:03'),(59,59,'TOU-00059','2026-07-31',NULL,'2026-08-05 16:20:03'),(60,60,'TOU-00060','2026-07-31',NULL,'2026-08-05 16:20:03'),(61,61,'TOU-00061','2026-07-31',NULL,'2026-08-05 16:20:03'),(62,62,'TOU-00062','2026-07-31',NULL,'2026-08-05 16:20:03'),(63,63,'TOU-00063','2026-07-31',NULL,'2026-08-05 16:20:03'),(64,64,'TOU-00064','2026-07-31',NULL,'2026-08-05 16:20:03'),(65,65,'TOU-00065','2026-07-31',NULL,'2026-08-05 16:20:03'),(66,66,'TOU-00066','2026-07-31',NULL,'2026-08-05 16:20:03'),(67,67,'TOU-00067','2026-07-31',NULL,'2026-08-05 16:20:03'),(68,68,'TOU-00068','2026-07-31',NULL,'2026-08-05 16:20:03'),(69,69,'TOU-00069','2026-07-31',NULL,'2026-08-05 16:20:03'),(70,70,'TOU-00070','2026-07-31',NULL,'2026-08-05 16:20:03'),(71,71,'TOU-00071','2026-07-31',NULL,'2026-08-05 16:20:03'),(72,72,'TOU-00072','2026-07-31',NULL,'2026-08-05 16:20:03'),(73,73,'TOU-00073','2026-07-31',NULL,'2026-08-05 16:20:03'),(74,74,'TOU-00074','2026-07-31',NULL,'2026-08-05 16:20:03'),(75,75,'TOU-00075','2026-07-31',NULL,'2026-08-05 16:20:03');
/*!40000 ALTER TABLE `bulletins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conge_annuel`
--

DROP TABLE IF EXISTS `conge_annuel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conge_annuel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `jours_par_mois` decimal(4,2) NOT NULL DEFAULT 1.50,
  `report_autorise` tinyint(1) NOT NULL DEFAULT 1,
  `report_max` tinyint(3) unsigned NOT NULL DEFAULT 15,
  `delai_anciennete` tinyint(3) unsigned NOT NULL DEFAULT 6,
  `report_max_annees` tinyint(3) unsigned NOT NULL DEFAULT 2,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `societe_id` (`societe_id`),
  CONSTRAINT `conge_annuel_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conge_annuel`
--

LOCK TABLES `conge_annuel` WRITE;
/*!40000 ALTER TABLE `conge_annuel` DISABLE KEYS */;
INSERT INTO `conge_annuel` VALUES (1,1,1.50,1,15,6,2,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(3,3,1.50,1,15,6,2,'2026-08-05 15:31:29','2026-08-05 15:31:29');
/*!40000 ALTER TABLE `conge_annuel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conges`
--

DROP TABLE IF EXISTS `conges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `salarie_id` int(10) unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nb_jours` decimal(5,1) NOT NULL,
  `type_conge` enum('paye','sans_solde','maladie','maternite','exceptionnel','autre') NOT NULL DEFAULT 'paye',
  `observation` text DEFAULT NULL,
  `statut` enum('en_attente','valide','refuse','annule') NOT NULL DEFAULT 'en_attente',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  KEY `salarie_id` (`salarie_id`),
  CONSTRAINT `conges_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conges_ibfk_2` FOREIGN KEY (`salarie_id`) REFERENCES `salaries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conges`
--

LOCK TABLES `conges` WRITE;
/*!40000 ALTER TABLE `conges` DISABLE KEYS */;
/*!40000 ALTER TABLE `conges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conges_soldes`
--

DROP TABLE IF EXISTS `conges_soldes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conges_soldes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `salarie_id` int(10) unsigned NOT NULL,
  `annee` int(11) NOT NULL,
  `solde_initial` decimal(5,1) NOT NULL DEFAULT 0.0,
  `conges_pris` decimal(5,1) NOT NULL DEFAULT 0.0,
  `report` decimal(5,1) NOT NULL DEFAULT 0.0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_salarie_annee` (`salarie_id`,`annee`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `conges_soldes_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conges_soldes_ibfk_2` FOREIGN KEY (`salarie_id`) REFERENCES `salaries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conges_soldes`
--

LOCK TABLES `conges_soldes` WRITE;
/*!40000 ALTER TABLE `conges_soldes` DISABLE KEYS */;
/*!40000 ALTER TABLE `conges_soldes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `droit_conge`
--

DROP TABLE IF EXISTS `droit_conge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `droit_conge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `annees_min` tinyint(3) unsigned NOT NULL,
  `annees_max` tinyint(3) unsigned NOT NULL,
  `jours_par_mois` decimal(4,2) NOT NULL DEFAULT 1.50,
  `jours_supplementaires` decimal(4,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `droit_conge_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `droit_conge`
--

LOCK TABLES `droit_conge` WRITE;
/*!40000 ALTER TABLE `droit_conge` DISABLE KEYS */;
INSERT INTO `droit_conge` VALUES (1,1,0,5,1.50,0.00,'2026-08-05 13:43:01'),(2,1,5,10,1.50,1.50,'2026-08-05 13:43:01'),(3,1,10,15,1.50,3.00,'2026-08-05 13:43:01'),(4,1,15,20,1.50,4.50,'2026-08-05 13:43:01'),(5,1,20,25,1.50,6.00,'2026-08-05 13:43:01'),(6,1,25,30,1.50,7.50,'2026-08-05 13:43:01'),(7,1,30,35,1.50,9.00,'2026-08-05 13:43:01'),(8,1,35,40,1.50,10.50,'2026-08-05 13:43:01'),(9,1,40,99,1.50,12.00,'2026-08-05 13:43:01'),(10,3,0,5,1.50,0.00,'2026-08-05 16:28:12'),(11,3,5,10,1.50,1.50,'2026-08-05 16:28:12'),(12,3,10,15,1.50,3.00,'2026-08-05 16:28:12'),(13,3,15,20,1.50,4.50,'2026-08-05 16:28:12'),(14,3,20,25,1.50,6.00,'2026-08-05 16:28:12'),(15,3,25,30,1.50,7.50,'2026-08-05 16:28:12'),(16,3,30,35,1.50,9.00,'2026-08-05 16:28:12'),(17,3,35,40,1.50,10.50,'2026-08-05 16:28:12'),(18,3,40,99,1.50,12.00,'2026-08-05 16:28:12');
/*!40000 ALTER TABLE `droit_conge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fonctions`
--

DROP TABLE IF EXISTS `fonctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonctions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `service_id` int(10) unsigned DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `fonctions_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fonctions_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonctions`
--

LOCK TABLES `fonctions` WRITE;
/*!40000 ALTER TABLE `fonctions` DISABLE KEYS */;
INSERT INTO `fonctions` VALUES (1,1,1,'Directeur G├®n├®ral',NULL,1,'2026-08-05 13:35:05'),(2,1,1,'Directeur Administratif',NULL,1,'2026-08-05 13:35:05'),(3,1,2,'D├®veloppeur Full Stack',NULL,1,'2026-08-05 13:35:05'),(4,1,2,'Administrateur Syst├¿mes',NULL,1,'2026-08-05 13:35:05'),(5,1,3,'Responsable RH',NULL,1,'2026-08-05 13:35:05'),(6,1,3,'Gestionnaire Paie',NULL,1,'2026-08-05 13:35:05'),(7,1,4,'Comptable',NULL,1,'2026-08-05 13:35:05'),(8,1,4,'Contr├┤leur de Gestion',NULL,1,'2026-08-05 13:35:05'),(9,1,5,'Commercial Senior',NULL,1,'2026-08-05 13:35:05'),(10,1,5,'Assistant Commercial',NULL,1,'2026-08-05 13:35:05'),(17,3,7,'RESPONSABLE',NULL,1,'2026-08-05 15:31:29'),(18,3,7,'TECHNICIEN',NULL,1,'2026-08-05 15:31:29'),(19,3,7,'ASSISTANTE ADMINISTRATIVE',NULL,1,'2026-08-05 15:31:29'),(20,3,7,'MAGAZINIER',NULL,1,'2026-08-05 15:31:29'),(21,3,7,'OUVRIER',NULL,1,'2026-08-05 15:31:29'),(22,3,7,'CHEFFEUR',NULL,1,'2026-08-05 15:31:29');
/*!40000 ALTER TABLE `fonctions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jours_feries`
--

DROP TABLE IF EXISTS `jours_feries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jours_feries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `nom` varchar(100) NOT NULL,
  `jour` tinyint(3) unsigned NOT NULL,
  `mois` tinyint(3) unsigned NOT NULL,
  `type` enum('fixe','variable') NOT NULL DEFAULT 'fixe',
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `jours_feries_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jours_feries`
--

LOCK TABLES `jours_feries` WRITE;
/*!40000 ALTER TABLE `jours_feries` DISABLE KEYS */;
INSERT INTO `jours_feries` VALUES (1,1,'Jour de l\'an',1,1,'fixe',1,'2026-08-05 13:35:05'),(2,1,'Manifeste de l\'Ind├®pendance',11,1,'fixe',1,'2026-08-05 13:35:05'),(3,1,'F├¬te du Tr├┤ne',30,7,'fixe',1,'2026-08-05 13:35:05'),(4,1,'F├¬te des Oueds',14,8,'fixe',1,'2026-08-05 13:35:05'),(5,1,'Anniversaire de la R├®volution',20,8,'fixe',1,'2026-08-05 13:35:05'),(6,1,'Marche Verte',6,11,'fixe',1,'2026-08-05 13:35:05'),(13,3,'Jour de l\'an',1,1,'fixe',1,'2026-08-05 15:31:29'),(14,3,'Manifeste de l\'Ind├®pendance',11,1,'fixe',1,'2026-08-05 15:31:29'),(15,3,'F├¬te du Tr├┤ne',30,7,'fixe',1,'2026-08-05 15:31:29'),(16,3,'F├¬te des Oueds',14,8,'fixe',1,'2026-08-05 15:31:29'),(17,3,'Anniversaire de la R├®volution',20,8,'fixe',1,'2026-08-05 15:31:29'),(18,3,'Marche Verte',6,11,'fixe',1,'2026-08-05 15:31:29');
/*!40000 ALTER TABLE `jours_feries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modeles_attestation`
--

DROP TABLE IF EXISTS `modeles_attestation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modeles_attestation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `titre` varchar(200) NOT NULL,
  `contenu` text NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `modeles_attestation_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modeles_attestation`
--

LOCK TABLES `modeles_attestation` WRITE;
/*!40000 ALTER TABLE `modeles_attestation` DISABLE KEYS */;
/*!40000 ALTER TABLE `modeles_attestation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modeles_bulletins`
--

DROP TABLE IF EXISTS `modeles_bulletins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modeles_bulletins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `nom` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`config`)),
  `defaut` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `modeles_bulletins_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modeles_bulletins`
--

LOCK TABLES `modeles_bulletins` WRITE;
/*!40000 ALTER TABLE `modeles_bulletins` DISABLE KEYS */;
INSERT INTO `modeles_bulletins` VALUES (1,1,'Mod├¿le Standard Maroc','Bulletin conforme au Code du Travail marocain','{\"nom\":\"Mod\\u00e8le Standard Maroc\",\"couleur_primaire\":\"#3b82f6\",\"sections\":[{\"titre\":\"Salaire et indemnit\\u00e9s\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"100\",\"label\":\"Salaire de base\"},{\"code\":\"204\",\"label\":\"Prime d\'anciennet\\u00e9\",\"show_base\":true,\"show_taux\":true,\"conditionnel\":true},{\"code\":\"330\",\"label\":\"Indemnit\\u00e9 de transport\",\"conditionnel\":true},{\"code\":\"346\",\"label\":\"Indemnit\\u00e9 de panier\",\"conditionnel\":true},{\"code\":\"331\",\"label\":\"Indemnit\\u00e9 de repr\\u00e9sentation\",\"conditionnel\":true},{\"code\":\"340\",\"label\":\"Avantage logement\",\"conditionnel\":true},{\"code\":\"201\",\"label\":\"Heures sup. 25%\",\"show_base\":true,\"conditionnel\":true},{\"code\":\"202\",\"label\":\"Heures sup. 50%\",\"show_base\":true,\"conditionnel\":true},{\"code\":\"203\",\"label\":\"Heures sup. 100%\",\"show_base\":true,\"conditionnel\":true}],\"total\":{\"code\":\"SB\",\"label\":\"Salaire brut\"}},{\"titre\":\"Cotisations salariales\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"400\",\"label\":\"CNSS (part salariale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"410\",\"label\":\"AMO (part salariale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"420\",\"label\":\"Mutuelle\",\"conditionnel\":true},{\"code\":\"501\",\"label\":\"Frais professionnels\",\"show_base\":true,\"show_taux\":true}],\"total\":{\"code\":\"502\",\"label\":\"Salaire net imposable (SNI)\"}},{\"titre\":\"Imp\\u00f4t sur le revenu\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"600\",\"label\":\"Imp\\u00f4t sur le revenu (IR)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"601\",\"label\":\"D\\u00e9ductions charges de famille\",\"conditionnel\":true}],\"total\":null},{\"titre\":\"Cotisations patronales\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"400P\",\"label\":\"CNSS (part patronale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"410P\",\"label\":\"AMO (part patronale)\",\"show_base\":true,\"show_taux\":true}],\"total\":null}],\"net_label\":\"Net \\u00e0 payer\",\"net_color\":\"#3b82f6\",\"show_footer\":true}',1,'2026-08-05 13:43:01'),(2,3,'Mod├¿le Standard Maroc','Bulletin conforme au Code du Travail marocain','{\"nom\":\"Mod\\u00e8le Standard Maroc\",\"couleur_primaire\":\"#3b82f6\",\"sections\":[{\"titre\":\"Salaire et indemnit\\u00e9s\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"100\",\"label\":\"Salaire de base\"},{\"code\":\"204\",\"label\":\"Prime d\'anciennet\\u00e9\",\"show_base\":true,\"show_taux\":true,\"conditionnel\":true},{\"code\":\"330\",\"label\":\"Indemnit\\u00e9 de transport\",\"conditionnel\":true},{\"code\":\"346\",\"label\":\"Indemnit\\u00e9 de panier\",\"conditionnel\":true},{\"code\":\"331\",\"label\":\"Indemnit\\u00e9 de repr\\u00e9sentation\",\"conditionnel\":true},{\"code\":\"340\",\"label\":\"Avantage logement\",\"conditionnel\":true},{\"code\":\"201\",\"label\":\"Heures sup. 25%\",\"show_base\":true,\"conditionnel\":true},{\"code\":\"202\",\"label\":\"Heures sup. 50%\",\"show_base\":true,\"conditionnel\":true},{\"code\":\"203\",\"label\":\"Heures sup. 100%\",\"show_base\":true,\"conditionnel\":true}],\"total\":{\"code\":\"SB\",\"label\":\"Salaire brut\"}},{\"titre\":\"Cotisations salariales\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"400\",\"label\":\"CNSS (part salariale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"410\",\"label\":\"AMO (part salariale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"420\",\"label\":\"Mutuelle\",\"conditionnel\":true},{\"code\":\"501\",\"label\":\"Frais professionnels\",\"show_base\":true,\"show_taux\":true}],\"total\":{\"code\":\"502\",\"label\":\"Salaire net imposable (SNI)\"}},{\"titre\":\"Imp\\u00f4t sur le revenu\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"600\",\"label\":\"Imp\\u00f4t sur le revenu (IR)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"601\",\"label\":\"D\\u00e9ductions charges de famille\",\"conditionnel\":true}],\"total\":null},{\"titre\":\"Cotisations patronales\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"400P\",\"label\":\"CNSS (part patronale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"410P\",\"label\":\"AMO (part patronale)\",\"show_base\":true,\"show_taux\":true}],\"total\":null}],\"net_label\":\"Net \\u00e0 payer\",\"net_color\":\"#3b82f6\",\"show_footer\":true}',1,'2026-08-05 16:28:12');
/*!40000 ALTER TABLE `modeles_bulletins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organismes`
--

DROP TABLE IF EXISTS `organismes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organismes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` enum('cnss','amo','cimr','mutuelle','autre') NOT NULL DEFAULT 'autre',
  `login` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `organismes_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organismes`
--

LOCK TABLES `organismes` WRITE;
/*!40000 ALTER TABLE `organismes` DISABLE KEYS */;
/*!40000 ALTER TABLE `organismes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paie_gains`
--

DROP TABLE IF EXISTS `paie_gains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paie_gains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paie_id` int(10) unsigned NOT NULL,
  `rubrique_id` int(10) unsigned NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paie_rubrique` (`paie_id`,`rubrique_id`),
  KEY `rubrique_id` (`rubrique_id`),
  CONSTRAINT `paie_gains_ibfk_1` FOREIGN KEY (`paie_id`) REFERENCES `paies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `paie_gains_ibfk_2` FOREIGN KEY (`rubrique_id`) REFERENCES `rubriques_gains` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paie_gains`
--

LOCK TABLES `paie_gains` WRITE;
/*!40000 ALTER TABLE `paie_gains` DISABLE KEYS */;
/*!40000 ALTER TABLE `paie_gains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paie_retenues`
--

DROP TABLE IF EXISTS `paie_retenues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paie_retenues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paie_id` int(10) unsigned NOT NULL,
  `type` enum('avance','pret','sanction','autre') NOT NULL DEFAULT 'autre',
  `libelle` varchar(200) NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `paie_id` (`paie_id`),
  CONSTRAINT `paie_retenues_ibfk_1` FOREIGN KEY (`paie_id`) REFERENCES `paies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paie_retenues`
--

LOCK TABLES `paie_retenues` WRITE;
/*!40000 ALTER TABLE `paie_retenues` DISABLE KEYS */;
/*!40000 ALTER TABLE `paie_retenues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paies`
--

DROP TABLE IF EXISTS `paies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `periode_id` int(10) unsigned NOT NULL,
  `salarie_id` int(10) unsigned NOT NULL,
  `societe_id` int(10) unsigned NOT NULL,
  `jours_travailles` tinyint(3) unsigned NOT NULL DEFAULT 30,
  `jours_conge` decimal(4,1) NOT NULL DEFAULT 0.0,
  `jours_feries` decimal(4,1) NOT NULL DEFAULT 0.0,
  `salaire_brut` decimal(10,2) NOT NULL DEFAULT 0.00,
  `prime_anciennete` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sbi` decimal(10,2) NOT NULL DEFAULT 0.00,
  `frais_professionnels` decimal(10,2) NOT NULL DEFAULT 0.00,
  `salaire_plafonne_cnss` decimal(10,2) NOT NULL DEFAULT 0.00,
  `indemnite_transport` decimal(10,2) NOT NULL DEFAULT 0.00,
  `indemnite_panier` decimal(10,2) NOT NULL DEFAULT 0.00,
  `indemnite_representation` decimal(10,2) NOT NULL DEFAULT 0.00,
  `avantage_logement` decimal(10,2) NOT NULL DEFAULT 0.00,
  `heures_supplementaires` decimal(10,2) NOT NULL DEFAULT 0.00,
  `montant_heures_sup` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_gains` decimal(10,2) NOT NULL DEFAULT 0.00,
  `montant_hs_25` decimal(10,2) NOT NULL DEFAULT 0.00,
  `montant_hs_50` decimal(10,2) NOT NULL DEFAULT 0.00,
  `montant_hs_100` decimal(10,2) NOT NULL DEFAULT 0.00,
  `heures_sup_25` decimal(10,2) NOT NULL DEFAULT 0.00,
  `heures_sup_50` decimal(10,2) NOT NULL DEFAULT 0.00,
  `heures_sup_100` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cnss_salariale` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amo_salariale` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mutuelle` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sni` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ir` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deductions_familiales` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_avant_retenues` decimal(10,2) NOT NULL DEFAULT 0.00,
  `autres_retenues` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_a_payer` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cnss_patronale` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amo_patronale` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paie` (`periode_id`,`salarie_id`),
  KEY `salarie_id` (`salarie_id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `paies_ibfk_1` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `paies_ibfk_2` FOREIGN KEY (`salarie_id`) REFERENCES `salaries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `paies_ibfk_3` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paies`
--

LOCK TABLES `paies` WRITE;
/*!40000 ALTER TABLE `paies` DISABLE KEYS */;
INSERT INTO `paies` VALUES (1,1,1,1,26,0.0,0.0,19103.33,750.00,17823.33,2916.70,6000.00,500.00,780.00,1500.00,0.00,0.00,0.00,573.33,0.00,0.00,0.00,0.00,0.00,0.00,268.80,431.74,0.00,14206.09,2996.74,100.00,15406.05,0.00,15506.05,538.80,785.15,'2026-08-05 13:35:06','2026-08-05 13:35:06'),(2,1,2,1,26,0.0,0.0,28253.33,2200.00,26973.33,2916.70,6000.00,500.00,780.00,2200.00,0.00,0.00,0.00,573.33,0.00,0.00,0.00,0.00,0.00,0.00,268.80,638.53,0.00,23149.30,6281.91,150.00,21064.09,0.00,21214.09,538.80,1161.21,'2026-08-05 13:35:06','2026-08-05 13:35:06'),(3,1,3,1,26,0.0,0.0,14453.33,600.00,13173.33,2916.70,6000.00,500.00,780.00,0.00,0.00,0.00,0.00,573.33,0.00,0.00,0.00,0.00,0.00,0.00,268.80,326.65,0.00,9661.18,1451.47,0.00,12406.41,0.00,12406.41,538.80,594.03,'2026-08-05 13:35:06','2026-08-05 13:35:06'),(4,1,4,1,26,0.0,0.0,16503.33,650.00,15223.33,2916.70,6000.00,500.00,780.00,1000.00,0.00,0.00,0.00,573.33,0.00,0.00,0.00,0.00,0.00,0.00,268.80,372.98,0.00,11664.85,2132.72,0.00,13728.83,0.00,13728.83,538.80,678.29,'2026-08-05 13:35:06','2026-08-05 13:35:06'),(5,1,5,1,26,0.0,0.0,35453.33,2800.00,34173.33,2916.70,6000.00,500.00,780.00,2800.00,0.00,0.00,0.00,573.33,0.00,0.00,0.00,0.00,0.00,0.00,268.80,801.25,0.00,30186.58,8885.70,200.00,25497.58,0.00,25697.58,538.80,1457.13,'2026-08-05 13:35:06','2026-08-05 13:35:06'),(6,1,6,1,26,0.0,0.0,11828.33,475.00,10548.33,2637.08,6000.00,500.00,780.00,0.00,0.00,0.00,0.00,573.33,0.00,0.00,0.00,0.00,0.00,0.00,268.80,267.32,0.00,7375.13,712.54,0.00,10579.67,0.00,10579.67,538.80,486.14,'2026-08-05 13:35:06','2026-08-05 13:35:06'),(57,2,7,3,26,0.0,0.0,22277.56,1749.80,19247.77,2916.67,6000.00,500.00,780.00,1749.80,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,268.80,435.00,0.00,15627.30,3498.77,0.00,18075.00,0.00,18075.00,2078.62,791.08,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(58,2,8,3,26,0.0,0.0,8572.62,714.39,7858.24,1964.56,6000.00,0.00,0.00,714.39,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,268.80,177.60,0.00,5447.28,0.00,0.00,8127.00,0.00,8127.00,1167.46,322.97,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(59,2,9,3,26,0.0,0.0,5361.46,0.00,5361.46,1876.51,5361.46,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,240.19,121.17,0.00,3123.59,0.00,0.00,5001.00,0.00,5001.00,910.38,220.36,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(60,2,10,3,26,0.0,0.0,5072.10,461.10,5072.10,1775.24,5072.10,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,227.23,114.63,0.00,2955.01,0.00,0.00,4731.00,0.00,4731.00,861.24,208.46,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(61,2,11,3,26,0.0,0.0,3940.29,187.63,3940.29,1379.10,3940.29,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,176.53,89.05,0.00,2295.61,0.00,0.00,3675.00,0.00,3675.00,669.06,161.95,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(62,2,12,3,26,0.0,0.0,3756.85,178.90,3756.85,1314.90,3756.85,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,168.31,84.90,0.00,2188.74,0.00,0.00,3504.00,0.00,3504.00,637.91,154.41,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(63,2,13,3,24,0.0,0.0,3523.65,320.33,3523.65,1233.28,3523.65,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,157.86,79.63,0.00,2052.88,0.00,0.00,3287.00,0.00,3287.00,598.32,144.82,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(64,2,14,3,26,0.0,0.0,3773.32,343.03,3773.32,1320.66,3773.32,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,169.04,85.28,0.00,2198.34,0.00,0.00,3519.00,0.00,3519.00,640.71,155.08,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(65,2,15,3,26,0.0,0.0,3593.86,171.14,3593.86,1257.85,3593.86,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,161.00,81.22,0.00,2093.78,0.00,0.00,3352.00,0.00,3352.00,610.24,147.71,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(66,2,16,3,26,0.0,0.0,3593.86,171.14,3593.86,1257.85,3593.86,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,161.00,81.22,0.00,2093.78,0.00,0.00,3352.00,0.00,3352.00,610.24,147.71,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(67,2,17,3,26,0.0,0.0,3422.72,0.00,3422.72,1197.95,3422.72,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,153.34,77.35,0.00,1994.08,0.00,0.00,3193.00,0.00,3193.00,581.18,140.67,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(68,2,18,3,26,0.0,0.0,3593.86,171.14,3593.86,1257.85,3593.86,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,161.00,81.22,0.00,2093.78,0.00,0.00,3352.00,0.00,3352.00,610.24,147.71,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(69,2,19,3,26,0.0,0.0,3422.72,0.00,3422.72,0.00,3422.72,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,153.34,77.35,0.00,3192.03,0.00,0.00,3193.00,0.00,3193.00,581.18,140.67,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(70,2,20,3,25,0.0,0.0,3480.96,316.45,3480.96,1218.34,3480.96,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,155.95,78.67,0.00,2028.01,0.00,0.00,3247.00,0.00,3247.00,591.07,143.07,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(71,2,21,3,25,0.0,0.0,3322.74,158.23,3322.74,1162.96,3322.74,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,148.86,75.09,0.00,1935.83,0.00,0.00,3099.00,0.00,3099.00,564.20,136.56,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(72,2,22,3,25,0.0,0.0,3164.51,0.00,3164.51,1107.58,3164.51,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,141.77,71.52,0.00,1843.64,0.00,0.00,2952.00,0.00,2952.00,537.33,130.06,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(73,2,23,3,24,0.0,0.0,2916.42,0.00,2916.42,1020.75,2916.42,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,130.66,65.91,0.00,1699.11,0.00,0.00,2720.00,0.00,2720.00,495.21,119.87,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(74,2,24,3,23,0.0,0.0,2812.39,133.92,2812.39,984.34,2812.39,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,125.99,63.56,0.00,1638.50,0.00,0.00,2623.00,0.00,2623.00,477.54,115.59,'2026-08-05 16:20:03','2026-08-05 16:20:03'),(75,2,25,3,23,0.0,0.0,2678.47,0.00,2678.47,937.46,2678.47,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,120.00,60.53,0.00,1560.47,0.00,0.00,2498.00,0.00,2498.00,454.80,110.08,'2026-08-05 16:20:03','2026-08-05 16:20:03');
/*!40000 ALTER TABLE `paies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parametres_cnss_amo`
--

DROP TABLE IF EXISTS `parametres_cnss_amo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parametres_cnss_amo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `plafond_cnss` decimal(10,2) NOT NULL DEFAULT 6000.00,
  `taux_cnss_salarial` decimal(5,2) NOT NULL DEFAULT 4.48,
  `taux_cnss_patronal` decimal(5,2) NOT NULL DEFAULT 8.98,
  `taux_cnss_patronal_non_plafonne` decimal(5,2) NOT NULL DEFAULT 0.00,
  `taux_amo_salarial` decimal(5,2) NOT NULL DEFAULT 2.26,
  `taux_amo_patronal` decimal(5,2) NOT NULL DEFAULT 4.11,
  `taux_amo_total` decimal(5,2) NOT NULL DEFAULT 6.37,
  `taux_ams_salarial` decimal(5,2) NOT NULL DEFAULT 0.00,
  `taux_ams_patronal` decimal(5,2) NOT NULL DEFAULT 0.00,
  `taux_allocations_familiales` decimal(5,2) NOT NULL DEFAULT 6.40,
  `taux_prestations_sociales` decimal(5,2) NOT NULL DEFAULT 13.46,
  `taxe_formation` decimal(5,2) NOT NULL DEFAULT 1.60,
  `participation_amo` decimal(5,2) NOT NULL DEFAULT 1.85,
  `taux_penalites_cnss` decimal(5,2) NOT NULL DEFAULT 0.00,
  `taux_penalites_tfp` decimal(5,2) NOT NULL DEFAULT 0.00,
  `taux_penalites_amo` decimal(5,2) NOT NULL DEFAULT 0.00,
  `penalite_cnss_premier_mois` decimal(5,2) NOT NULL DEFAULT 3.00,
  `penalite_cnss_mois_suivants` decimal(5,2) NOT NULL DEFAULT 0.50,
  `penalite_amo_taux` decimal(5,2) NOT NULL DEFAULT 1.00,
  `astreinte_cnss_par_salarie` decimal(10,2) NOT NULL DEFAULT 50.00,
  `astreinte_amo_par_salarie` decimal(10,2) NOT NULL DEFAULT 100.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `societe_id` (`societe_id`),
  CONSTRAINT `parametres_cnss_amo_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametres_cnss_amo`
--

LOCK TABLES `parametres_cnss_amo` WRITE;
/*!40000 ALTER TABLE `parametres_cnss_amo` DISABLE KEYS */;
INSERT INTO `parametres_cnss_amo` VALUES (1,1,6000.00,4.48,8.98,0.00,2.26,4.11,6.37,0.00,0.00,6.40,13.46,1.60,1.85,0.00,0.00,0.00,3.00,0.50,1.00,50.00,100.00,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(3,3,6000.00,4.48,8.98,8.00,2.26,4.11,6.37,0.00,0.00,6.40,13.46,1.60,1.85,0.00,0.00,0.00,3.00,0.50,1.00,50.00,100.00,'2026-08-05 15:31:29','2026-08-05 16:28:22');
/*!40000 ALTER TABLE `parametres_cnss_amo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `periodes`
--

DROP TABLE IF EXISTS `periodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `mois` tinyint(3) unsigned NOT NULL,
  `annee` smallint(5) unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `cloturee` tinyint(1) NOT NULL DEFAULT 0,
  `penalites_cnss` decimal(10,2) NOT NULL DEFAULT 0.00,
  `penalites_tfp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `penalites_amo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_periode` (`societe_id`,`mois`,`annee`),
  CONSTRAINT `periodes_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periodes`
--

LOCK TABLES `periodes` WRITE;
/*!40000 ALTER TABLE `periodes` DISABLE KEYS */;
INSERT INTO `periodes` VALUES (1,1,8,2026,'2026-08-01','2026-08-31',0,0.00,0.00,0.00,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(2,3,7,2026,'2026-07-01','2026-07-31',0,0.00,0.00,0.00,'2026-08-05 15:31:29','2026-08-05 16:44:38');
/*!40000 ALTER TABLE `periodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rubrique_sources_articles`
--

DROP TABLE IF EXISTS `rubrique_sources_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rubrique_sources_articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rubrique_id` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `article` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rubrique_source` (`rubrique_id`,`source_id`,`article`),
  KEY `source_id` (`source_id`),
  CONSTRAINT `rubrique_sources_articles_ibfk_1` FOREIGN KEY (`rubrique_id`) REFERENCES `rubriques_gains` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rubrique_sources_articles_ibfk_2` FOREIGN KEY (`source_id`) REFERENCES `sources_legales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rubrique_sources_articles`
--

LOCK TABLES `rubrique_sources_articles` WRITE;
/*!40000 ALTER TABLE `rubrique_sources_articles` DISABLE KEYS */;
INSERT INTO `rubrique_sources_articles` VALUES (1,54,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(2,55,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(3,56,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(4,57,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(5,58,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(6,59,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(7,60,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(8,61,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(9,62,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(10,63,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(11,64,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(12,65,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(13,66,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(14,67,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(15,68,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(16,69,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(17,70,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(18,71,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(19,72,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(20,73,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(21,74,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(22,75,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(23,76,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(24,77,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(25,78,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(26,79,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(27,80,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(28,81,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(29,82,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(30,83,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(31,84,5,'Art. 57-1┬░','2026-08-05 13:34:56'),(32,85,5,'Art. 57-7┬░','2026-08-05 13:34:56'),(33,86,5,'Art. 57-7┬░','2026-08-05 13:34:56'),(34,87,5,'Art. 57-7┬░','2026-08-05 13:34:56'),(35,85,1,'Art. 53','2026-08-05 13:34:56'),(36,86,1,'Art. 41','2026-08-05 13:34:56'),(37,88,1,'Art. 43','2026-08-05 13:34:56'),(38,54,4,'Titre I','2026-08-05 13:34:56'),(39,56,4,'Titre I','2026-08-05 13:34:56'),(40,57,4,'Titre I','2026-08-05 13:34:56'),(41,58,4,'Titre I','2026-08-05 13:34:56'),(42,59,4,'Titre I','2026-08-05 13:34:56'),(43,60,4,'Titre I','2026-08-05 13:34:56'),(44,61,4,'Titre I','2026-08-05 13:34:56'),(45,70,4,'Titre I','2026-08-05 13:34:56'),(46,71,4,'Titre I','2026-08-05 13:34:56'),(47,72,4,'Titre I','2026-08-05 13:34:56'),(48,55,4,'Titre II','2026-08-05 13:34:56'),(49,62,4,'Titre II','2026-08-05 13:34:56'),(50,63,4,'Titre II','2026-08-05 13:34:56'),(51,64,4,'Titre II','2026-08-05 13:34:56'),(52,65,4,'Titre II','2026-08-05 13:34:56'),(53,66,4,'Titre II','2026-08-05 13:34:56'),(54,67,4,'Titre II','2026-08-05 13:34:56'),(55,68,4,'Titre II','2026-08-05 13:34:56'),(56,69,4,'Titre II','2026-08-05 13:34:56'),(57,79,4,'Titre II','2026-08-05 13:34:56'),(58,73,4,'Titre V','2026-08-05 13:34:56'),(59,74,4,'Titre V','2026-08-05 13:34:56'),(60,75,4,'Titre V','2026-08-05 13:34:56'),(61,76,4,'Titre V','2026-08-05 13:34:56'),(62,77,4,'Titre V','2026-08-05 13:34:56'),(63,78,4,'Titre V','2026-08-05 13:34:56'),(64,80,4,'Titre V','2026-08-05 13:34:56'),(65,81,4,'Titre V','2026-08-05 13:34:56'),(66,82,4,'Titre V','2026-08-05 13:34:56'),(67,83,4,'Titre V','2026-08-05 13:34:56'),(68,84,4,'Titre V','2026-08-05 13:34:56'),(69,85,4,'Titre III','2026-08-05 13:34:56'),(70,86,4,'Titre III','2026-08-05 13:34:56'),(71,87,4,'Titre III','2026-08-05 13:34:56'),(72,88,4,'Titre III','2026-08-05 13:34:56'),(73,89,4,'Titre III','2026-08-05 13:34:56'),(74,90,4,'Titre III','2026-08-05 13:34:56'),(75,91,4,'Titre III','2026-08-05 13:34:56'),(76,92,4,'Titre III','2026-08-05 13:34:56'),(77,93,4,'Titre III','2026-08-05 13:34:56'),(78,94,4,'Titre III','2026-08-05 13:34:56'),(79,95,4,'Titre III','2026-08-05 13:34:56'),(80,96,4,'Titre VII','2026-08-05 13:34:56'),(128,49,5,'Art. 57 (soumis)','2026-08-05 14:43:12'),(129,50,5,'Art. 57 (soumis)','2026-08-05 14:43:12'),(130,51,5,'Art. 57 (soumis)','2026-08-05 14:43:12'),(131,52,5,'Art. 57 (soumis)','2026-08-05 14:43:12'),(132,53,5,'Art. 57 (soumis)','2026-08-05 14:43:12'),(133,49,1,'Art. 345-353','2026-08-05 14:43:12'),(134,50,1,'Art. 345-353','2026-08-05 14:43:12'),(135,51,1,'Art. 345-353','2026-08-05 14:43:12'),(136,52,1,'Art. 345-353','2026-08-05 14:43:12'),(137,53,1,'Art. 345','2026-08-05 14:43:12');
/*!40000 ALTER TABLE `rubrique_sources_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rubriques_gains`
--

DROP TABLE IF EXISTS `rubriques_gains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rubriques_gains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned DEFAULT NULL,
  `is_global` tinyint(1) NOT NULL DEFAULT 0,
  `code` varchar(20) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `type_montant` enum('Fixe','Proportionnel') NOT NULL DEFAULT 'Fixe',
  `valeur_defaut` decimal(10,2) NOT NULL DEFAULT 0.00,
  `categorie` varchar(50) DEFAULT NULL,
  `imposable` tinyint(1) NOT NULL DEFAULT 1,
  `affectation` varchar(20) DEFAULT NULL,
  `plafond_dgi` varchar(200) DEFAULT NULL,
  `plafond_cnss` varchar(200) DEFAULT NULL,
  `justificatifs` varchar(500) DEFAULT NULL,
  `plafond_dgi_actif` tinyint(1) NOT NULL DEFAULT 0,
  `plafond_dgi_valeur` decimal(10,2) DEFAULT NULL,
  `plafond_dgi_type` varchar(50) DEFAULT NULL,
  `plafond_cnss_actif` tinyint(1) NOT NULL DEFAULT 0,
  `plafond_cnss_valeur` decimal(10,2) DEFAULT NULL,
  `plafond_cnss_type` varchar(50) DEFAULT NULL,
  `plafond_dgi_desc` text DEFAULT NULL,
  `plafond_cnss_desc` text DEFAULT NULL,
  `compte` varchar(20) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `source_maj` date DEFAULT NULL,
  `nature_edi` varchar(20) DEFAULT NULL,
  `base_anciennete` tinyint(1) NOT NULL DEFAULT 0,
  `au_prorata` tinyint(1) NOT NULL DEFAULT 0,
  `imposable_ir` tinyint(1) NOT NULL DEFAULT 1,
  `imposable_cnss` tinyint(1) NOT NULL DEFAULT 1,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `rubriques_gains_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rubriques_gains`
--

LOCK TABLES `rubriques_gains` WRITE;
/*!40000 ALTER TABLE `rubriques_gains` DISABLE KEYS */;
INSERT INTO `rubriques_gains` VALUES (49,NULL,1,'501','Prime de rendement','Proportionnel',10.00,'Gain standard',1,'61711000','Imposable','Imposable','Contrat de travail / avenant d├®finissant les objectifs et crit├¿res de rendement',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','Contrat de travail','2025-10-01','REND',1,0,1,1,1,'2026-08-05 13:43:00'),(50,NULL,1,'502','Prime d\'objectifs','Proportionnel',5.00,'Gain standard',1,'61711000','Imposable','Imposable','Contrat de travail / avenant d├®finissant les objectifs',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','Contrat de travail','2025-10-01','OBJEC',1,0,1,1,1,'2026-08-05 13:43:00'),(51,NULL,1,'503','Prime d\'assiduit├®','Fixe',300.00,'Gain standard',1,'61711000','Imposable','Imposable','R├¿glement int├®rieur ou contrat d├®finissant les conditions de pr├®sence',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','R├¿glement int├®rieur / contrat','2025-10-01','ASSID',1,0,1,1,1,'2026-08-05 13:43:00'),(52,NULL,1,'504','Prime de nuit','Fixe',250.00,'Gain standard',1,'61711000','Imposable','Imposable','Planning / pointage justifiant les heures de nuit effectu├®es',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','Convention collective / contrat','2025-10-01','NUIT',1,0,1,1,1,'2026-08-05 13:43:00'),(53,NULL,1,'505','13├¿me mois (prorata)','Proportionnel',8.33,'Gain standard',1,'61711000','Imposable','Imposable','Convention collective ou usage d\'entreprise',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','Convention collective','2025-10-01','13EME',0,1,1,1,1,'2026-08-05 13:43:00'),(54,NULL,1,'330','Indemnit├® de transport urbain','Fixe',500.00,'Transport & D├®placement',0,'61713000','500.00 DH / mois','500.00 DH / mois','Lieu de travail situ├® au milieu urbain de la ville',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(55,NULL,1,'331','Indemnit├® de repr├®sentation','Proportionnel',10.00,'Sp├®cifiques ├á certains emplois',0,'61713000','10% du salaire de base','10% du salaire de base','Poste de direction, d\'encadrement sup├®rieur ou ├®quivalent',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(56,NULL,1,'334','Indemnit├® kilom├®trique','Fixe',0.00,'Transport & D├®placement',0,'61713000','3 DH / KM','3 DH / KM','Carnet de bord, carte grise au nom du salari├®, trajet < 50 KM',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(57,NULL,1,'337','Indemnit├® de tourn├®e','Fixe',1500.00,'Transport & D├®placement',0,'61713000','1 500.00 DH / mois','1 500.00 DH / mois','P├®rim├¿tre de d├®placement limit├® ├á 50 KM, planning de tourn├®e',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(58,NULL,1,'339','Indemnit├® de d├®placement justifi├®e','Fixe',0.00,'Transport & D├®placement',0,'61713000','Nourriture (10x SMIG hor.), H├®bergement (30x SMIG hor.)','Totalement exon├®r├® si justifi├®','Pi├¿ces justificatives (factures, tickets, ordre de mission)',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(59,NULL,1,'340','Indemnit├® de d├®placement forfaitaire ponctuelle','Fixe',0.00,'Transport & D├®placement',0,'61713000','Nourriture (10x SMIG hor.), H├®bergement (30x SMIG hor.)','Repas: 171 DH/j, H├®bergement: 513 DH/nuit','Ordre de mission stipulant la nature ponctuelle',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(60,NULL,1,'341','Indemnit├® de d├®placement forfaitaire r├®guli├¿re','Fixe',5000.00,'Transport & D├®placement',0,'61713000','<= 5000 DH et <= Salaire de base','Exon├®ration dans la limite de 100% du S.B. (max 5000 DH/mois)','D├®placements professionnels hors p├®rim├¿tre urbain (> 50 km)',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(61,NULL,1,'342','Indemnit├® de transport hors urbain','Fixe',750.00,'Transport & D├®placement',0,'61713000','750.00 DH / mois','750.00 DH / mois','Lieu de travail situ├® en dehors du milieu urbain',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(62,NULL,1,'343','Prime d\'outillage','Fixe',100.00,'Sp├®cifiques ├á certains emplois',0,'61713000','100 DH / mois','119.70 DH / 26 jours de travail','Le salari├® doit ├¬tre propri├®taire de ses propres ├®quipements',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(63,NULL,1,'344','Prime de salissure','Fixe',210.00,'Sp├®cifiques ├á certains emplois',0,'61713000','210 DH / mois','239.40 DH / 26 jours de travail','Travaux salissants / insalubres (bleu de travail requis)',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(64,NULL,1,'345','Prime d\'usure de v├¬tements / Tenue','Fixe',0.00,'Sp├®cifiques ├á certains emplois',0,'61713000','Frais r├®els ou bar├¿me interne','Exon├®r├® si port obligatoire pour le service','Obligation contractuelle ou r├¿glement int├®rieur',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(65,NULL,1,'346','Indemnit├® de panier / Panier de nuit','Fixe',0.00,'Sp├®cifiques ├á certains emplois',0,'61713000','2x SMIG horaire par jour','Exon├®ration selon plafond l├®gal en vigueur','Horaires de nuit ou travail continu sans coupure',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(66,NULL,1,'347','Indemnit├® de p├®nibilit├®','Fixe',0.00,'Sp├®cifiques ├á certains emplois',0,'61713000','Selon convention collective','Exon├®r├® sous r├®serve d\'un cadre r├®glement├®','Attestation de conditions de travail p├®nibles',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(67,NULL,1,'348','Indemnit├® de risque / Danger','Fixe',0.00,'Sp├®cifiques ├á certains emplois',0,'61713000','Selon bar├¿me sectoriel','Exon├®r├® si le risque est inh├®rent ├á la fonction','Fiche de poste, rapport d\'├®valuation des risques',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(68,NULL,1,'349','Indemnit├® d\'astreinte','Fixe',0.00,'Sp├®cifiques ├á certains emplois',0,'61713000','Selon convention collective','Exon├®r├® si li├®e ├á des interventions urgentes hors horaires','Planning d\'astreinte et rapports d\'intervention',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(69,NULL,1,'350','Indemnit├® de garde','Fixe',0.00,'Sp├®cifiques ├á certains emplois',0,'61713000','Bar├¿me interne conventionn├®','Exon├®r├® dans le cadre m├®dical ou de s├®curit├®','Registre des gardes effectu├®es',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(70,NULL,1,'351','Voiture de fonction ou de service','Fixe',0.00,'Transport & D├®placement',0,'61713000','Charges support├®es par l\'entreprise','Totalement exon├®r├®','Usage strictement professionnel ou convention d\'affectation',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(71,NULL,1,'352','Indemnit├® de voyage ├á l\'├®tranger','Fixe',0.00,'Transport & D├®placement',0,'61713000','Frais r├®els justifi├®s','Frais r├®els sur justificatifs ou bar├¿me officiel','Ordre de mission international, billets, factures h├┤tel',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(72,NULL,1,'353','Indemnit├® de d├®m├®nagement / mutation','Fixe',0.00,'Transport & D├®placement',0,'61713000','Frais r├®els sur factures','Exon├®r├® si requis par l\'employeur','D├®cision de mutation, factures du d├®m├®nageur',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(73,NULL,1,'354','Allocations familiales additionnelles','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Plafond l├®gal CNSS','Totalement exon├®r├®','Livret de famille, attestation de non-paiement par ailleurs',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(74,NULL,1,'355','Allocation de naissance','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Bar├¿me interne raisonnable','Exon├®r├® si ponctuel','Extrait d\'acte de naissance du nouveau-n├®',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(75,NULL,1,'356','Allocation de mariage','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Bar├¿me social de l\'entreprise','Exon├®r├® si ponctuel','Acte de mariage adoul├® ou officiel',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(76,NULL,1,'357','Allocation de d├®c├¿s / Obs├¿ques','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Frais r├®els ou forfait social','Totalement exon├®r├®','Certificat de d├®c├¿s du conjoint ou d\'un ascendant/descendant direct',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(77,NULL,1,'358','Prime de scolarit├® / Rentr├®e scolaire','Fixe',400.00,'Caract├¿re Social & Familial',0,'61712000','Plafond par enfant/an','Exon├®r├® si attribu├® aux enfants ├á charge','Certificat de scolarit├® annuel',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(78,NULL,1,'359','Bons d\'achat / Cadeaux de fin d\'ann├®e','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Plafond annuel (ex: 10% SMIG)','Exon├®r├® dans la limite du plafond social','Distribution g├®n├®rale ├á l\'occasion de f├¬tes (A├»d, Achoura, etc.)',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(79,NULL,1,'360','Indemnit├® de caisse (responsabilit├® p├®cuniaire)','Fixe',190.00,'Sp├®cifiques ├á certains emplois',0,'61713000','190 DH / mois','239.40 DH / 26 jours de travail','Poste de caissier ou manipulation effective de fonds',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(80,NULL,1,'361','Subvention de cantine / Titres repas','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Plafond par ticket / jour','Exon├®r├® selon la quote-part patronale r├®glementaire','Factures du prestataire de restauration ou ├®metteur de titres',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(81,NULL,1,'362','Prise en charge des frais m├®dicaux','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Sur dossier m├®dical','Exon├®r├® si g├®r├® par le fonds social / mutuelle','D├®compte AMO/Mutuelle et ordonnances rest├®es ├á charge',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(82,NULL,1,'363','Aide aux vacances / Estivage','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Plafond annuel fixe','Exon├®r├® si g├®r├® via les ┼ôuvres sociales (COS)','Factures d\'organismes de vacances ou convention COS',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(83,NULL,1,'364','Secours exceptionnel / Social','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Forfait ponctuel motiv├®','Exon├®r├® si situation de pr├®carit├® av├®r├®e','Dossier d\'assistante sociale ou justificatifs de force majeure',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(84,NULL,1,'365','Bourses d\'├®tudes pour les enfants','Fixe',0.00,'Caract├¿re Social & Familial',0,'61712000','Selon m├®rite et crit├¿res sociaux','Exon├®r├® si vers├® directement ├á l\'├®tablissement','Facture de l\'├®cole/universit├®, attestation de r├®ussite',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(85,NULL,1,'366','Indemnit├® l├®gale de licenciement','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Bar├¿me du Code du Travail','Totalement exon├®r├®e de CNSS et DGI','Lettre de licenciement, PV de l\'inspecteur du travail / tribunal',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(86,NULL,1,'367','Indemnit├® de licenciement abusive','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Fix├®e par tribunal ou conciliation','Exon├®r├®e selon la limite l├®gale ou judiciaire','Jugement d├®finitif ou PV de conciliation l├®galis├®',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(87,NULL,1,'368','Indemnit├® de d├®part volontaire / Retraite','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Plafonds selon bar├¿me l├®gal','Exon├®r├®e sous conditions de l\'accord DGI/CNSS','Convention de d├®part volontaire sign├®e et l├®galis├®e',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(88,NULL,1,'369','Indemnit├® de pr├®avis (dispens├®)','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Montant correspondant aux salaires','Assujettie sauf cas sp├®cifiques d\'exon├®ration globale','Lettre de dispense de pr├®avis',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(89,NULL,1,'370','Prime de fin de carri├¿re','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Selon convention collective','Exon├®r├®e si assimil├®e ├á l\'indemnit├® de d├®part','Notification de mise ├á la retraite',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(90,NULL,1,'371','Indemnit├® compensatrice de logement','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Frais r├®els ou bar├¿me','Exon├®r├®e si int├®gr├®e aux dommages et int├®r├¬ts','Protocole d\'accord transactionnel',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(91,NULL,1,'372','Indemnit├® de non-concurrence','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Fix├®e par contrat','Exon├®r├®e si qualifi├®e de dommages et int├®r├¬ts','Clause contractuelle et re├ºu pour solde de tout compte',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(92,NULL,1,'373','Indemnit├® de client├¿le (VRP)','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Selon pr├®judice commercial','Exon├®r├®e selon le Code du Travail','Calcul de la perte de client├¿le valid├® par expert/tribunal',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(93,NULL,1,'374','Indemnit├® de reconversion professionnelle','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Prise en charge de la formation','Exon├®r├®e si vers├®e au centre de formation','Facture du centre de formation, plan de sauvegarde de l\'emploi',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(94,NULL,1,'375','Indemnit├® de ch├┤mage technique / Partiel','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Selon autorisations r├®glementaires','Exon├®r├®e en p├®riode de crise majeure','Autorisation du gouverneur ou d├®cision minist├®rielle',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(95,NULL,1,'376','Indemnit├® transactionnelle globale','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Limite des dommages l├®gaux','Exon├®r├®e ├á hauteur des plafonds l├®gaux','Protocole de transaction enregistr├® aupr├¿s des autorit├®s',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00'),(96,NULL,1,'377','Prime de tutorat / Fin de projet','Fixe',0.00,'Rupture & Fin de Contrat',0,'61713000','Forfait contractuel','Exon├®r├® si li├® ├á un transfert d\'outils de fin de contrat','Rapport de fin de mission valid├® par l\'entreprise',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Code du Travail / Arr├¬t├® n┬░ 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-05 13:43:00');
/*!40000 ALTER TABLE `rubriques_gains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rubriques_retenues`
--

DROP TABLE IF EXISTS `rubriques_retenues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rubriques_retenues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned DEFAULT NULL,
  `is_global` tinyint(1) NOT NULL DEFAULT 0,
  `code` varchar(20) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `type_montant` enum('Fixe','Proportionnel') NOT NULL DEFAULT 'Fixe',
  `valeur_defaut` decimal(10,2) NOT NULL DEFAULT 0.00,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `rubriques_retenues_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rubriques_retenues`
--

LOCK TABLES `rubriques_retenues` WRITE;
/*!40000 ALTER TABLE `rubriques_retenues` DISABLE KEYS */;
INSERT INTO `rubriques_retenues` VALUES (1,NULL,1,'801','Avance sur salaire','Fixe',0.00,1,'2026-08-05 13:34:55'),(2,NULL,1,'802','Pr├¬t personnel','Fixe',0.00,1,'2026-08-05 13:34:55'),(3,NULL,1,'803','Pr├¬t logement','Fixe',0.00,1,'2026-08-05 13:34:55'),(4,NULL,1,'804','Cotisation syndicale','Fixe',0.00,1,'2026-08-05 13:34:55'),(5,NULL,1,'805','Pension alimentaire','Fixe',0.00,1,'2026-08-05 13:34:55'),(6,NULL,1,'806','Saisie-arr├¬t','Fixe',0.00,1,'2026-08-05 13:34:55'),(7,NULL,1,'801','Avance sur salaire','Fixe',0.00,1,'2026-08-05 14:46:26'),(8,NULL,1,'802','Pr├¬t personnel','Fixe',0.00,1,'2026-08-05 14:46:26'),(9,NULL,1,'803','Pr├¬t logement','Fixe',0.00,1,'2026-08-05 14:46:26'),(10,NULL,1,'804','Cotisation syndicale','Fixe',0.00,1,'2026-08-05 14:46:26'),(11,NULL,1,'805','Pension alimentaire','Fixe',0.00,1,'2026-08-05 14:46:26'),(12,NULL,1,'806','Saisie-arr├¬t','Fixe',0.00,1,'2026-08-05 14:46:26'),(13,NULL,1,'801','Avance sur salaire','Fixe',0.00,1,'2026-08-05 14:48:30'),(14,NULL,1,'802','Pr├¬t personnel','Fixe',0.00,1,'2026-08-05 14:48:30'),(15,NULL,1,'803','Pr├¬t logement','Fixe',0.00,1,'2026-08-05 14:48:30'),(16,NULL,1,'804','Cotisation syndicale','Fixe',0.00,1,'2026-08-05 14:48:30'),(17,NULL,1,'805','Pension alimentaire','Fixe',0.00,1,'2026-08-05 14:48:30'),(18,NULL,1,'806','Saisie-arr├¬t','Fixe',0.00,1,'2026-08-05 14:48:30'),(19,NULL,1,'801','Avance sur salaire','Fixe',0.00,1,'2026-08-05 14:49:42'),(20,NULL,1,'802','Pr├¬t personnel','Fixe',0.00,1,'2026-08-05 14:49:42'),(21,NULL,1,'803','Pr├¬t logement','Fixe',0.00,1,'2026-08-05 14:49:42'),(22,NULL,1,'804','Cotisation syndicale','Fixe',0.00,1,'2026-08-05 14:49:42'),(23,NULL,1,'805','Pension alimentaire','Fixe',0.00,1,'2026-08-05 14:49:42'),(24,NULL,1,'806','Saisie-arr├¬t','Fixe',0.00,1,'2026-08-05 14:49:42');
/*!40000 ALTER TABLE `rubriques_retenues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salarie_gains`
--

DROP TABLE IF EXISTS `salarie_gains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salarie_gains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salarie_id` int(10) unsigned NOT NULL,
  `rubrique_id` int(10) unsigned NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 0.00,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_salarie_rubrique` (`salarie_id`,`rubrique_id`),
  KEY `rubrique_id` (`rubrique_id`),
  CONSTRAINT `salarie_gains_ibfk_1` FOREIGN KEY (`salarie_id`) REFERENCES `salaries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salarie_gains_ibfk_2` FOREIGN KEY (`rubrique_id`) REFERENCES `rubriques_gains` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salarie_gains`
--

LOCK TABLES `salarie_gains` WRITE;
/*!40000 ALTER TABLE `salarie_gains` DISABLE KEYS */;
INSERT INTO `salarie_gains` VALUES (1,1,53,0.00,1,'2026-08-05 13:35:05'),(2,1,51,0.00,1,'2026-08-05 13:35:05'),(3,2,53,0.00,1,'2026-08-05 13:35:05'),(4,2,51,0.00,1,'2026-08-05 13:35:05');
/*!40000 ALTER TABLE `salarie_gains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salarie_indemnites`
--

DROP TABLE IF EXISTS `salarie_indemnites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salarie_indemnites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salarie_id` int(10) unsigned NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 0.00,
  `plafond_dgi` decimal(10,2) DEFAULT NULL,
  `plafond_cnss` decimal(10,2) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `salarie_id` (`salarie_id`),
  CONSTRAINT `salarie_indemnites_ibfk_1` FOREIGN KEY (`salarie_id`) REFERENCES `salaries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salarie_indemnites`
--

LOCK TABLES `salarie_indemnites` WRITE;
/*!40000 ALTER TABLE `salarie_indemnites` DISABLE KEYS */;
/*!40000 ALTER TABLE `salarie_indemnites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salaries`
--

DROP TABLE IF EXISTS `salaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salaries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `service_id` int(10) unsigned DEFAULT NULL,
  `fonction_id` int(10) unsigned DEFAULT NULL,
  `matricule` varchar(20) NOT NULL,
  `nom_famille` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `adresse` text DEFAULT NULL,
  `sexe` enum('M','F') DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `date_sortie` date DEFAULT NULL,
  `cin` varchar(255) DEFAULT NULL,
  `cnss` varchar(20) DEFAULT NULL,
  `situation_familiale` enum('celibataire','marie','divorce','veuf') NOT NULL DEFAULT 'celibataire',
  `nb_enfants` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `enfants_a_charge` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `personnes_a_charge` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `poste` varchar(150) DEFAULT NULL,
  `type_contrat` enum('CDI','CDD','stage','interim','anapec','tahfiz') NOT NULL DEFAULT 'CDI',
  `salaire_base` decimal(10,2) NOT NULL DEFAULT 0.00,
  `type_salaire` enum('mensuel','horaire','journalier') NOT NULL DEFAULT 'mensuel',
  `frequence_paiement` enum('mensuel','quinzaine','hebdomadaire') NOT NULL DEFAULT 'mensuel',
  `mode_paiement` enum('virement','cheque','especes') NOT NULL DEFAULT 'virement',
  `rib` varchar(255) DEFAULT NULL,
  `indemnite_transport` decimal(10,2) NOT NULL DEFAULT 500.00,
  `indemnite_panier` decimal(10,2) NOT NULL DEFAULT 780.00,
  `indemnite_representation` decimal(10,2) NOT NULL DEFAULT 0.00,
  `avantage_logement` decimal(10,2) NOT NULL DEFAULT 0.00,
  `avances_salaire` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mutuelle` decimal(10,2) NOT NULL DEFAULT 0.00,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  KEY `service_id` (`service_id`),
  KEY `fonction_id` (`fonction_id`),
  CONSTRAINT `salaries_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salaries_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salaries_ibfk_3` FOREIGN KEY (`fonction_id`) REFERENCES `fonctions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salaries`
--

LOCK TABLES `salaries` WRITE;
/*!40000 ALTER TABLE `salaries` DISABLE KEYS */;
INSERT INTO `salaries` VALUES (1,1,2,3,'TMS001','Benali','Karim','15 Rue Atlas, Casablanca','M','1990-03-15','Casablanca','2022-01-10',NULL,'AB123456','CNSS2001','marie',2,2,2,'D├®veloppeur Full Stack','CDI',15000.00,'mensuel','mensuel','virement','RIB001',500.00,780.00,1500.00,0.00,0.00,0.00,1,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(2,1,1,2,'TMS002','Alaoui','Fatima','22 Avenue Hassan II, Casablanca','F','1985-07-22','Casablanca','2020-06-01',NULL,'CD234567','CNSS2002','marie',3,3,3,'Directeur Administratif','CDI',22000.00,'mensuel','mensuel','virement','RIB002',500.00,780.00,2200.00,0.00,0.00,0.00,1,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(3,1,2,3,'TMS003','Idrissi','Youssef','8 Rue des Oliviers, Casablanca','M','1995-11-08','Casablanca','2023-03-15',NULL,'EF345678','CNSS2003','celibataire',0,0,0,'D├®veloppeur Full Stack','CDI',12000.00,'mensuel','mensuel','virement','RIB003',500.00,780.00,0.00,0.00,0.00,0.00,1,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(4,1,3,6,'TMS004','Bennani','Sara','5 Boulevard Mohammed VI, Casablanca','F','1992-09-30','Casablanca','2021-09-01',NULL,'GH456789','CNSS2004','celibataire',0,0,0,'Gestionnaire Paie','CDI',13000.00,'mensuel','mensuel','virement','RIB004',500.00,780.00,1000.00,0.00,0.00,0.00,1,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(5,1,5,9,'TMS005','Ouazzani','Hicham','12 Rue de la Gare, Casablanca','M','1988-05-12','Casablanca','2019-11-20',NULL,'IJ567890','CNSS2005','marie',4,4,4,'Commercial Senior','CDI',28000.00,'mensuel','mensuel','virement','RIB005',500.00,780.00,2800.00,0.00,0.00,0.00,1,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(6,1,4,7,'TMS006','El Amrani','Nadia','30 Rue Oued Zem, Casablanca','F','1997-01-25','Casablanca','2024-02-01',NULL,'KL678901','CNSS2006','celibataire',0,0,0,'Comptable','CDD',9500.00,'mensuel','mensuel','virement','RIB006',500.00,780.00,0.00,0.00,0.00,0.00,1,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(7,3,7,17,'2','TOUCHOUNI','MOHAMED','LOT BEAUSSITE NO 22 AIN SEBAA','M',NULL,NULL,'2020-07-01',NULL,'gsUdi9DDe/NmjDKX+blms1lkc09xL1hJL1M5aEdHWWgxOER1TEE9PQ==','195549511','celibataire',0,0,0,'RESPONSABLE','CDI',17497.97,'mensuel','mensuel','virement',NULL,500.00,780.00,1749.80,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(8,3,7,18,'1','ZIRI','MOHAMED','HAY MLY RACHID GR 3 RUE 18 N 13','M',NULL,NULL,'2020-07-01',NULL,'r5zXT7avzW1/3EsA3hFHm1ZMckZ1WFNGQ2w3WjF0ZUNNVHNpcFE9PQ==','55252525','marie',2,2,3,'TECHNICIEN','CDI',7143.85,'mensuel','mensuel','virement','',0.00,0.00,714.39,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:47:04'),(9,3,7,17,'24','LAAMRI','REDA','LOT ADDOHA 1 IMM A 67 ETG 2 APPT 10 AIN SEBAA CASABLANCA','M',NULL,NULL,'2025-11-01',NULL,'pedOSdoKMDTXswM+UqVFEGQ1SzdONjRwOG5WRkMxNkZUYWs3aGc9PQ==','170881159','celibataire',0,0,0,'RESPONSABLE','CDI',5361.46,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(10,3,7,19,'6','ELSADIK','CHAIMAA','HAY MOULAY RACHID BOURNAZIL EMM 22.B.10','F',NULL,NULL,'2020-11-01',NULL,'FbFGoMfScYcjcOsK5SEjgTBBZVpSQnVUOW12bXJsQys3eDFnYVE9PQ==','119133710','celibataire',0,0,0,'ASSISTANTE ADMINISTRATIVE','CDI',4611.00,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(11,3,7,20,'19','AZOUAGH','MOHAMED','RES OULMES IM 5 N 2 SIDIMOUMEN SIDI BERNOUSSI CASA','M',NULL,NULL,'2024-02-01',NULL,'mDSLojZ6nVcdq+YAFhwLIkFIclFrSmUzclFYd0hkcnZIZlpsTlE9PQ==','143865081','celibataire',0,0,0,'MAGAZINIER','CDI',3752.66,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(12,3,7,21,'11','BEJNE','ABDELOUAHD','DR BAJA OUAD EL BOUR IMINTANOUTE','M',NULL,NULL,'2022-06-06',NULL,'gAw0RZ3dC114yBHDQ4s7KC9rcEhWdXBNdjVhYkJ5blVTWklKRGc9PQ==','154598945','celibataire',0,0,0,'OUVRIER','CDI',3577.95,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(13,3,7,21,'7','ZAIRI','FOUAD','HAY ISELAHE 02 AHLAF BENSLIMANE','M',NULL,NULL,'2020-08-24',NULL,'jJdBtJpu5GnAWmOQW1nn6XpTSkZhZDJTOEY3VzgzMEEzQWEvRUE9PQ==','152512020','celibataire',0,0,0,'OUVRIER','CDI',3470.26,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(14,3,7,22,'9','HAKIM','BOUCHAIB','LOT AL FADL GH 2 IMB 3 N 28 ETG 4 MLY RACHID','M',NULL,NULL,'2021-03-01',NULL,'q47VhgQG54znczuxXBkP5WlDbXh0akxOMllNNmRLSWZsNlZkSHc9PQ==','173876694','celibataire',0,0,0,'CHEFFEUR','CDI',3430.29,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(15,3,7,21,'13','LAKRAD','AICHA','NR 37 AIN HARROUDA','F',NULL,NULL,'2021-12-01',NULL,'U9x9xBPhReiLG3ZcEAIT53NBZEJvR2pZR0c5YVhDeUU3ZVNMK2c9PQ==','122971958','celibataire',0,0,0,'OUVRIER','CDI',3422.72,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(16,3,7,21,'15','AGROUCH','LAHOUCINE','HAY ENNOUR HR C NO 46 SIDI OTHMANE CASA','M',NULL,NULL,'2023-08-02',NULL,'AxPonMgJQCQZxYWGYMRgw3hrRVhzdEFKWUo2RC9DbDdId0xNUlE9PQ==','997126719','celibataire',0,0,0,'OUVRIER','CDI',3422.72,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(17,3,7,21,'16','EL AOUNY','LATIFA','BLOC 61 NR 39 MANSOUR 3 BERNOUSSI CASA','F',NULL,NULL,'2024-10-01',NULL,'cWbIu8kSoUbZxUcUAvE+wGphK3MzLzQzaEprTzc3b3pSOWI1ZGc9PQ==','111645168','celibataire',0,0,0,'OUVRIER','CDI',3422.72,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(18,3,7,21,'18','HARIMI','KHADIJA','RES DAIR EL ALIA GH 9 IMM 3 RDC NR 1 BENI IKHLEF MOHAMMEDIA','F',NULL,NULL,'2024-02-01',NULL,'sZyO1suOIXwdGhIr4bcsgGh3R3Z2VGg4WWQxajhKY1BlVE9sd2c9PQ==','186498256','celibataire',0,0,0,'OUVRIER','CDI',3422.72,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(19,3,7,21,'25','MOUHASSINE','HAMZA',NULL,'M',NULL,NULL,'2025-09-01',NULL,NULL,'108917567','celibataire',0,0,0,'OUVRIER','CDI',3422.72,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(20,3,7,21,'8','AMAR','HABIBA','HAY EL AMAL 1 NR 170 B AIN HARROUDA','F',NULL,NULL,'2021-07-16',NULL,'cvo0/CqF4eTfbBqL6TzSXjk3Q1hMc2ZOZWwybHgrVStwR1M2MEE9PQ==','176709126','celibataire',0,0,0,'OUVRIER','CDI',3291.09,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(21,3,7,21,'12','BASLAM','ASMAE','DR MARIA NR 26 QUARTIER INDUSTRIEL BERNOUSSI','F',NULL,NULL,'2022-06-03',NULL,'B4alYZ/wG5Du8Ti3FGKmsmZ4eXNaeUxiNml1NnJBUHA2cXoyUmc9PQ==','162720744','celibataire',0,0,0,'OUVRIER','CDI',3291.09,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(22,3,7,21,'17','EL AOUNY','FAOUZIA','BLOC 61 NR 39 MANSOUR 3 BERNOUSSI CASA','F',NULL,NULL,'2024-11-01',NULL,'N2ik5jY0XRk1SjKHzHUctDkvVC9CdjNMY2djQWV6TFo4eEY2M1E9PQ==','108206262','celibataire',0,0,0,'OUVRIER','CDI',3291.09,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(23,3,7,21,'20','MENDILI','ABDELKADER','DR LAMNADLA OULED BRAHIM SIDI HJJAJ BEN AHMED','M',NULL,NULL,'2025-07-01',NULL,'XP1DRT/Ck/WUW//lvLLcq1Z3cG1iYjBNbkNDTkdOeldhdmJzSlE9PQ==','100315563','celibataire',0,0,0,'OUVRIER','CDI',3159.46,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(24,3,7,NULL,'5','BOURI','LATIFA','DR ZAOUIA SIDI ABDELLAH OLD TMIME','F',NULL,NULL,'2022-06-03',NULL,'lwmUuE7+Rp+gR5dpBIQktGdTMS9LUjRCVm1JNDJGWXNNVFJSbEE9PQ==','164942746','celibataire',0,0,0,NULL,'CDI',3027.83,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29'),(25,3,7,21,'23','EL BAHOUI','REDOUANE','DOUAR AIT MIMOUN BIR TAM TAM SEFROU','M',NULL,NULL,'2025-10-01',NULL,'dWHyQMDA+1hOB2F3Q3M6EXF2WXhxZGdQNm1RNklBTEg1aW5kTFE9PQ==','138017717','celibataire',0,0,0,'OUVRIER','CDI',3027.83,'mensuel','mensuel','virement',NULL,0.00,0.00,0.00,0.00,0.00,0.00,1,'2026-08-05 15:31:29','2026-08-05 15:31:29');
/*!40000 ALTER TABLE `salaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `societe_id` int(10) unsigned NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `societe_id` (`societe_id`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`societe_id`) REFERENCES `societes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,1,'Direction G├®n├®rale',NULL,1,'2026-08-05 13:35:05'),(2,1,'Informatique',NULL,1,'2026-08-05 13:35:05'),(3,1,'Ressources Humaines',NULL,1,'2026-08-05 13:35:05'),(4,1,'Comptabilit├®',NULL,1,'2026-08-05 13:35:05'),(5,1,'Commercial',NULL,1,'2026-08-05 13:35:05'),(7,3,'Production',NULL,1,'2026-08-05 15:31:29');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `societes`
--

DROP TABLE IF EXISTS `societes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `societes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `raison_sociale` varchar(200) NOT NULL,
  `forme_juridique` varchar(20) NOT NULL,
  `ice` varchar(20) NOT NULL,
  `if_fiscal` varchar(20) NOT NULL,
  `rc` varchar(20) NOT NULL,
  `tp` varchar(20) NOT NULL,
  `cnss` varchar(20) NOT NULL,
  `adresse` text DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `banque` varchar(100) DEFAULT NULL,
  `agence` varchar(100) DEFAULT NULL,
  `rib` varchar(255) DEFAULT NULL,
  `modele_bulletin_id` int(10) unsigned DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `damancom_login` varchar(100) DEFAULT NULL,
  `damancom_password` varchar(255) DEFAULT NULL,
  `simpl_login` varchar(100) DEFAULT NULL,
  `simpl_password` varchar(255) DEFAULT NULL,
  `cimr_login` varchar(100) DEFAULT NULL,
  `cimr_password` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `societes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `societes`
--

LOCK TABLES `societes` WRITE;
/*!40000 ALTER TABLE `societes` DISABLE KEYS */;
INSERT INTO `societes` VALUES (1,1,'TechMaroc Solutions','SARL','ICE001234567','IF123456','RC78901','TP34567','CNSS1001','12 Rue des Innovateurs, Quartier des Affaires','Casablanca','0522123456','contact@techmaroc.ma','www.techmaroc.ma','Attijariwafa Bank','Agence Anfa','0078100001234000000001234',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-08-05 13:35:05','2026-08-05 13:35:05'),(3,1,'TOUCOUPLAST','SARL','002331133000090','37697646','443977','','2001180',NULL,'CASABLANCA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-08-05 15:31:29','2026-08-05 15:31:29');
/*!40000 ALTER TABLE `societes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sources_legales`
--

DROP TABLE IF EXISTS `sources_legales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sources_legales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `libelle` varchar(200) NOT NULL,
  `type` enum('loi','decret','arrete','note','circulaire','convention') NOT NULL,
  `organisme` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `reference_bo` varchar(50) DEFAULT NULL,
  `date_publication` date DEFAULT NULL,
  `date_effet` date DEFAULT NULL,
  `url_officiel` varchar(300) DEFAULT NULL,
  `statut` enum('en_vigueur','modifie','abroge') DEFAULT 'en_vigueur',
  `ordre` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sources_legales`
--

LOCK TABLES `sources_legales` WRITE;
/*!40000 ALTER TABLE `sources_legales` DISABLE KEYS */;
INSERT INTO `sources_legales` VALUES (1,'CT','Code du Travail (Loi n┬░ 65-99)','loi','Inspection du Travail','Code du Travail marocain promulgu├® par la Loi n┬░ 65-99. R├®git les relations individuelles et collectives de travail.','BO n┬░ 5210','2003-09-11','2004-06-08',NULL,'en_vigueur',1),(2,'DAHIR_CNSS','Dahir n┬░ 1.72.184 ÔÇö R├®gime de s├®curit├® sociale','loi','CNSS','Dahir portant institution du r├®gime de s├®curit├® sociale au Maroc. Base l├®gale de la CNSS.','BO n┬░ 3120','1972-07-27','1972-10-01',NULL,'modifie',2),(3,'D266','D├®cret n┬░ 2-25-266 ÔÇö Application CNSS','decret','CNSS','D├®cret fixant les modalit├®s d\'application des dispositions relatives aux exon├®rations de cotisations CNSS.','BO n┬░ 7443','2025-04-24','2025-10-01',NULL,'en_vigueur',3),(4,'A1314','Arr├¬t├® n┬░ 1314-25 ÔÇö Indemnit├®s exon├®r├®es CNSS','arrete','CNSS','Arr├¬t├® du ministre de l\'├ëconomie et des Finances fixant la liste des indemnit├®s exon├®r├®es de cotisations CNSS.','BO n┬░ 7443','2025-05-19','2025-10-01',NULL,'en_vigueur',4),(5,'CGI','Code G├®n├®ral des Imp├┤ts','loi','DGI','Code G├®n├®ral des Imp├┤ts marocain. D├®finit les r├¿gles d\'assujettissement ├á l\'IR et ├á l\'IS.',NULL,NULL,NULL,NULL,'en_vigueur',5),(6,'N16_2017','Note n┬░ 16/2017 ÔÇö Indemnit├®s exon├®r├®es IR','note','DGI','Note circulaire de la DGI pr├®cisant la liste des indemnit├®s exon├®r├®es de l\'imp├┤t sur le revenu.',NULL,'2017-01-01','2017-01-01',NULL,'en_vigueur',6),(7,'LF','Loi de Finances','loi','DGI','Loi de Finances annuelle. Modifie chaque ann├®e les dispositions fiscales (bar├¿me IR, plafonds, etc.).',NULL,NULL,NULL,NULL,'en_vigueur',7),(8,'CCOLL','Conventions collectives sectorielles','convention','Inspection du Travail','Conventions collectives de travail applicables par secteur d\'activit├®. Peuvent pr├®voir des indemnit├®s sp├®cifiques.',NULL,NULL,NULL,NULL,'en_vigueur',8);
/*!40000 ALTER TABLE `sources_legales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','gestionnaire') NOT NULL DEFAULT 'gestionnaire',
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrateur','admin@paie-me.ma','$2y$10$DRPsKOgLy.Ib4oKPT8oX/.2gRRWXSCgQz3UdUMLbbiyYvVOnX6fhq','admin',1,'2026-08-05 13:34:55','2026-08-05 13:34:55');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'paie_me'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-05 19:00:37
