CREATE TABLE `gravure_product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_category` int,
  `product_id` int NOT NULL,
  `time` int NOT NULL,
  `alias` varchar(191) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (id_category) REFERENCES gravure_category(id),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;