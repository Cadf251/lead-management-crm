ALTER TABLE `propostas`
  ADD CONSTRAINT `fk_ppt_produto_id` FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ppt_atendimento_id` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ppt_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;