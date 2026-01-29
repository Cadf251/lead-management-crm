CREATE TABLE `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `celular` varchar(15) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `foto_perfil` varchar(100) DEFAULT NULL,
  `usuario_status_id` INT NOT NULL DEFAULT 1,
  `nivel_acesso_id` INT NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `tokens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `token` VARCHAR(32) NOT NULL,
  `tipo` VARCHAR(50),
  `contexto` VARCHAR(255),
  `prazo` DATETIME,
  `usuario_id` INT(11),
  `atendimento_id` INT(11),
  `token_status_id` INT(11) DEFAULT 3,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `equipes_usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vez` INT NOT NULL DEFAULT 0,
  `pode_receber_leads` BOOLEAN NOT NULL DEFAULT 1,
  `equipe_usuario_funcao_id` INT NOT NULL DEFAULT 1,
  `equipe_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `equipes_usuarios_funcoes`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `descricao` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

INSERT INTO `equipes_usuarios_funcoes` (`id`, `nome`, `descricao`) VALUES
(1, "Colaborador", "O usuário receberá os leads direcionados a essa equipe e poderá ver os próprios leads."),
(2, "Gerente", "O usuário precisa ter nível de acesso de gerente, e poderá gerenciar os leads de todos na equipe.");

CREATE TABLE `equipes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `equipe_status_id` INT NOT NULL DEFAULT 3,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `generico_status`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

INSERT INTO `generico_status` (`id`, `nome`) VALUES
(1, "Desativado"),
(2, "Pausado"),
(3, "Ativado");

CREATE TABLE `produtos`(
  `id`INT NOT NULL AUTO_INCREMENT,
  `nome`varchar(50) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `atendimentos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) DEFAULT NULL,
  `primeiro_contato` datetime DEFAULT NULL,
  `atendimento_status_id` INT NOT NULL DEFAULT 1,
  `equipe_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `lead_id` INT NOT NULL,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `atendimento_status`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `atendimento_status` (`id`, `nome`) VALUES
(1, "Não Atendido"),
(2, "Em atendimento"),
(3, "Arquivado");

CREATE TABLE `retornos`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) DEFAULT NULL,
  `data` datetime NOT NULL,
  `retorno_status_id` INT NOT NULL DEFAULT 1,
  `atendimento_id` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `retorno_status`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

INSERT INTO `retorno_status` (`id`, `nome`) VALUES
(1, "Pendente"),
(2, "Concluído");

CREATE TABLE `leads` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NULL,
  `celular` varchar(15) NULL,
  `lead_status_id` INT DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `lead_utm`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `origem` varchar(50) DEFAULT NULL,
  `meio` varchar(50) DEFAULT 'organic',
  `campanha` varchar(50) DEFAULT NULL,
  `conteudo` varchar(50) DEFAULT NULL,
  `termo` varchar(50) DEFAULT NULL,
  `palavra_chave` VARCHAR(150) DEFAULT NULL,
  `gclid` varchar(256) DEFAULT NULL,
  `fbclid` varchar(256) DEFAULT NULL,
  `lead_id` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

CREATE TABLE `lead_status` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

INSERT INTO `lead_status` (`id`, `nome`, `descricao`) VALUES
(1, 'Não respondeu', 'Leads que não responderam a primeira mensagem.'),
(2, 'Desqualificado', 'Leads que não se encaixam no perfil necessário da campanha, mesmo que seja feito a cotação em outra seguradora/operadora/produto.'),
(3, 'Qualificado', "Leads que se encaixaram no perfil da campanha, porém não tiveram muito interesse na proposta."),
(4, 'Oportunidade', 'Leads estão muito próximos de fechar, receberam valores, demonstraram interesse.'),
(5, 'Contratado', "Leads que fecharam negócio.");

CREATE TABLE `atendimento_perfil` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `perfil` JSON NOT NULL,
  `tipo` VARCHAR(50),
  `contexto` VARCHAR(255),
  `atendimento_id` INT NOT NULL
);

