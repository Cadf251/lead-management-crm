CREATE TABLE `atendimento_perfil` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `dados` JSON NOT NULL,
  `tipo` VARCHAR(50),
  `contexto` VARCHAR(255),
  `atendimento_id` INT NOT NULL
);

ALTER TABLE `atendimento_perfil`
  ADD KEY `idx_attperfil_atendimento_id` (`atendimento_id`);

ALTER TABLE `atendimento_perfil`
  ADD CONSTRAINT `fk_attperfil_atendimento_id` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

DROP TABLE IF EXISTS `lead_perfil`;