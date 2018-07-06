CREATE TABLE `gravure_text` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_block` varchar(191) NOT NULL,
  `value` varchar(191) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;