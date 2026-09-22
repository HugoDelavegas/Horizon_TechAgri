-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 22 sep. 2026 à 12:47
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `plantes_spacefarm`
--

-- --------------------------------------------------------

--
-- Structure de la table `plantes`
--

DROP TABLE IF EXISTS `plantes`;
CREATE TABLE IF NOT EXISTS `plantes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_scientifique` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categorie` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temps_croissance_jours` int NOT NULL,
  `temperature_min` decimal(4,1) DEFAULT NULL,
  `temperature_max` decimal(4,1) DEFAULT NULL,
  `humidite_min` decimal(5,2) DEFAULT NULL,
  `humidite_max` decimal(5,2) DEFAULT NULL,
  `ph_min` decimal(3,1) DEFAULT NULL,
  `ph_max` decimal(3,1) DEFAULT NULL,
  `eau_l_jour_m2` decimal(5,2) DEFAULT NULL,
  `co2_ppm` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `plantes`
--

INSERT INTO `plantes` (`id`, `nom`, `nom_scientifique`, `categorie`, `temps_croissance_jours`, `temperature_min`, `temperature_max`, `humidite_min`, `humidite_max`, `ph_min`, `ph_max`, `eau_l_jour_m2`, `co2_ppm`, `description`) VALUES
(1, 'Laitue', 'Lactuca sativa', 'Legume-feuille', 30, 15.0, 22.0, 50.00, 70.00, 5.5, 6.5, 2.10, 1000, 'Plante a croissance rapide, adaptee aux systemes de culture controles.'),
(2, 'Radis', 'Raphanus sativus', 'Legume-racine', 27, 12.0, 20.0, 50.00, 70.00, 6.0, 7.0, 2.00, 1000, 'Culture tres rapide et interessante pour une ferme spatiale.'),
(3, 'Epinard', 'Spinacia oleracea', 'Legume-feuille', 40, 10.0, 21.0, 50.00, 70.00, 6.0, 7.0, 2.20, 1000, 'Legume-feuille riche en nutriments et adapte aux environnements controles.'),
(4, 'Mizuna', 'Brassica rapa var. japonica', 'Legume-feuille', 30, 12.0, 24.0, 50.00, 70.00, 6.0, 7.0, 2.00, 1000, 'Legume-feuille japonais a croissance rapide.'),
(5, 'Chou kale', 'Brassica oleracea var. acephala', 'Legume-feuille', 55, 12.0, 24.0, 50.00, 75.00, 6.0, 7.0, 2.50, 1000, 'Legume-feuille pouvant permettre plusieurs recoltes.'),
(6, 'Tomate', 'Solanum lycopersicum', 'Fruit', 80, 18.0, 28.0, 55.00, 75.00, 5.5, 6.8, 3.00, 1200, 'Plante fruitiere demandant une temperature et une humidite controlees.'),
(7, 'Poivron', 'Capsicum annuum', 'Fruit', 90, 20.0, 30.0, 55.00, 75.00, 5.5, 6.5, 3.00, 1200, 'Plante necessitant une temperature relativement elevee.'),
(8, 'Pomme de terre', 'Solanum tuberosum', 'Tubercule', 90, 15.0, 22.0, 50.00, 70.00, 5.0, 6.5, 4.00, 1000, 'Tubercule fournissant une importante source de glucides.'),
(9, 'Patate douce', 'Ipomoea batatas', 'Tubercule', 100, 20.0, 30.0, 55.00, 80.00, 5.5, 6.5, 3.50, 1000, 'Plante produisant des racines comestibles et pouvant etre cultivee en environnement controle.'),
(10, 'Ble', 'Triticum aestivum', 'Cereale', 90, 18.0, 24.0, 40.00, 65.00, 6.0, 7.0, 4.70, 1000, 'Cereale pouvant fournir une source importante de glucides.'),
(11, 'Soja', 'Glycine max', 'Legumineuse', 100, 20.0, 30.0, 50.00, 75.00, 6.0, 7.0, 4.70, 1000, 'Legumineuse riche en proteines et en lipides.'),
(12, 'Carotte', 'Daucus carota', 'Legume-racine', 70, 15.0, 24.0, 50.00, 70.00, 6.0, 7.0, 2.50, 1000, 'Racine comestible necessitant un substrat suffisamment profond.'),
(13, 'Oignon vert', 'Allium fistulosum', 'Legume', 60, 13.0, 24.0, 50.00, 70.00, 6.0, 7.0, 2.00, 1000, 'Culture compacte pouvant etre produite de maniere continue.'),
(14, 'Fraise', 'Fragaria x ananassa', 'Fruit', 90, 15.0, 25.0, 55.00, 75.00, 5.5, 6.5, 2.50, 1000, 'Fruit permettant de diversifier l alimentation dans une ferme spatiale.'),
(15, 'Basilic', 'Ocimum basilicum', 'Herbe aromatique', 45, 20.0, 30.0, 50.00, 70.00, 5.5, 6.5, 2.00, 1000, 'Herbe aromatique compacte permettant de varier les repas.');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
