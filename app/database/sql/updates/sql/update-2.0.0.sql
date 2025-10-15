CREATE TABLE `clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NULL,
  `celular` varchar(15) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `propostas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) DEFAULT NULL,
  `seguradora` varchar(100) DEFAULT NULL,
  `comissao` DECIMAL(10,2) DEFAULT NULL,
  `data_contratacao` DATETIME NOT NULL,
  `atendimento_id` INT,
  `usuario_id` INT,
  `produto_id` INT NOT NULL,
  `cliente_id` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `carteiras`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `descricao` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `carteiras_clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `carteira_id` INT NOT NULL,
  `cliente_id` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `carteiras_usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `carteira_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

ALTER TABLE `propostas`
  ADD KEY `idx_propostas_atendimento_id` (`atendimento_id`),
  ADD KEY `idx_propostas_usuario_id` (`usuario_id`),
  ADD KEY `idx_propostas_produto_id` (`produto_id`),
  ADD KEY `idx_propostas_cliente_id` (`cliente_id`);

ALTER TABLE `carteiras_clientes`
  ADD KEY `idx_carcli_cliente_id` (`cliente_id`),
  ADD KEY `idx_carcli_carteira_id` (`carteira_id`);

ALTER TABLE `carteiras_usuarios`
  ADD KEY `idx_carusr_usuario_id` (`usuario_id`),
  ADD KEY `idx_carusr_carteira_id` (`carteira_id`);

ALTER TABLE `propostas`
  ADD CONSTRAINT `fk_ppt_cliente_id` FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `carteiras_clientes`
  ADD CONSTRAINT `fk_carcli_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carcli_carteira` FOREIGN KEY (`carteira_id`) REFERENCES `carteiras`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `carteiras_usuarios`
  ADD CONSTRAINT `fk_carusr_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carusr_carteira` FOREIGN KEY (`carteira_id`) REFERENCES `carteiras`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;