CREATE TABLE `gravure_session` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `gravure_total` int,
  `created_at` datetime NOT,
  `updated_at` datetime NOT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;