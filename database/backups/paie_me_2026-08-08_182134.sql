
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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
INSERT INTO `audit_log` VALUES (1,1,'login','user',1,'Connexion utilisateur','::1','2026-07-17 10:36:52'),(2,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 12:45:31'),(3,1,'calculate','periode',2,'Recalcul paies période','::1','2026-08-04 13:28:07'),(4,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 13:34:44'),(5,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-04 13:35:07'),(6,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 13:48:48'),(7,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-04 13:48:50'),(8,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-04 13:49:00'),(9,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 13:49:01'),(10,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 14:01:50'),(11,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-04 14:04:53'),(12,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 14:05:18'),(13,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-04 14:07:58'),(14,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 14:09:47'),(15,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-04 14:12:06'),(16,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 14:18:22'),(17,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-04 14:29:14'),(18,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 14:29:15'),(19,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-04 14:29:17'),(20,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 19:31:22'),(21,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 19:31:47'),(22,1,'login','user',1,'Connexion utilisateur','::1','2026-08-04 19:32:24'),(23,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-04 19:58:49'),(24,1,'login','user',1,'Connexion utilisateur','::1','2026-08-08 15:12:36'),(25,1,'login','user',1,'Connexion utilisateur','::1','2026-08-08 15:22:28'),(26,1,'sortie','salarie',7,'Sortie salarié: Benali Karim (2026-08-08 — Licenciement)','::1','2026-08-08 15:23:15'),(27,1,'reintegration','salarie',7,'Réintégration salarié: Benali Karim','::1','2026-08-08 15:23:50'),(28,1,'sortie','salarie',7,'Sortie salarié: Benali Karim (2026-08-08 — Décès)','::1','2026-08-08 15:25:31'),(29,1,'reintegration','salarie',7,'Réintégration salarié: Benali Karim','::1','2026-08-08 15:26:00'),(30,1,'create','salarie',13,'Création salarié: Test Salarie1','::1','2026-08-08 15:32:11'),(31,1,'create','salarie',14,'Création salarié: Test Salarie2','::1','2026-08-08 15:32:43'),(32,1,'sortie','salarie',13,'Sortie salarié: Test Salarie1 (2026-08-08 — Démission)','::1','2026-08-08 15:33:16'),(33,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-08 15:59:57'),(34,1,'login','user',1,'Connexion utilisateur','::1','2026-08-08 15:59:59'),(35,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-08 16:05:06'),(36,1,'login','user',1,'Connexion utilisateur','::1','2026-08-08 16:05:25'),(37,1,'create','societe',3,'Création société: Atlas Agro Industries','::1','2026-08-08 16:08:23'),(38,1,'create','salarie',15,'Création salarié: El Mansouri Youssef','::1','2026-08-08 16:09:48'),(39,1,'create','salarie',16,'Création salarié: Bennani Karim','::1','2026-08-08 16:10:38'),(40,1,'create','salarie',17,'Création salarié: Idrissi Salma','::1','2026-08-08 16:11:00'),(41,1,'create','salarie',18,'Création salarié: Tazi Omar','::1','2026-08-08 16:11:18'),(42,1,'create','periode',3,'Création période: 8/2026','::1','2026-08-08 16:11:49'),(43,1,'ajouter-salaries','periode',3,'Ajout de 4 salariés à la période','::1','2026-08-08 16:12:21'),(44,1,'update','paie',19,'Modification paie: El Mansouri Youssef','::1','2026-08-08 16:17:09'),(45,1,'update','paie',19,'Modification paie: El Mansouri Youssef','::1','2026-08-08 16:17:12'),(46,1,'recalculer','paie',19,'Recalcul paie: El Mansouri Youssef','::1','2026-08-08 16:17:12'),(47,1,'update','paie',19,'Modification paie: El Mansouri Youssef','::1','2026-08-08 16:17:18'),(48,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-08 16:31:13'),(49,1,'login','user',1,'Connexion utilisateur','::1','2026-08-08 16:31:16'),(50,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-08 17:19:29'),(51,1,'login','user',1,'Connexion utilisateur','::1','2026-08-08 17:19:32'),(52,1,'logout','user',1,'Déconnexion utilisateur','::1','2026-08-08 18:20:30');
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bareme_anciennete` WRITE;
/*!40000 ALTER TABLE `bareme_anciennete` DISABLE KEYS */;
INSERT INTO `bareme_anciennete` VALUES (13,NULL,0,2,0.00,'2026-08-04 19:52:50'),(14,NULL,2,5,5.00,'2026-08-04 19:52:50'),(15,NULL,5,10,10.00,'2026-08-04 19:52:50'),(16,NULL,10,15,15.00,'2026-08-04 19:52:50'),(17,NULL,15,20,20.00,'2026-08-04 19:52:50'),(18,NULL,20,25,25.00,'2026-08-04 19:52:50'),(19,NULL,25,99,30.00,'2026-08-04 19:52:50'),(27,2,0,2,0.00,'2026-08-04 19:55:17'),(28,2,2,5,5.00,'2026-08-04 19:55:17'),(29,2,5,10,10.00,'2026-08-04 19:55:17'),(30,2,10,15,15.00,'2026-08-04 19:55:17'),(31,2,15,20,20.00,'2026-08-04 19:55:17'),(32,2,20,25,25.00,'2026-08-04 19:55:17'),(33,2,25,99,30.00,'2026-08-04 19:55:17');
/*!40000 ALTER TABLE `bareme_anciennete` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bareme_heures_sup` WRITE;
/*!40000 ALTER TABLE `bareme_heures_sup` DISABLE KEYS */;
INSERT INTO `bareme_heures_sup` VALUES (2,2,25.00,50.00,100.00,8,'2026-08-04 13:26:50','2026-08-04 13:26:50'),(6,NULL,25.00,50.00,100.00,8,'2026-08-04 19:54:55','2026-08-04 19:54:55');
/*!40000 ALTER TABLE `bareme_heures_sup` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bareme_ir` WRITE;
/*!40000 ALTER TABLE `bareme_ir` DISABLE KEYS */;
INSERT INTO `bareme_ir` VALUES (1,0.00,3333.33,0.00,0.00,'mensuel'),(2,3333.34,5000.00,10.00,333.33,'mensuel'),(3,5000.01,6666.67,20.00,833.33,'mensuel'),(4,6666.68,8333.33,30.00,1500.00,'mensuel'),(5,8333.34,15000.00,34.00,1833.33,'mensuel'),(6,15000.01,999999.99,37.00,2283.33,'mensuel'),(7,0.00,40000.00,0.00,0.00,'annuel'),(8,40001.00,60000.00,10.00,4000.00,'annuel'),(9,60001.00,80000.00,20.00,10000.00,'annuel'),(10,80001.00,100000.00,30.00,18000.00,'annuel'),(11,100001.00,180000.00,34.00,22000.00,'annuel'),(12,180000.01,9999999.99,37.00,27400.00,'annuel');
/*!40000 ALTER TABLE `bareme_ir` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bareme_smig_smag` WRITE;
/*!40000 ALTER TABLE `bareme_smig_smag` DISABLE KEYS */;
INSERT INTO `bareme_smig_smag` VALUES (3,2,2026,'SMIG',17.92,3422.72,'2026-01-01','2026-08-04 19:38:54'),(4,2,2026,'SMAG',97.44,2533.44,'2026-04-01','2026-08-04 19:38:54'),(5,2,2021,'SMIG',14.13,2698.83,'2021-01-01','2026-08-04 19:44:23'),(6,2,2021,'SMAG',73.05,1899.30,'2021-01-01','2026-08-04 19:44:23'),(7,2,2022,'SMIG',14.81,2828.71,'2022-01-01','2026-08-04 19:44:23'),(8,2,2022,'SMAG',76.70,1994.20,'2022-01-01','2026-08-04 19:44:23'),(9,2,2023,'SMIG',15.55,2970.05,'2023-01-01','2026-08-04 19:44:23'),(10,2,2023,'SMAG',84.37,2193.62,'2023-01-01','2026-08-04 19:44:23'),(11,2,2024,'SMIG',16.29,3111.39,'2024-01-01','2026-08-04 19:44:23'),(12,2,2024,'SMAG',88.58,2303.08,'2024-01-01','2026-08-04 19:44:23'),(13,2,2025,'SMIG',17.10,3266.10,'2025-01-01','2026-08-04 19:44:23'),(14,2,2025,'SMAG',93.00,2418.00,'2025-04-01','2026-08-04 19:44:23'),(15,NULL,2021,'SMIG',14.13,2698.83,'2021-01-01','2026-08-04 19:52:50'),(16,NULL,2021,'SMAG',73.05,1899.30,'2021-01-01','2026-08-04 19:52:50'),(17,NULL,2022,'SMIG',14.81,2828.71,'2022-01-01','2026-08-04 19:52:50'),(18,NULL,2022,'SMAG',76.70,1994.20,'2022-01-01','2026-08-04 19:52:50'),(19,NULL,2023,'SMIG',15.55,2970.05,'2023-01-01','2026-08-04 19:52:50'),(20,NULL,2023,'SMAG',84.37,2193.62,'2023-01-01','2026-08-04 19:52:50'),(21,NULL,2024,'SMIG',16.29,3111.39,'2024-01-01','2026-08-04 19:52:50'),(22,NULL,2024,'SMAG',88.58,2303.08,'2024-01-01','2026-08-04 19:52:50'),(23,NULL,2025,'SMIG',17.10,3266.10,'2025-01-01','2026-08-04 19:52:50'),(24,NULL,2025,'SMAG',93.00,2418.00,'2025-04-01','2026-08-04 19:52:50'),(25,NULL,2026,'SMIG',17.92,3422.72,'2026-01-01','2026-08-04 19:52:50'),(26,NULL,2026,'SMAG',97.44,2533.44,'2026-04-01','2026-08-04 19:52:50');
/*!40000 ALTER TABLE `bareme_smig_smag` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bulletins` WRITE;
/*!40000 ALTER TABLE `bulletins` DISABLE KEYS */;
INSERT INTO `bulletins` VALUES (13,13,'TEC-00013','2026-08-04',NULL,'2026-08-04 13:28:07'),(14,14,'TEC-00014','2026-08-04',NULL,'2026-08-04 13:28:07'),(15,15,'TEC-00015','2026-08-04',NULL,'2026-08-04 13:28:07'),(16,16,'TEC-00016','2026-08-04',NULL,'2026-08-04 13:28:07'),(17,17,'TEC-00017','2026-08-04',NULL,'2026-08-04 13:28:07'),(18,18,'TEC-00018','2026-08-04',NULL,'2026-08-04 13:28:07'),(19,19,'ATL-00019','2026-08-08',NULL,'2026-08-08 16:12:21'),(20,20,'ATL-00020','2026-08-08',NULL,'2026-08-08 16:12:21'),(21,21,'ATL-00021','2026-08-08',NULL,'2026-08-08 16:12:21'),(22,22,'ATL-00022','2026-08-08',NULL,'2026-08-08 16:12:21');
/*!40000 ALTER TABLE `bulletins` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `conge_annuel` WRITE;
/*!40000 ALTER TABLE `conge_annuel` DISABLE KEYS */;
INSERT INTO `conge_annuel` VALUES (2,2,1.50,1,15,6,2,'2026-08-04 13:26:50','2026-08-04 13:26:50'),(3,3,1.50,1,15,6,2,'2026-08-08 16:57:10','2026-08-08 16:58:57');
/*!40000 ALTER TABLE `conge_annuel` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `conges` WRITE;
/*!40000 ALTER TABLE `conges` DISABLE KEYS */;
/*!40000 ALTER TABLE `conges` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `conges_soldes` WRITE;
/*!40000 ALTER TABLE `conges_soldes` DISABLE KEYS */;
/*!40000 ALTER TABLE `conges_soldes` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `droit_conge` WRITE;
/*!40000 ALTER TABLE `droit_conge` DISABLE KEYS */;
INSERT INTO `droit_conge` VALUES (1,2,0,5,1.50,0.00,'2026-08-04 13:27:06'),(2,2,5,10,1.50,1.50,'2026-08-04 13:27:06'),(3,2,10,15,1.50,3.00,'2026-08-04 13:27:06'),(4,2,15,20,1.50,4.50,'2026-08-04 13:27:06'),(5,2,20,25,1.50,6.00,'2026-08-04 13:27:06'),(6,2,25,30,1.50,7.50,'2026-08-04 13:27:06'),(7,2,30,35,1.50,9.00,'2026-08-04 13:27:06'),(8,2,35,40,1.50,10.50,'2026-08-04 13:27:06'),(9,2,40,99,1.50,12.00,'2026-08-04 13:27:06'),(28,3,0,5,1.50,0.00,'2026-08-08 16:58:57'),(29,3,5,10,1.50,1.50,'2026-08-08 16:58:57'),(30,3,10,15,1.50,3.00,'2026-08-08 16:58:57'),(31,3,15,20,1.50,4.50,'2026-08-08 16:58:57'),(32,3,20,25,1.50,6.00,'2026-08-08 16:58:57'),(33,3,25,30,1.50,7.50,'2026-08-08 16:58:57'),(34,3,30,35,1.50,9.00,'2026-08-08 16:58:57'),(35,3,35,40,1.50,10.50,'2026-08-08 16:58:57'),(36,3,40,99,1.50,12.00,'2026-08-08 16:58:57');
/*!40000 ALTER TABLE `droit_conge` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `fonctions` WRITE;
/*!40000 ALTER TABLE `fonctions` DISABLE KEYS */;
INSERT INTO `fonctions` VALUES (21,2,11,'Directeur Général',NULL,1,'2026-08-04 13:26:50'),(22,2,11,'Directeur Administratif',NULL,1,'2026-08-04 13:26:50'),(23,2,12,'Développeur Full Stack',NULL,1,'2026-08-04 13:26:50'),(24,2,12,'Administrateur Systèmes',NULL,1,'2026-08-04 13:26:50'),(25,2,13,'Responsable RH',NULL,1,'2026-08-04 13:26:50'),(26,2,13,'Gestionnaire Paie',NULL,1,'2026-08-04 13:26:50'),(27,2,14,'Comptable',NULL,1,'2026-08-04 13:26:50'),(28,2,14,'Contrôleur de Gestion',NULL,1,'2026-08-04 13:26:50'),(29,2,15,'Commercial Senior',NULL,1,'2026-08-04 13:26:50'),(30,2,15,'Assistant Commercial',NULL,1,'2026-08-04 13:26:50'),(31,3,16,'Agent de production',NULL,1,'2026-08-08 16:08:49'),(32,3,17,'Cariste',NULL,1,'2026-08-08 16:08:49'),(33,3,18,'Comptable',NULL,1,'2026-08-08 16:08:49'),(34,3,18,'Responsable RH',NULL,1,'2026-08-08 16:08:49');
/*!40000 ALTER TABLE `fonctions` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `jours_feries` WRITE;
/*!40000 ALTER TABLE `jours_feries` DISABLE KEYS */;
INSERT INTO `jours_feries` VALUES (7,2,'Jour de l\'an',1,1,'fixe',1,'2026-08-04 13:26:50'),(8,2,'Manifeste de l\'Indépendance',11,1,'fixe',1,'2026-08-04 13:26:50'),(9,2,'Fête du Trône',30,7,'fixe',1,'2026-08-04 13:26:50'),(10,2,'Fête des Oueds',14,8,'fixe',1,'2026-08-04 13:26:50'),(11,2,'Anniversaire de la Révolution',20,8,'fixe',1,'2026-08-04 13:26:50'),(12,2,'Marche Verte',6,11,'fixe',1,'2026-08-04 13:26:50');
/*!40000 ALTER TABLE `jours_feries` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `modeles_attestation` WRITE;
/*!40000 ALTER TABLE `modeles_attestation` DISABLE KEYS */;
/*!40000 ALTER TABLE `modeles_attestation` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `modeles_bulletins` WRITE;
/*!40000 ALTER TABLE `modeles_bulletins` DISABLE KEYS */;
INSERT INTO `modeles_bulletins` VALUES (1,2,'Modèle Standard Maroc','Bulletin conforme au Code du Travail marocain','{\"nom\":\"Mod\\u00e8le Standard Maroc\",\"couleur_primaire\":\"#3b82f6\",\"sections\":[{\"titre\":\"Salaire et indemnit\\u00e9s\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"100\",\"label\":\"Salaire de base\"},{\"code\":\"204\",\"label\":\"Prime d\'anciennet\\u00e9\",\"show_base\":true,\"show_taux\":true,\"conditionnel\":true},{\"code\":\"330\",\"label\":\"Indemnit\\u00e9 de transport\",\"conditionnel\":true},{\"code\":\"346\",\"label\":\"Indemnit\\u00e9 de panier\",\"conditionnel\":true},{\"code\":\"331\",\"label\":\"Indemnit\\u00e9 de repr\\u00e9sentation\",\"conditionnel\":true},{\"code\":\"340\",\"label\":\"Avantage logement\",\"conditionnel\":true},{\"code\":\"201\",\"label\":\"Heures sup. 25%\",\"show_base\":true,\"conditionnel\":true},{\"code\":\"202\",\"label\":\"Heures sup. 50%\",\"show_base\":true,\"conditionnel\":true},{\"code\":\"203\",\"label\":\"Heures sup. 100%\",\"show_base\":true,\"conditionnel\":true}],\"total\":{\"code\":\"SB\",\"label\":\"Salaire brut\"}},{\"titre\":\"Cotisations salariales\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"400\",\"label\":\"CNSS (part salariale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"410\",\"label\":\"AMO (part salariale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"420\",\"label\":\"Mutuelle\",\"conditionnel\":true},{\"code\":\"501\",\"label\":\"Frais professionnels\",\"show_base\":true,\"show_taux\":true}],\"total\":{\"code\":\"502\",\"label\":\"Salaire net imposable (SNI)\"}},{\"titre\":\"Imp\\u00f4t sur le revenu\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"600\",\"label\":\"Imp\\u00f4t sur le revenu (IR)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"601\",\"label\":\"D\\u00e9ductions charges de famille\",\"conditionnel\":true}],\"total\":null},{\"titre\":\"Cotisations patronales\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"400P\",\"label\":\"CNSS (part patronale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"410P\",\"label\":\"AMO (part patronale)\",\"show_base\":true,\"show_taux\":true}],\"total\":null}],\"net_label\":\"Net \\u00e0 payer\",\"net_color\":\"#3b82f6\",\"show_footer\":true}',1,'2026-08-04 13:27:06'),(2,3,'Modèle Standard Maroc','Bulletin conforme au Code du Travail marocain','{\"nom\":\"Mod\\u00e8le Standard Maroc\",\"couleur_primaire\":\"#3b82f6\",\"sections\":[{\"titre\":\"Salaire et indemnit\\u00e9s\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"100\",\"label\":\"Salaire de base\"},{\"code\":\"204\",\"label\":\"Prime d\'anciennet\\u00e9\",\"show_base\":true,\"show_taux\":true,\"conditionnel\":true},{\"code\":\"330\",\"label\":\"Indemnit\\u00e9 de transport\",\"conditionnel\":true},{\"code\":\"346\",\"label\":\"Indemnit\\u00e9 de panier\",\"conditionnel\":true},{\"code\":\"331\",\"label\":\"Indemnit\\u00e9 de repr\\u00e9sentation\",\"conditionnel\":true},{\"code\":\"340\",\"label\":\"Avantage logement\",\"conditionnel\":true},{\"code\":\"201\",\"label\":\"Heures sup. 25%\",\"show_base\":true,\"conditionnel\":true},{\"code\":\"202\",\"label\":\"Heures sup. 50%\",\"show_base\":true,\"conditionnel\":true},{\"code\":\"203\",\"label\":\"Heures sup. 100%\",\"show_base\":true,\"conditionnel\":true}],\"total\":{\"code\":\"SB\",\"label\":\"Salaire brut\"}},{\"titre\":\"Cotisations salariales\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"400\",\"label\":\"CNSS (part salariale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"410\",\"label\":\"AMO (part salariale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"420\",\"label\":\"Mutuelle\",\"conditionnel\":true},{\"code\":\"501\",\"label\":\"Frais professionnels\",\"show_base\":true,\"show_taux\":true}],\"total\":{\"code\":\"502\",\"label\":\"Salaire net imposable (SNI)\"}},{\"titre\":\"Imp\\u00f4t sur le revenu\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"600\",\"label\":\"Imp\\u00f4t sur le revenu (IR)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"601\",\"label\":\"D\\u00e9ductions charges de famille\",\"conditionnel\":true}],\"total\":null},{\"titre\":\"Cotisations patronales\",\"colonnes\":[\"Code\",\"Libell\\u00e9\",\"Base\",\"Taux\",\"Montant\"],\"lignes\":[{\"code\":\"400P\",\"label\":\"CNSS (part patronale)\",\"show_base\":true,\"show_taux\":true},{\"code\":\"410P\",\"label\":\"AMO (part patronale)\",\"show_base\":true,\"show_taux\":true}],\"total\":null}],\"net_label\":\"Net \\u00e0 payer\",\"net_color\":\"#3b82f6\",\"show_footer\":true}',1,'2026-08-08 17:43:39');
/*!40000 ALTER TABLE `modeles_bulletins` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `organismes` WRITE;
/*!40000 ALTER TABLE `organismes` DISABLE KEYS */;
/*!40000 ALTER TABLE `organismes` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `paie_gains` WRITE;
/*!40000 ALTER TABLE `paie_gains` DISABLE KEYS */;
/*!40000 ALTER TABLE `paie_gains` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `paie_retenues` WRITE;
/*!40000 ALTER TABLE `paie_retenues` DISABLE KEYS */;
/*!40000 ALTER TABLE `paie_retenues` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `paies` WRITE;
/*!40000 ALTER TABLE `paies` DISABLE KEYS */;
INSERT INTO `paies` VALUES (13,2,7,2,26,0.0,0.0,19676.66,750.00,18396.66,2916.70,6000.00,500.00,780.00,1500.00,0.00,0.00,0.00,1146.66,0.00,0.00,0.00,0.00,0.00,0.00,268.80,444.69,0.00,14766.47,3187.27,150.00,15775.90,0.00,15925.90,538.80,808.71,'2026-08-04 13:28:07','2026-08-04 13:28:07'),(14,2,8,2,26,0.0,0.0,28826.66,2200.00,27546.66,2916.70,6000.00,500.00,780.00,2200.00,0.00,0.00,0.00,1146.66,0.00,0.00,0.00,0.00,0.00,0.00,268.80,651.48,0.00,23709.68,6489.25,200.00,21417.13,0.00,21617.13,538.80,1184.78,'2026-08-04 13:28:07','2026-08-08 15:51:06'),(15,2,9,2,26,0.0,0.0,15026.66,600.00,13746.66,2916.70,6000.00,500.00,780.00,0.00,0.00,0.00,0.00,1146.66,0.00,0.00,0.00,0.00,0.00,0.00,268.80,339.60,0.00,10221.56,1642.00,0.00,12776.26,0.00,12776.26,538.80,617.60,'2026-08-04 13:28:07','2026-08-04 13:28:07'),(16,2,10,2,26,0.0,0.0,17076.66,650.00,15796.66,2916.70,6000.00,500.00,780.00,1000.00,0.00,0.00,0.00,1146.66,0.00,0.00,0.00,0.00,0.00,0.00,268.80,385.93,0.00,12225.23,2323.25,0.00,14098.68,0.00,14098.68,538.80,701.85,'2026-08-04 13:28:07','2026-08-04 13:28:07'),(17,2,11,2,26,0.0,0.0,36026.66,2800.00,34746.66,2916.70,6000.00,500.00,780.00,2800.00,0.00,0.00,0.00,1146.66,0.00,0.00,0.00,0.00,0.00,0.00,268.80,814.20,0.00,30746.96,9093.05,250.00,25850.61,0.00,26100.61,538.80,1480.70,'2026-08-04 13:28:07','2026-08-04 13:28:07'),(18,2,12,2,26,0.0,0.0,12401.66,475.00,11121.66,2780.42,6000.00,500.00,780.00,0.00,0.00,0.00,0.00,1146.66,0.00,0.00,0.00,0.00,0.00,0.00,268.80,280.28,0.00,7792.16,837.65,0.00,11014.93,0.00,11014.93,538.80,509.71,'2026-08-04 13:28:07','2026-08-04 13:28:07'),(19,3,15,3,10,0.0,0.0,2800.01,192.31,2115.39,740.39,2800.01,500.00,780.00,500.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,94.77,47.81,0.00,1232.42,0.00,50.00,2657.43,0.00,2707.43,251.44,86.94,'2026-08-08 16:12:21','2026-08-08 16:17:12'),(20,3,16,3,26,0.0,0.0,4280.00,250.00,2750.00,962.50,4280.00,500.00,780.00,250.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,123.20,62.15,0.00,1602.15,0.00,0.00,4094.65,0.00,4094.65,384.34,113.03,'2026-08-08 16:12:21','2026-08-08 16:12:21'),(21,3,17,3,26,0.0,0.0,11150.00,800.00,8970.00,2242.50,6000.00,600.00,850.00,900.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,268.80,202.72,0.00,6255.98,417.87,100.00,10260.61,0.00,10360.61,538.80,368.67,'2026-08-08 16:12:21','2026-08-08 16:12:21'),(22,3,18,3,26,0.0,0.0,14900.00,600.00,14100.00,2916.67,6000.00,400.00,400.00,0.00,1500.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,268.80,318.66,0.00,10595.87,1769.27,0.00,12543.27,0.00,12543.27,538.80,579.51,'2026-08-08 16:12:21','2026-08-08 16:12:21');
/*!40000 ALTER TABLE `paies` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `parametres_cnss_amo` WRITE;
/*!40000 ALTER TABLE `parametres_cnss_amo` DISABLE KEYS */;
INSERT INTO `parametres_cnss_amo` VALUES (2,2,6000.00,4.48,8.98,0.00,2.26,4.11,6.37,0.00,0.00,6.40,13.46,1.60,1.85,0.00,0.00,0.00,3.00,0.50,1.00,50.00,100.00,'2026-08-04 13:26:50','2026-08-04 13:26:50');
/*!40000 ALTER TABLE `parametres_cnss_amo` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `periodes` WRITE;
/*!40000 ALTER TABLE `periodes` DISABLE KEYS */;
INSERT INTO `periodes` VALUES (2,2,8,2026,'2026-08-01','2026-08-31',0,0.00,0.00,0.00,'2026-08-04 13:26:50','2026-08-04 13:26:50'),(3,3,8,2026,'2026-08-01','2026-08-31',0,0.00,0.00,0.00,'2026-08-08 16:11:49','2026-08-08 16:11:49');
/*!40000 ALTER TABLE `periodes` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rubrique_sources_articles` WRITE;
/*!40000 ALTER TABLE `rubrique_sources_articles` DISABLE KEYS */;
INSERT INTO `rubrique_sources_articles` VALUES (1,246,4,'Titre I','2026-07-17 10:36:07'),(2,246,5,'Art. 57-1°','2026-07-17 10:36:07'),(3,247,4,'Titre II','2026-07-17 10:36:07'),(4,247,5,'Art. 57-1°','2026-07-17 10:36:07'),(5,248,4,'Titre I','2026-07-17 10:36:07'),(6,248,5,'Art. 57-1°','2026-07-17 10:36:07'),(7,249,4,'Titre I','2026-07-17 10:36:07'),(8,249,5,'Art. 57-1°','2026-07-17 10:36:07'),(9,250,4,'Titre I','2026-07-17 10:36:07'),(10,250,5,'Art. 57-1°','2026-07-17 10:36:07'),(11,251,4,'Titre I','2026-07-17 10:36:07'),(12,251,5,'Art. 57-1°','2026-07-17 10:36:07'),(13,252,4,'Titre I','2026-07-17 10:36:07'),(14,252,5,'Art. 57-1°','2026-07-17 10:36:07'),(15,253,4,'Titre I','2026-07-17 10:36:07'),(16,253,5,'Art. 57-1°','2026-07-17 10:36:07'),(17,254,4,'Titre II','2026-07-17 10:36:07'),(18,254,5,'Art. 57-1°','2026-07-17 10:36:07'),(19,255,4,'Titre II','2026-07-17 10:36:07'),(20,255,5,'Art. 57-1°','2026-07-17 10:36:07'),(21,256,4,'Titre II','2026-07-17 10:36:07'),(22,256,5,'Art. 57-1°','2026-07-17 10:36:07'),(23,257,4,'Titre II','2026-07-17 10:36:07'),(24,257,5,'Art. 57-1°','2026-07-17 10:36:07'),(25,258,4,'Titre II','2026-07-17 10:36:07'),(26,258,5,'Art. 57-1°','2026-07-17 10:36:07'),(27,259,4,'Titre II','2026-07-17 10:36:07'),(28,259,5,'Art. 57-1°','2026-07-17 10:36:07'),(29,260,4,'Titre II','2026-07-17 10:36:07'),(30,260,5,'Art. 57-1°','2026-07-17 10:36:07'),(31,261,4,'Titre II','2026-07-17 10:36:07'),(32,261,5,'Art. 57-1°','2026-07-17 10:36:07'),(33,262,4,'Titre I','2026-07-17 10:36:07'),(34,262,5,'Art. 57-1°','2026-07-17 10:36:07'),(35,263,4,'Titre I','2026-07-17 10:36:07'),(36,263,5,'Art. 57-1°','2026-07-17 10:36:07'),(37,264,4,'Titre I','2026-07-17 10:36:07'),(38,264,5,'Art. 57-1°','2026-07-17 10:36:07'),(39,265,4,'Titre V','2026-07-17 10:36:07'),(40,265,5,'Art. 57-1°','2026-07-17 10:36:07'),(41,266,4,'Titre V','2026-07-17 10:36:07'),(42,266,5,'Art. 57-1°','2026-07-17 10:36:07'),(43,267,4,'Titre V','2026-07-17 10:36:07'),(44,267,5,'Art. 57-1°','2026-07-17 10:36:07'),(45,268,4,'Titre V','2026-07-17 10:36:07'),(46,268,5,'Art. 57-1°','2026-07-17 10:36:07'),(47,269,4,'Titre V','2026-07-17 10:36:07'),(48,269,5,'Art. 57-1°','2026-07-17 10:36:07'),(49,270,4,'Titre V','2026-07-17 10:36:07'),(50,270,5,'Art. 57-1°','2026-07-17 10:36:07'),(51,271,4,'Titre II','2026-07-17 10:36:07'),(52,271,5,'Art. 57-1°','2026-07-17 10:36:07'),(53,272,4,'Titre V','2026-07-17 10:36:07'),(54,272,5,'Art. 57-1°','2026-07-17 10:36:07'),(55,273,4,'Titre V','2026-07-17 10:36:07'),(56,273,5,'Art. 57-1°','2026-07-17 10:36:07'),(57,274,4,'Titre V','2026-07-17 10:36:07'),(58,274,5,'Art. 57-1°','2026-07-17 10:36:07'),(59,275,4,'Titre V','2026-07-17 10:36:07'),(60,275,5,'Art. 57-1°','2026-07-17 10:36:07'),(61,276,4,'Titre V','2026-07-17 10:36:07'),(62,276,5,'Art. 57-1°','2026-07-17 10:36:07'),(63,277,4,'Titre III','2026-07-17 10:36:07'),(64,277,5,'Art. 57-7°','2026-07-17 10:36:07'),(65,277,1,'Art. 53','2026-07-17 10:36:07'),(66,278,4,'Titre III','2026-07-17 10:36:07'),(67,278,5,'Art. 57-7°','2026-07-17 10:36:07'),(68,278,1,'Art. 41','2026-07-17 10:36:07'),(69,279,4,'Titre III','2026-07-17 10:36:07'),(70,279,5,'Art. 57-7°','2026-07-17 10:36:07'),(71,280,4,'Titre III','2026-07-17 10:36:07'),(72,280,1,'Art. 43','2026-07-17 10:36:07'),(73,281,4,'Titre III','2026-07-17 10:36:07'),(74,282,4,'Titre III','2026-07-17 10:36:07'),(75,283,4,'Titre III','2026-07-17 10:36:07'),(76,284,4,'Titre III','2026-07-17 10:36:07'),(77,285,4,'Titre III','2026-07-17 10:36:07'),(78,286,4,'Titre III','2026-07-17 10:36:07'),(79,287,4,'Titre III','2026-07-17 10:36:07'),(80,288,4,'Titre VII','2026-07-17 10:36:07'),(85,241,1,'Art. 345-353','2026-08-08 17:43:39'),(86,241,5,'Art. 57 (soumis)','2026-08-08 17:43:39'),(87,242,1,'Art. 345-353','2026-08-08 17:43:39'),(88,242,5,'Art. 57 (soumis)','2026-08-08 17:43:39'),(89,243,1,'Art. 345-353','2026-08-08 17:43:39'),(90,243,5,'Art. 57 (soumis)','2026-08-08 17:43:39'),(91,244,1,'Art. 345-353','2026-08-08 17:43:39'),(92,244,5,'Art. 57 (soumis)','2026-08-08 17:43:39'),(93,245,1,'Art. 345','2026-08-08 17:43:39'),(94,245,5,'Art. 57 (soumis)','2026-08-08 17:43:39');
/*!40000 ALTER TABLE `rubrique_sources_articles` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=289 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rubriques_gains` WRITE;
/*!40000 ALTER TABLE `rubriques_gains` DISABLE KEYS */;
INSERT INTO `rubriques_gains` VALUES (241,NULL,1,'501','Prime de rendement','Proportionnel',10.00,'Gain standard',1,'61711000','Imposable','Imposable','Contrat de travail / avenant définissant les objectifs et critères de rendement',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','Contrat de travail','2025-10-01','REND',1,0,1,1,1,'2026-08-08 17:43:39'),(242,NULL,1,'502','Prime d\'objectifs','Proportionnel',5.00,'Gain standard',1,'61711000','Imposable','Imposable','Contrat de travail / avenant définissant les objectifs',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','Contrat de travail','2025-10-01','OBJEC',1,0,1,1,1,'2026-08-08 17:43:39'),(243,NULL,1,'503','Prime d\'assiduité','Fixe',300.00,'Gain standard',1,'61711000','Imposable','Imposable','Règlement intérieur ou contrat définissant les conditions de présence',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','Règlement intérieur / contrat','2025-10-01','ASSID',1,0,1,1,1,'2026-08-08 17:43:39'),(244,NULL,1,'504','Prime de nuit','Fixe',250.00,'Gain standard',1,'61711000','Imposable','Imposable','Planning / pointage justifiant les heures de nuit effectuées',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','Convention collective / contrat','2025-10-01','NUIT',1,0,1,1,1,'2026-08-08 17:43:39'),(245,NULL,1,'505','13ème mois (prorata)','Proportionnel',8.33,'Gain standard',1,'61711000','Imposable','Imposable','Convention collective ou usage d\'entreprise',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61711000','Convention collective','2025-10-01','13EME',0,1,1,1,1,'2026-08-08 17:43:39'),(246,NULL,1,'330','Indemnité de transport urbain','Fixe',500.00,'Transport & Déplacement',0,'61713000','500.00 DH / mois','500.00 DH / mois','Lieu de travail situé au milieu urbain de la ville',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(247,NULL,1,'331','Indemnité de représentation','Proportionnel',10.00,'Spécifiques à certains emplois',0,'61713000','10% du salaire de base','10% du salaire de base','Poste de direction, d\'encadrement supérieur ou équivalent',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(248,NULL,1,'334','Indemnité kilométrique','Fixe',0.00,'Transport & Déplacement',0,'61713000','3 DH / KM','3 DH / KM','Carnet de bord, carte grise au nom du salarié, trajet < 50 KM',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(249,NULL,1,'337','Indemnité de tournée','Fixe',1500.00,'Transport & Déplacement',0,'61713000','1 500.00 DH / mois','1 500.00 DH / mois','Périmètre de déplacement limité à 50 KM, planning de tournée',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(250,NULL,1,'339','Indemnité de déplacement justifiée','Fixe',0.00,'Transport & Déplacement',0,'61713000','Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)','Totalement exonéré si justifié','Pièces justificatives (factures, tickets, ordre de mission)',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(251,NULL,1,'340','Indemnité de déplacement forfaitaire ponctuelle','Fixe',0.00,'Transport & Déplacement',0,'61713000','Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)','Repas: 171 DH/j, Hébergement: 513 DH/nuit','Ordre de mission stipulant la nature ponctuelle',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(252,NULL,1,'341','Indemnité de déplacement forfaitaire régulière','Fixe',5000.00,'Transport & Déplacement',0,'61713000','<= 5000 DH et <= Salaire de base','Exonération dans la limite de 100% du S.B. (max 5000 DH/mois)','Déplacements professionnels hors périmètre urbain (> 50 km)',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(253,NULL,1,'342','Indemnité de transport hors urbain','Fixe',750.00,'Transport & Déplacement',0,'61713000','750.00 DH / mois','750.00 DH / mois','Lieu de travail situé en dehors du milieu urbain',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(254,NULL,1,'343','Prime d\'outillage','Fixe',100.00,'Spécifiques à certains emplois',0,'61713000','100 DH / mois','119.70 DH / 26 jours de travail','Le salarié doit être propriétaire de ses propres équipements',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(255,NULL,1,'344','Prime de salissure','Fixe',210.00,'Spécifiques à certains emplois',0,'61713000','210 DH / mois','239.40 DH / 26 jours de travail','Travaux salissants / insalubres (bleu de travail requis)',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(256,NULL,1,'345','Prime d\'usure de vêtements / Tenue','Fixe',0.00,'Spécifiques à certains emplois',0,'61713000','Frais réels ou barème interne','Exonéré si port obligatoire pour le service','Obligation contractuelle ou règlement intérieur',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(257,NULL,1,'346','Indemnité de panier / Panier de nuit','Fixe',0.00,'Spécifiques à certains emplois',0,'61713000','2x SMIG horaire par jour','Exonération selon plafond légal en vigueur','Horaires de nuit ou travail continu sans coupure',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(258,NULL,1,'347','Indemnité de pénibilité','Fixe',0.00,'Spécifiques à certains emplois',0,'61713000','Selon convention collective','Exonéré sous réserve d\'un cadre réglementé','Attestation de conditions de travail pénibles',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(259,NULL,1,'348','Indemnité de risque / Danger','Fixe',0.00,'Spécifiques à certains emplois',0,'61713000','Selon barème sectoriel','Exonéré si le risque est inhérent à la fonction','Fiche de poste, rapport d\'évaluation des risques',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(260,NULL,1,'349','Indemnité d\'astreinte','Fixe',0.00,'Spécifiques à certains emplois',0,'61713000','Selon convention collective','Exonéré si liée à des interventions urgentes hors horaires','Planning d\'astreinte et rapports d\'intervention',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(261,NULL,1,'350','Indemnité de garde','Fixe',0.00,'Spécifiques à certains emplois',0,'61713000','Barème interne conventionné','Exonéré dans le cadre médical ou de sécurité','Registre des gardes effectuées',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(262,NULL,1,'351','Voiture de fonction ou de service','Fixe',0.00,'Transport & Déplacement',0,'61713000','Charges supportées par l\'entreprise','Totalement exonéré','Usage strictement professionnel ou convention d\'affectation',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(263,NULL,1,'352','Indemnité de voyage à l\'étranger','Fixe',0.00,'Transport & Déplacement',0,'61713000','Frais réels justifiés','Frais réels sur justificatifs ou barème officiel','Ordre de mission international, billets, factures hôtel',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(264,NULL,1,'353','Indemnité de déménagement / mutation','Fixe',0.00,'Transport & Déplacement',0,'61713000','Frais réels sur factures','Exonéré si requis par l\'employeur','Décision de mutation, factures du déménageur',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(265,NULL,1,'354','Allocations familiales additionnelles','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Plafond légal CNSS','Totalement exonéré','Livret de famille, attestation de non-paiement par ailleurs',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(266,NULL,1,'355','Allocation de naissance','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Barème interne raisonnable','Exonéré si ponctuel','Extrait d\'acte de naissance du nouveau-né',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(267,NULL,1,'356','Allocation de mariage','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Barème social de l\'entreprise','Exonéré si ponctuel','Acte de mariage adoulé ou officiel',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(268,NULL,1,'357','Allocation de décès / Obsèques','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Frais réels ou forfait social','Totalement exonéré','Certificat de décès du conjoint ou d\'un ascendant/descendant direct',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(269,NULL,1,'358','Prime de scolarité / Rentrée scolaire','Fixe',400.00,'Caractère Social & Familial',0,'61712000','Plafond par enfant/an','Exonéré si attribué aux enfants à charge','Certificat de scolarité annuel',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(270,NULL,1,'359','Bons d\'achat / Cadeaux de fin d\'année','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Plafond annuel (ex: 10% SMIG)','Exonéré dans la limite du plafond social','Distribution générale à l\'occasion de fêtes (Aïd, Achoura, etc.)',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(271,NULL,1,'360','Indemnité de caisse (responsabilité pécuniaire)','Fixe',190.00,'Spécifiques à certains emplois',0,'61713000','190 DH / mois','239.40 DH / 26 jours de travail','Poste de caissier ou manipulation effective de fonds',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(272,NULL,1,'361','Subvention de cantine / Titres repas','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Plafond par ticket / jour','Exonéré selon la quote-part patronale réglementaire','Factures du prestataire de restauration ou émetteur de titres',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(273,NULL,1,'362','Prise en charge des frais médicaux','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Sur dossier médical','Exonéré si géré par le fonds social / mutuelle','Décompte AMO/Mutuelle et ordonnances restées à charge',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(274,NULL,1,'363','Aide aux vacances / Estivage','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Plafond annuel fixe','Exonéré si géré via les œuvres sociales (COS)','Factures d\'organismes de vacances ou convention COS',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(275,NULL,1,'364','Secours exceptionnel / Social','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Forfait ponctuel motivé','Exonéré si situation de précarité avérée','Dossier d\'assistante sociale ou justificatifs de force majeure',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(276,NULL,1,'365','Bourses d\'études pour les enfants','Fixe',0.00,'Caractère Social & Familial',0,'61712000','Selon mérite et critères sociaux','Exonéré si versé directement à l\'établissement','Facture de l\'école/université, attestation de réussite',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61712000','Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(277,NULL,1,'366','Indemnité légale de licenciement','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Barème du Code du Travail','Totalement exonérée de CNSS et DGI','Lettre de licenciement, PV de l\'inspecteur du travail / tribunal',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(278,NULL,1,'367','Indemnité de licenciement abusive','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Fixée par tribunal ou conciliation','Exonérée selon la limite légale ou judiciaire','Jugement définitif ou PV de conciliation légalisé',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(279,NULL,1,'368','Indemnité de départ volontaire / Retraite','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Plafonds selon barème légal','Exonérée sous conditions de l\'accord DGI/CNSS','Convention de départ volontaire signée et légalisée',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(280,NULL,1,'369','Indemnité de préavis (dispensé)','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Montant correspondant aux salaires','Assujettie sauf cas spécifiques d\'exonération globale','Lettre de dispense de préavis',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(281,NULL,1,'370','Prime de fin de carrière','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Selon convention collective','Exonérée si assimilée à l\'indemnité de départ','Notification de mise à la retraite',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(282,NULL,1,'371','Indemnité compensatrice de logement','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Frais réels ou barème','Exonérée si intégrée aux dommages et intérêts','Protocole d\'accord transactionnel',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(283,NULL,1,'372','Indemnité de non-concurrence','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Fixée par contrat','Exonérée si qualifiée de dommages et intérêts','Clause contractuelle et reçu pour solde de tout compte',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(284,NULL,1,'373','Indemnité de clientèle (VRP)','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Selon préjudice commercial','Exonérée selon le Code du Travail','Calcul de la perte de clientèle validé par expert/tribunal',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(285,NULL,1,'374','Indemnité de reconversion professionnelle','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Prise en charge de la formation','Exonérée si versée au centre de formation','Facture du centre de formation, plan de sauvegarde de l\'emploi',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(286,NULL,1,'375','Indemnité de chômage technique / Partiel','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Selon autorisations réglementaires','Exonérée en période de crise majeure','Autorisation du gouverneur ou décision ministérielle',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(287,NULL,1,'376','Indemnité transactionnelle globale','Fixe',0.00,'Rupture & Fin de Contrat',0,'61715000','Limite des dommages légaux','Exonérée à hauteur des plafonds légaux','Protocole de transaction enregistré auprès des autorités',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61715000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39'),(288,NULL,1,'377','Prime de tutorat / Fin de projet','Fixe',0.00,'Rupture & Fin de Contrat',0,'61713000','Forfait contractuel','Exonéré si lié à un transfert d\'outils de fin de contrat','Rapport de fin de mission validé par l\'entreprise',0,NULL,NULL,0,NULL,NULL,NULL,NULL,'61713000','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0,1,1,1,'2026-08-08 17:43:39');
/*!40000 ALTER TABLE `rubriques_gains` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rubriques_retenues` WRITE;
/*!40000 ALTER TABLE `rubriques_retenues` DISABLE KEYS */;
INSERT INTO `rubriques_retenues` VALUES (1,NULL,1,'801','Avance sur salaire','Fixe',0.00,1,'2026-07-17 10:36:07'),(2,NULL,1,'802','Prêt personnel','Fixe',0.00,1,'2026-07-17 10:36:07'),(3,NULL,1,'803','Prêt logement','Fixe',0.00,1,'2026-07-17 10:36:07'),(4,NULL,1,'804','Cotisation syndicale','Fixe',0.00,1,'2026-07-17 10:36:07'),(5,NULL,1,'805','Pension alimentaire','Fixe',0.00,1,'2026-07-17 10:36:07'),(6,NULL,1,'806','Saisie-arrêt','Fixe',0.00,1,'2026-07-17 10:36:07');
/*!40000 ALTER TABLE `rubriques_retenues` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `salarie_gains` WRITE;
/*!40000 ALTER TABLE `salarie_gains` DISABLE KEYS */;
INSERT INTO `salarie_gains` VALUES (5,7,241,0.00,1,'2026-08-04 13:26:50'),(6,7,242,0.00,1,'2026-08-04 13:26:50'),(7,8,241,0.00,1,'2026-08-04 13:26:50'),(8,8,242,0.00,1,'2026-08-04 13:26:50');
/*!40000 ALTER TABLE `salarie_gains` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `salarie_indemnites` WRITE;
/*!40000 ALTER TABLE `salarie_indemnites` DISABLE KEYS */;
/*!40000 ALTER TABLE `salarie_indemnites` ENABLE KEYS */;
UNLOCK TABLES;
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
  `motif_sortie` varchar(100) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `salaries` WRITE;
/*!40000 ALTER TABLE `salaries` DISABLE KEYS */;
INSERT INTO `salaries` VALUES (7,2,12,23,'TMS001','Benali','Karim','15 Rue Atlas, Casablanca','M','1990-03-15','Casablanca','2022-01-10',NULL,NULL,'AB123456','CNSS2001','marie',2,2,2,'Développeur Full Stack','CDI',15000.00,'mensuel','mensuel','virement','RIB001',500.00,780.00,1500.00,0.00,0.00,0.00,1,'2026-08-04 13:26:50','2026-08-08 15:26:00'),(8,2,11,22,'TMS002','Alaoui','Fatima','22 Avenue Hassan II, Casablanca','F','1985-07-22','Casablanca','2020-06-01',NULL,NULL,'CD234567','CNSS2002','marie',3,3,3,'Directeur Administratif','CDI',22000.00,'mensuel','mensuel','virement','RIB002',500.00,780.00,2200.00,0.00,0.00,0.00,1,'2026-08-04 13:26:50','2026-08-08 15:51:06'),(9,2,12,23,'TMS003','Idrissi','Youssef','8 Rue des Oliviers, Casablanca','M','1995-11-08','Casablanca','2023-03-15',NULL,NULL,'EF345678','CNSS2003','celibataire',0,0,0,'Développeur Full Stack','CDI',12000.00,'mensuel','mensuel','virement','RIB003',500.00,780.00,0.00,0.00,0.00,0.00,1,'2026-08-04 13:26:50','2026-08-04 13:26:50'),(10,2,13,26,'TMS004','Bennani','Sara','5 Boulevard Mohammed VI, Casablanca','F','1992-09-30','Casablanca','2021-09-01',NULL,NULL,'GH456789','CNSS2004','celibataire',0,0,0,'Gestionnaire Paie','CDI',13000.00,'mensuel','mensuel','virement','RIB004',500.00,780.00,1000.00,0.00,0.00,0.00,1,'2026-08-04 13:26:50','2026-08-04 13:26:50'),(11,2,15,29,'TMS005','Ouazzani','Hicham','12 Rue de la Gare, Casablanca','M','1988-05-12','Casablanca','2019-11-20',NULL,NULL,'IJ567890','CNSS2005','marie',4,4,4,'Commercial Senior','CDI',28000.00,'mensuel','mensuel','virement','RIB005',500.00,780.00,2800.00,0.00,0.00,0.00,1,'2026-08-04 13:26:50','2026-08-04 13:26:50'),(12,2,14,27,'TMS006','El Amrani','Nadia','30 Rue Oued Zem, Casablanca','F','1997-01-25','Casablanca','2024-02-01',NULL,NULL,'KL678901','CNSS2006','celibataire',0,0,0,'Comptable','CDD',9500.00,'mensuel','mensuel','virement','RIB006',500.00,780.00,0.00,0.00,0.00,0.00,1,'2026-08-04 13:26:50','2026-08-04 13:26:50'),(13,2,NULL,NULL,'TMS0007','Test','Salarie1','','M',NULL,NULL,NULL,'2026-08-08','Démission','eyPilgzDeht/BkurcuTDiHNXdzF4bi9SbFkyVkNDQUwxcW5xeUE9PQ==','','celibataire',0,0,0,'','CDI',8000.00,'mensuel','mensuel','virement','',500.00,780.00,800.00,0.00,0.00,0.00,0,'2026-08-08 15:32:11','2026-08-08 15:33:16'),(14,2,NULL,NULL,'TMS0008','Test','Salarie2','','F',NULL,NULL,NULL,NULL,NULL,'JUX1bn1m78NMXKtHbM9GvUpyQ2prYWtHYmRDSWFwUVRiME5DYVE9PQ==','','celibataire',0,0,0,'Testeur','CDI',10000.00,'mensuel','mensuel','virement','',500.00,780.00,1000.00,0.00,0.00,0.00,1,'2026-08-08 15:32:43','2026-08-08 15:32:43'),(15,3,16,31,'SAL0001','El Mansouri','Youssef','Quartier Moulay Abdellah, Rue 12','M','1990-05-12','Fès','2015-03-02',NULL,NULL,'8xosK24xmEXKxYnBGDLXVkpZSjNtZ2dkeTVzTnZESm84aGRtNXc9PQ==','CNSS30001','marie',2,0,1,'Agent de production','CDI',5000.00,'mensuel','mensuel','virement','LBC8F8l0tiDA7/DsLOsgv2ZLQitWL1BlZVlucUgvSHprWHgrdVRUVHBNbzVQVEMxL20rTjlyNkwrM3M9',500.00,780.00,500.00,0.00,0.00,0.00,1,'2026-08-08 16:09:48','2026-08-08 16:09:48'),(16,3,17,32,'SAL0002','Bennani','Karim','Quartier Oasis, Résidence Atlas, Apt 4','M','1988-11-23','Casablanca','2018-07-15',NULL,NULL,'Y35f5gNICCMPuIBKcbqbhXFLcVdsZ29SbFVIdkdrdy9mRmExWmc9PQ==','CNSS30002','celibataire',0,0,0,'Cariste','CDI',2500.00,'mensuel','mensuel','virement','0PXkrGD1qJmqv6lIRPCgNE4xN0pUWWRuM2x0a1ZReVlsT3NGcFRVM3l2N0ZXWTlML3Z0QTQ5ZVRPZ289',500.00,780.00,250.00,0.00,0.00,0.00,1,'2026-08-08 16:10:38','2026-08-08 16:10:38'),(17,3,18,33,'SAL0003','Idrissi','Salma','Hay Riad, Rue des Orangers, N°8','F','1993-09-30','Rabat','2021-01-04',NULL,NULL,'0uM07EY+dlofbsHOQ/jvNTBYV1Jtby95TThoQ2VFV1VYeDloc3c9PQ==','CNSS30003','marie',1,1,2,'Comptable','CDI',8000.00,'mensuel','mensuel','virement','jGmJ9DHLA5rOB4EoB895mFd2aFllOEhsMnNoVG0zY1JyS3JHVjF2U3hMOXV5ZXdLZ3NUMzR3T2NWS2s9',600.00,850.00,900.00,0.00,0.00,0.00,1,'2026-08-08 16:11:00','2026-08-08 16:11:00'),(18,3,18,34,'SAL0004','Tazi','Omar','Centre-ville, Avenue Mohammed V, N°23','M','1995-02-14','Tanger','2023-09-01',NULL,NULL,'Nu/G/oFrlR/vVuYPDBuqMlM1UDNrVjZjbW13SzRyblNKQVNvT3c9PQ==','CNSS30004','celibataire',0,0,0,'Responsable RH','CDD',12000.00,'mensuel','mensuel','cheque','wg8SaFc3JLqEazufg7802StTK1BiR3psSFZjUlRGU2NNM0U0VkZocWZ3bWlhSnBJUUprN0k3bEk5aEE9',400.00,400.00,0.00,1500.00,0.00,0.00,1,'2026-08-08 16:11:18','2026-08-08 16:11:18');
/*!40000 ALTER TABLE `salaries` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (11,2,'Direction Générale',NULL,1,'2026-08-04 13:26:50'),(12,2,'Informatique',NULL,1,'2026-08-04 13:26:50'),(13,2,'Ressources Humaines',NULL,1,'2026-08-04 13:26:50'),(14,2,'Comptabilité',NULL,1,'2026-08-04 13:26:50'),(15,2,'Commercial',NULL,1,'2026-08-04 13:26:50'),(16,3,'Production','Atelier production',1,'2026-08-08 16:08:49'),(17,3,'Logistique','Transport et stockage',1,'2026-08-08 16:08:49'),(18,3,'Administration','Gestion administrative',1,'2026-08-08 16:08:49');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `societes` WRITE;
/*!40000 ALTER TABLE `societes` DISABLE KEYS */;
INSERT INTO `societes` VALUES (2,1,'TechMaroc Solutions','SARL','ICE001234567','IF123456','RC78901','TP34567','CNSS1001','12 Rue des Innovateurs, Quartier des Affaires','Casablanca','0522123456','contact@techmaroc.ma','www.techmaroc.ma','Attijariwafa Bank','Agence Anfa','0078100001234000000001234',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-08-04 13:26:50','2026-08-04 13:26:50'),(3,1,'Atlas Agro Industries','SARL','002345678901234','45678910','123456','654321','CNSS-778899','Zone Industrielle Sidi Bernoussi, Lot 12','Casablanca','+212 522 98 76 54','contact@atlasagro.ma','https://www.atlasagro.ma','Attijariwafa Bank','Sidi Bernoussi','a5f0M/XSN7gE0TmzRIQuSS9qcG5xREpNVEdnZ05NSUxZcU83RmZodmJuNUYxVjZwZS92amQ0VnJkOXM9',NULL,NULL,'atlas_cnss','cnss2026!','atlas_simpl','simpl2026!','atlas_cimr','cimr2026!',1,'2026-08-08 16:08:23','2026-08-08 16:08:23');
/*!40000 ALTER TABLE `societes` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `sources_legales` WRITE;
/*!40000 ALTER TABLE `sources_legales` DISABLE KEYS */;
INSERT INTO `sources_legales` VALUES (1,'CT','Code du Travail (Loi n° 65-99)','loi','Inspection du Travail','Code du Travail marocain promulgué par la Loi n° 65-99. Régit les relations individuelles et collectives de travail.','BO n° 5210','2003-09-11','2004-06-08',NULL,'en_vigueur',1),(2,'DAHIR_CNSS','Dahir n° 1.72.184 — Régime de sécurité sociale','loi','CNSS','Dahir portant institution du régime de sécurité sociale au Maroc. Base légale de la CNSS.','BO n° 3120','1972-07-27','1972-10-01',NULL,'modifie',2),(3,'D266','Décret n° 2-25-266 — Application CNSS','decret','CNSS','Décret fixant les modalités d\'application des dispositions relatives aux exonérations de cotisations CNSS.','BO n° 7443','2025-04-24','2025-10-01',NULL,'en_vigueur',3),(4,'A1314','Arrêté n° 1314-25 — Indemnités exonérées CNSS','arrete','CNSS','Arrêté du ministre de l\'Économie et des Finances fixant la liste des indemnités exonérées de cotisations CNSS.','BO n° 7443','2025-05-19','2025-10-01',NULL,'en_vigueur',4),(5,'CGI','Code Général des Impôts','loi','DGI','Code Général des Impôts marocain. Définit les règles d\'assujettissement à l\'IR et à l\'IS.',NULL,NULL,NULL,NULL,'en_vigueur',5),(6,'N16_2017','Note n° 16/2017 — Indemnités exonérées IR','note','DGI','Note circulaire de la DGI précisant la liste des indemnités exonérées de l\'impôt sur le revenu.',NULL,'2017-01-01','2017-01-01',NULL,'en_vigueur',6),(7,'LF','Loi de Finances','loi','DGI','Loi de Finances annuelle. Modifie chaque année les dispositions fiscales (barème IR, plafonds, etc.).',NULL,NULL,NULL,NULL,'en_vigueur',7),(8,'CCOLL','Conventions collectives sectorielles','convention','Inspection du Travail','Conventions collectives de travail applicables par secteur d\'activité. Peuvent prévoir des indemnités spécifiques.',NULL,NULL,NULL,NULL,'en_vigueur',8);
/*!40000 ALTER TABLE `sources_legales` ENABLE KEYS */;
UNLOCK TABLES;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrateur','admin@paie-me.ma','$2y$10$DRPsKOgLy.Ib4oKPT8oX/.2gRRWXSCgQz3UdUMLbbiyYvVOnX6fhq','admin',1,'2026-07-17 10:36:07','2026-07-17 10:36:07');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

