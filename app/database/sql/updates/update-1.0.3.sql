ALTER TABLE `atendimentos`
  ADD COLUMN `modified` DATETIME NULL AFTER `created`,
  ADD COLUMN `data_conversao` DATETIME NULL AFTER `comissao`;