ALTER TABLE tokens 
  MODIFY usuario_id INT(11),
  ADD prazo DATETIME AFTER token,
  ADD atendimento_id INT(11),
  ADD CONSTRAINT `fk_tokens_atendimento_id` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;