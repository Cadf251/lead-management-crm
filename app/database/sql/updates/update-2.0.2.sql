ALTER TABLE `lead_perfil`
  ADD `tipo` VARCHAR(50) AFTER `perfil`,
  ADD `contexto` VARCHAR(255) AFTER `tipo`;

ALTER TABLE `tokens`
  ADD `tipo` VARCHAR(50) AFTER `token`,
  ADD `contexto` VARCHAR(255) AFTER `tipo`,
  ADD `token_status_id` INT(11) DEFAULT 3 AFTER `atendimento_id`;

UPDATE `equipe_status`
  SET `nome` = 'Desativado'
  WHERE `id` = 1;

UPDATE `equipe_status`
  SET `nome` = 'Pausado'
  WHERE `id` = 2;
  
UPDATE `equipe_status`
  SET `nome` = 'Ativado'
  WHERE `id` = 3;

ALTER TABLE `equipe_status`
  RENAME TO `generico_status`;

ALTER TABLE `equipes`
  DROP CONSTRAINT `fk_eq_equipe_status_id`;

ALTER TABLE `equipes`
  ADD CONSTRAINT `fk_eq_equipe_status_id` FOREIGN KEY (`equipe_status_id`) REFERENCES `generico_status` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `tokens`
  ADD CONSTRAINT `fk_tokens_status_id` FOREIGN KEY (`token_status_id`) REFERENCES `generico_status` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;