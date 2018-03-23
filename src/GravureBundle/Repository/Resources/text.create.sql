CREATE TABLE `gravure_text` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_block` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL default CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;