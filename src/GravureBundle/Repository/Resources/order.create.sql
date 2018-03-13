CREATE TABLE `gravure_order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_prestashop` int NOT NULL,
  `box` int,
  `gift` bool DEFAULT '0',
  `engrave` bool DEFAULT '0',
  `checked` bool DEFAULT '0',
  `state_prestashop` int NOT NULL,
  `date_prestashop` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_prestashop` (`id_prestashop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;