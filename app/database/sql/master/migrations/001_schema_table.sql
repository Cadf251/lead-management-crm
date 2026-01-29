DROP TABLE IF EXISTS schema_migrations;

CREATE TABLE schema_migrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  migration VARCHAR(100) NOT NULL,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_migration (migration)
);

ALTER TABLE `database`
  CHANGE COLUMN `version` `is_installed` TINYINT(1) NOT NULL DEFAULT 0;
