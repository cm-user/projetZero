CREATE TABLE `gravure` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_product` int,
  `id_session` int,
  `id_order` int,
  `id_machine` int,
  `id_status` int,
  `config_id` int NOT NULL,
  `path_jpg` varchar(191) NOT NULL,
  `path_pdf` varchar(191) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (id_product) REFERENCES gravure_product(id),
  FOREIGN KEY (id_session) REFERENCES gravure_session(id),
  FOREIGN KEY (id_order) REFERENCES gravure_order(id),
  FOREIGN KEY (id_machine) REFERENCES gravure_machine(id),
  FOREIGN KEY (id_status) REFERENCES gravure_status(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;