-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.3.0 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour db_mototrack
CREATE DATABASE IF NOT EXISTS `db_mototrack` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_mototrack`;

-- Listage de la structure de la table db_mototrack. adresse
CREATE TABLE IF NOT EXISTS `adresse` (
  `id` int NOT NULL AUTO_INCREMENT,
  `motard_id` int NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ville` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commune` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quartier` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avenue` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `motard_id` (`motard_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.adresse : 2 rows
/*!40000 ALTER TABLE `adresse` DISABLE KEYS */;
INSERT INTO `adresse` (`id`, `motard_id`, `province`, `ville`, `commune`, `quartier`, `avenue`, `is_active`) VALUES
	(1, 1, 'Kinshasa', 'Kinshasa', 'Lemba', 'Salongo', 'Av. 1', 1),
	(2, 2, 'Nord-Kivu', 'Goma', 'Karisimbi', 'Katoyi', 'Av. du Lac', 1);
/*!40000 ALTER TABLE `adresse` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. affectation_plaque
CREATE TABLE IF NOT EXISTS `affectation_plaque` (
  `id` int NOT NULL AUTO_INCREMENT,
  `moto_id` int NOT NULL,
  `plaque_id` int NOT NULL,
  `validated_by` int NOT NULL,
  `validated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `moto_id` (`moto_id`),
  UNIQUE KEY `plaque_id` (`plaque_id`),
  KEY `validated_by` (`validated_by`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.affectation_plaque : 2 rows
/*!40000 ALTER TABLE `affectation_plaque` DISABLE KEYS */;
INSERT INTO `affectation_plaque` (`id`, `moto_id`, `plaque_id`, `validated_by`, `validated_at`) VALUES
	(1, 1, 1, 3, '2025-12-21 13:22:22'),
	(2, 2, 2, 5, '2025-12-21 13:22:22');
/*!40000 ALTER TABLE `affectation_plaque` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. marque_moto
CREATE TABLE IF NOT EXISTS `marque_moto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.marque_moto : 4 rows
/*!40000 ALTER TABLE `marque_moto` DISABLE KEYS */;
INSERT INTO `marque_moto` (`id`, `nom`, `is_active`) VALUES
	(1, 'Honda', 1),
	(2, 'Yamaha', 1),
	(3, 'Bajaj', 1),
	(4, 'TVS', 1);
/*!40000 ALTER TABLE `marque_moto` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. motard
CREATE TABLE IF NOT EXISTS `motard` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` enum('PHYSIQUE','MORALE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_complet` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identification` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.motard : 2 rows
/*!40000 ALTER TABLE `motard` DISABLE KEYS */;
INSERT INTO `motard` (`id`, `type`, `nom_complet`, `identification`, `telephone`, `email`, `created_by`, `created_at`) VALUES
	(1, 'PHYSIQUE', 'Jean Mukendi', 'CNI12345', '099000111', NULL, 2, '2025-12-21 13:22:22'),
	(2, 'MORALE', 'Moto Express SARL', 'RCCM4567', '081222333', NULL, 4, '2025-12-21 13:22:22');
/*!40000 ALTER TABLE `motard` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. moto
CREATE TABLE IF NOT EXISTS `moto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `motard_id` int NOT NULL,
  `marque_id` int NOT NULL,
  `modele` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_chassis` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `couleur` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_id` int NOT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_chassis` (`numero_chassis`),
  KEY `motard_id` (`motard_id`),
  KEY `marque_id` (`marque_id`),
  KEY `site_id` (`site_id`),
  KEY `created_by` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.moto : 2 rows
/*!40000 ALTER TABLE `moto` DISABLE KEYS */;
INSERT INTO `moto` (`id`, `motard_id`, `marque_id`, `modele`, `numero_chassis`, `couleur`, `site_id`, `created_by`, `created_at`) VALUES
	(1, 1, 1, 'CB 125', 'CHASSIS001', 'Rouge', 1, 2, '2025-12-21 13:22:22'),
	(2, 2, 3, 'Boxer 150', 'CHASSIS002', 'Noir', 2, 4, '2025-12-21 13:22:22');
