
DROP TABLE IF EXISTS `maladies`;
CREATE TABLE IF NOT EXISTS `maladies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symptomes` text COLLATE utf8mb4_unicode_ci,
  `cause` text COLLATE utf8mb4_unicode_ci,
  `traitement` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `maladies` (`id`, `nom`, `symptomes`, `cause`, `traitement`) VALUES
(1, 'Mildiou', 'Taches brunes huileuses sur les feuilles, duvet blanchatre au revers, pourrissement rapide des tissus.', 'Champignon favorise par une humidite elevee et une mauvaise circulation d air.', 'Retirer les parties atteintes, reduire l humidite, ameliorer la ventilation.'),
(2, 'Oidium', 'Poudre blanche farineuse sur les feuilles et les tiges, deformation du feuillage.', 'Champignon favorise par une atmosphere confinee et des ecarts de temperature.', 'Ameliorer la circulation d air, pulverisation de bicarbonate ou fongicide leger.'),
(3, 'Pourriture racinaire', 'Fletrissement malgre un arrosage suffisant, racines brunes et molles, croissance ralentie.', 'Exces d eau, substrat mal draine.', 'Reduire l arrosage, ameliorer le drainage, retirer les racines atteintes.'),
(4, 'Rouille', 'Pustules orange a brun rouille sous les feuilles, jaunissement puis chute du feuillage.', 'Champignon favorise par une humidite elevee et un feuillage qui reste mouille longtemps.', 'Eliminer les feuilles atteintes, eviter de mouiller le feuillage, ameliorer l aeration.'),
(5, 'Fonte des semis', 'Jeunes plants qui s affaissent au niveau du collet et meurent rapidement apres la levee.', 'Substrat trop humide et mal aere, temperature trop basse.', 'Desinfecter le substrat, eviter l exces d arrosage, ameliorer la ventilation.');