CREATE TABLE `clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NULL,
  `celular` varchar(15) NULL,
  `lead_id` INT DEFAULT NULL,
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

ALTER TABLE `tokens`
  ADD KEY `idx_tokens_usuario_id` (`usuario_id`);

ALTER TABLE `equipes_usuarios`
  ADD KEY `idx_eu_equipe_usuario_funcao_id` (`equipe_usuario_funcao_id`),
  ADD KEY `idx_eu_equipe_id` (`equipe_id`),
  ADD KEY `idx_eu_usuario_id` (`usuario_id`);
  
ALTER TABLE `equipes`
  ADD KEY `idx_eq_equipe_status_id` (`equipe_status_id`);

ALTER TABLE `atendimentos`
  ADD KEY `idx_at_atendimento_status_id` (`atendimento_status_id`),
  ADD KEY `idx_at_usuario_id` (`usuario_id`),
  ADD KEY `idx_at_equipe_id` (`equipe_id`),
  ADD KEY `idx_at_lead_id` (`lead_id`);

ALTER TABLE `retornos`
  ADD KEY `idx_rt_retorno_status_id` (`retorno_status_id`),
  ADD KEY `idx_rt_atendimento_id` (`atendimento_id`);

ALTER TABLE `leads`
  ADD KEY `idx_ld_lead_status_id` (`lead_status_id`);

ALTER TABLE `lead_utm`
  ADD KEY `idx_ldutm_lead_id` (`lead_id`);

ALTER TABLE `atendimento_perfil`
  ADD KEY `idx_attperfil_atendimento_id` (`atendimento_id`);

ALTER TABLE `propostas`
  ADD KEY `idx_propostas_atendimento_id` (`atendimento_id`),
  ADD KEY `idx_propostas_usuario_id` (`usuario_id`),
  ADD KEY `idx_propostas_produto_id` (`produto_id`),
  ADD KEY `idx_propostas_cliente_id` (`cliente_id`);

ALTER TABLE `clientes`
  ADD KEY `idx_clientes_lead_id` (`lead_id`);

ALTER TABLE `carteiras_clientes`
  ADD KEY `idx_carcli_cliente_id` (`cliente_id`),
  ADD KEY `idx_carcli_carteira_id` (`carteira_id`);

ALTER TABLE `carteiras_usuarios`
  ADD KEY `idx_carusr_usuario_id` (`usuario_id`),
  ADD KEY `idx_carusr_carteira_id` (`carteira_id`);

ALTER TABLE `recursos`
  ADD KEY `idx_rcr_usuario_id` (`usuario_id`);

ALTER TABLE `recursos_compartilhamentos`
  ADD KEY `idx_rcrcompart_recurso_id` (`recurso_id`),
  ADD KEY `idx_rcrcompart_usuario_id` (`usuario_id`),
  ADD KEY `idx_rcrcompart_equipe_id` (`equipe_id`);

ALTER TABLE `email_envios`
  ADD KEY `idx_emailenvios_status_id` (`email_status_id`);

ALTER TABLE `tokens`
  ADD CONSTRAINT `fk_tokens_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tokens_atendimento_id` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tokens_status_id` FOREIGN KEY (`token_status_id`) REFERENCES `generico_status` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `equipes_usuarios`
  ADD CONSTRAINT `fk_eu_equipe_usuario_funcao_id` FOREIGN KEY (`equipe_usuario_funcao_id`) REFERENCES `equipes_usuarios_funcoes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eu_equipe_id` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eu_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `equipes`
  ADD CONSTRAINT `fk_eq_equipe_status_id` FOREIGN KEY (`equipe_status_id`) REFERENCES `generico_status` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `atendimentos`
  ADD CONSTRAINT `fk_at_atendimento_status_id` FOREIGN KEY (`atendimento_status_id`) REFERENCES `atendimento_status` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_at_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_at_equipe_id` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_at_lead_id` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `retornos`
  ADD CONSTRAINT `fk_rt_retorno_status_id` FOREIGN KEY (`retorno_status_id`) REFERENCES `retorno_status` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rt_atendimento_id` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `leads`
  ADD CONSTRAINT `fk_ld_lead_status_id` FOREIGN KEY (`lead_status_id`) REFERENCES `lead_status` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `lead_utm`
  ADD CONSTRAINT `fk_ldutm_lead_id` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `atendimento_perfil`
  ADD CONSTRAINT `fk_attperfil_atendimento_id` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `propostas`
  ADD CONSTRAINT `fk_ppt_cliente_id` FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ppt_produto_id` FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ppt_atendimento_id` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ppt_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_lead_id` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `carteiras_clientes`
  ADD CONSTRAINT `fk_carcli_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carcli_carteira` FOREIGN KEY (`carteira_id`) REFERENCES `carteiras`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `carteiras_usuarios`
  ADD CONSTRAINT `fk_carusr_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carusr_carteira` FOREIGN KEY (`carteira_id`) REFERENCES `carteiras`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `recursos`
  ADD CONSTRAINT `fk_rcr_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `recursos_compartilhamentos`
  ADD CONSTRAINT `fk_rcrcompart_recurso_id` FOREIGN KEY (`recurso_id`) REFERENCES `recursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rcrcompart_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rcrcompart_equipe_id` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `email_envios`
  ADD CONSTRAINT `fk_emailenvios_status_id` FOREIGN KEY (`email_status_id`) REFERENCES `email_status` (`id`) ON DELETE RESTRICT;