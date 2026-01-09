ALTER TABLE `usuarios`
  DROP FOREIGN KEY `fk_usr_usuario_status_id`,
  DROP FOREIGN KEY `fk_usr_nivel_acesso_id`;

DROP TABLE IF EXISTS `niveis_acesso_permissoes`;
DROP TABLE IF EXISTS `niveis_acesso`;
DROP TABLE IF EXISTS `permissoes`;
DROP TABLE IF EXISTS `usuario_status`;

CREATE TABLE `ofertas` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL DEFAULT "Sem nome",
  `descricao` VARCHAR(255) DEFAULT NULL,
  `status_id` INT DEFAULT 1,
  `produto_id` INT NOT NULL
);

CREATE TABLE `equipes_ofertas` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `equipe_id` INT NOT NULL,
  `oferta_id` INT NOT NULL,
  UNIQUE KEY `uk_equipe_oferta` (`equipe_id`, `oferta_id`)
);

ALTER TABLE `equipes`
DROP FOREIGN KEY `fk_eq_produto_id`,
  DROP COLUMN `produto_id`;