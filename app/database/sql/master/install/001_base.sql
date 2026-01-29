-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema tenants
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema tenants
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `tenants` DEFAULT CHARACTER SET utf8mb4 ;
SHOW WARNINGS;
USE `tenants` ;

-- -----------------------------------------------------
-- Table `tenants`.`tenants`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tenants`.`tenants` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `tenants`.`tenants` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `contact_email` VARCHAR(100) NOT NULL,
  `api_token` VARCHAR(32) NULL DEFAULT NULL,
  `status_id` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `tenants`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tenants`.`users` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `tenants`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `tenants`.`user_tenants`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tenants`.`user_tenants` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `tenants`.`user_tenants` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `tenant_id` INT NOT NULL,
  `status_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_tenants_users1_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_user_tenants_tenants1_idx` (`tenant_id` ASC) VISIBLE,
  CONSTRAINT `fk_user_tenants_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `tenants`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_tenants_tenants1`
    FOREIGN KEY (`tenant_id`)
    REFERENCES `tenants`.`tenants` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `tenants`.`database`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tenants`.`database` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `tenants`.`database` (
  `name` VARCHAR(100) NOT NULL,
  `host` VARCHAR(100) NOT NULL,
  `user` VARCHAR(100) NOT NULL,
  `version` VARCHAR(15) NULL,
  `tenant_id` INT NOT NULL,
  INDEX `fk_database_tenants1_idx` (`tenant_id` ASC) VISIBLE,
  UNIQUE INDEX `tenant_id_UNIQUE` (`tenant_id` ASC) VISIBLE,
  CONSTRAINT `fk_database_tenants1`
    FOREIGN KEY (`tenant_id`)
    REFERENCES `tenants`.`tenants` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
