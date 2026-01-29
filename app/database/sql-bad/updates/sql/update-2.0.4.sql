DROP TABLE IF EXISTS `mensagens_automaticas`;

CREATE TABLE `recursos` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(50) NOT NULL DEFAULT 'Recurso',
  `descricao` VARCHAR(255) DEFAULT NULL,
  `conteudo` LONGTEXT NOT NULL,
  `email_assunto` VARCHAR(50) DEFAULT NULL,
  `tipo` ENUM('email', 'mensagem') DEFAULT 'mensagem',
  `usuario_id` INT NOT NULL,
  `visibilidade` ENUM('publico', 'equipe', 'pessoal') NOT NULL DEFAULT 'equipe'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `recursos_compartilhamentos` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `recurso_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `equipe_id` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `email_envios` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `recurso_id` INT NOT NULL,
  `remetente` VARCHAR(255) DEFAULT NULL,
  `destinatario` VARCHAR(100) DEFAULT NULL,
  `email_status_id` INT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `email_status` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

INSERT INTO `email_status` (`nome`) VALUES
('Enviado'),
('Retornado'),
('Aberto'),
('Clicado');

ALTER TABLE `atendimentos`
  DROP COLUMN `comissao`,
  DROP COLUMN `data_conversao`;

ALTER TABLE `clientes`
  ADD COLUMN `lead_id` INT DEFAULT NULL AFTER `celular`;

ALTER TABLE `recursos`
  ADD KEY `idx_rcr_usuario_id` (`usuario_id`);

ALTER TABLE `recursos_compartilhamentos`
  ADD KEY `idx_rcrcompart_recurso_id` (`recurso_id`),
  ADD KEY `idx_rcrcompart_usuario_id` (`usuario_id`),
  ADD KEY `idx_rcrcompart_equipe_id` (`equipe_id`);

ALTER TABLE `email_envios`
  ADD KEY `idx_emailenvios_status_id` (`email_status_id`);

ALTER TABLE `recursos`
  ADD CONSTRAINT `fk_rcr_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `recursos_compartilhamentos`
  ADD CONSTRAINT `fk_rcrcompart_recurso_id` FOREIGN KEY (`recurso_id`) REFERENCES `recursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rcrcompart_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rcrcompart_equipe_id` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `email_envios`
  ADD CONSTRAINT `fk_emailenvios_status_id` FOREIGN KEY (`email_status_id`) REFERENCES `email_status` (`id`) ON DELETE RESTRICT;

ALTER TABLE `clientes`
  ADD KEY `idx_clientes_lead_id` (`lead_id`);

ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_lead_id` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
