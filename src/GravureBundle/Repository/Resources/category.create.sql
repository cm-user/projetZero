CREATE TABLE `gravure_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_machine` int,
  `surname` varchar(255) NOT NULL,
  `folder` varchar(255) NOT NULL,
  `name_gabarit` varchar(50) NOT NULL,
  `path_gabarit` varchar(255),
  `max_gabarit` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (id_machine) REFERENCES gravure_machine(id),
  UNIQUE KEY `surname` (`surname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;