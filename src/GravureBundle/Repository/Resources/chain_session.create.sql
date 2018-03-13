CREATE TABLE `gravure_chain_session` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_gravure` int,
  `id_session` int,
  `chain_number` int NOT NULL,
  `series_number` int NOT NULL,
  `engrave` bool DEFAULT '0',
  PRIMARY KEY (`id`),
  FOREIGN KEY (id_gravure) REFERENCES gravure(id),
  FOREIGN KEY (id_session) REFERENCES gravure_session(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;