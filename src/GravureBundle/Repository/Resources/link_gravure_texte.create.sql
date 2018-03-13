CREATE TABLE `gravure_link_gravure_texte` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_gravure` int NOT NULL,
  `id_texte` int NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (id_gravure) REFERENCES gravure(id),
  FOREIGN KEY (id_texte) REFERENCES gravure_texte(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;