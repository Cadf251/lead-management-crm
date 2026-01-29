
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