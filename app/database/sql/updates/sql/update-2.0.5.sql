-- ALTER TABLE `lead_status`
--   ADD COLUMN `descricao` VARCHAR(255) DEFAULT NULL AFTER `nome`;

-- UPDATE lead_status 
-- SET
-- nome = "Não respondeu",
-- descricao = "Leads que não responderam a primeira mensagem."
-- WHERE 
-- id = 1;

-- UPDATE lead_status 
-- SET
-- nome = "Desqualificado",
-- descricao = "Leads que não se encaixam no perfil necessário da campanha, mesmo que seja feito a cotação em outra seguradora/operadora/produto."
-- WHERE 
-- id = 2;

-- UPDATE lead_status 
-- SET
-- nome = "Qualificado",
-- descricao = "Leads que se encaixaram no perfil da campanha, porém não tiveram muito interesse na proposta."
-- WHERE 
-- id = 3;

-- INSERT INTO `lead_status` (`id`, `nome`, `descricao`) VALUES
-- (4, 'Oportunidade', 'Leads estão muito próximos de fechar, receberam valores, demonstraram interesse.'),
-- (5, 'Contratado', "Leads que fecharam negócio.");

-- NORMALIZA OS STATUS QUE JÁ EXISTEM
-- UPDATE leads
-- SET lead_status_id = 3
-- WHERE lead_status_id = 2;

-- UPDATE leads
-- SET lead_status_id = 5
-- WHERE lead_status_id = 3;