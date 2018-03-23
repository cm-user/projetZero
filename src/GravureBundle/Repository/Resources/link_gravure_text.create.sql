CREATE TABLE `gravure_link_gravure_text` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_gravure` int NOT NULL,
  `id_text` int NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (id_gravure) REFERENCES gravure(id),
  FOREIGN KEY (id_text) REFERENCES gravure_text(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;