/*!40000 ALTER TABLE `moto` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. paiement
CREATE TABLE IF NOT EXISTS `paiement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `moto_id` int NOT NULL,
  `taxe_id` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `mode_paiement` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('EN_ATTENTE','VALIDE') COLLATE utf8mb4_unicode_ci DEFAULT 'VALIDE',
  `created_by` int NOT NULL,
  `validated_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `validated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moto_id` (`moto_id`),
  KEY `taxe_id` (`taxe_id`),
  KEY `created_by` (`created_by`),
  KEY `validated_by` (`validated_by`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.paiement : 6 rows
/*!40000 ALTER TABLE `paiement` DISABLE KEYS */;
INSERT INTO `paiement` (`id`, `moto_id`, `taxe_id`, `montant`, `mode_paiement`, `status`, `created_by`, `validated_by`, `created_at`, `validated_at`) VALUES
	(1, 1, 1, 17.00, 'CASH', 'VALIDE', 2, 3, '2025-12-21 13:22:22', NULL),
	(2, 1, 2, 32.00, 'CASH', 'VALIDE', 2, 3, '2025-12-21 13:22:22', NULL),
	(3, 1, 3, 5.00, 'CASH', 'VALIDE', 2, 3, '2025-12-21 13:22:22', NULL),
	(4, 2, 1, 17.00, 'MOBILE MONEY', 'VALIDE', 4, 5, '2025-12-21 13:22:22', NULL),
	(5, 2, 2, 32.00, 'MOBILE MONEY', 'VALIDE', 4, 5, '2025-12-21 13:22:22', NULL),
	(6, 2, 3, 5.00, 'MOBILE MONEY', 'VALIDE', 4, 5, '2025-12-21 13:22:22', NULL);
/*!40000 ALTER TABLE `paiement` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. plaque
CREATE TABLE IF NOT EXISTS `plaque` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('DISPONIBLE','AFFECTEE') COLLATE utf8mb4_unicode_ci DEFAULT 'DISPONIBLE',
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.plaque : 3 rows
/*!40000 ALTER TABLE `plaque` DISABLE KEYS */;
INSERT INTO `plaque` (`id`, `numero`, `statut`) VALUES
	(1, 'PLQ-001', 'AFFECTEE'),
	(2, 'PLQ-002', 'AFFECTEE'),
	(3, 'PLQ-003', 'DISPONIBLE');
/*!40000 ALTER TABLE `plaque` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. role
CREATE TABLE IF NOT EXISTS `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.role : 3 rows
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` (`id`, `code`, `label`) VALUES
	(1, 'ADMIN', 'Administrateur'),
	(2, 'ENCODEUR', 'Encodeur'),
	(3, 'VALIDATEUR', 'Validateur');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. site
CREATE TABLE IF NOT EXISTS `site` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localisation` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.site : 2 rows
/*!40000 ALTER TABLE `site` DISABLE KEYS */;
INSERT INTO `site` (`id`, `nom`, `localisation`, `is_active`) VALUES
	(1, 'Site Central', 'Kinshasa', 1),
	(2, 'Site Est', 'Goma', 1);
/*!40000 ALTER TABLE `site` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. taxe
CREATE TABLE IF NOT EXISTS `taxe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.taxe : 3 rows
/*!40000 ALTER TABLE `taxe` DISABLE KEYS */;
INSERT INTO `taxe` (`id`, `code`, `libelle`, `montant`) VALUES
	(1, 'VIGNETTE', 'Vignette annuelle', 17.00),
	(2, 'PLAQUE', 'Plaque moto', 32.00),
	(3, 'TCR', 'Taxe TCR', 5.00);
/*!40000 ALTER TABLE `taxe` ENABLE KEYS */;

-- Listage de la structure de la table db_mototrack. utilisateur
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_id` int NOT NULL,
  `role_id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `site_id` (`site_id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table db_mototrack.utilisateur : 5 rows
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` (`id`, `site_id`, `role_id`, `nom`, `email`, `password`, `is_active`, `created_at`) VALUES
	(1, 1, 1, 'Admin Général', 'admin@test.cd', 'admin123', 1, '2025-12-21 13:22:22'),
	(2, 1, 2, 'Encodeur Kin', 'encodeur@kin.cd', 'pass', 1, '2025-12-21 13:22:22'),
	(3, 1, 3, 'Validateur Kin', 'validateur@kin.cd', 'pass', 1, '2025-12-21 13:22:22'),
	(4, 2, 2, 'Encodeur Goma', 'encodeur@goma.cd', 'pass', 1, '2025-12-21 13:22:22'),
	(5, 2, 3, 'Validateur Goma', 'validateur@goma.cd', 'pass', 1, '2025-12-21 13:22:22');
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
