-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(15) NULL,
  `profile_picture_type` VARCHAR(5) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `teams`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `teams` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  `status_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` DATETIME NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `teams_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `teams_users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `time` INT NOT NULL DEFAULT 0,
  `receive_leads` TINYINT(2) NOT NULL DEFAULT 1,
  `team_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `is_active` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_teams_users_teams1_idx` (`team_id` ASC) VISIBLE,
  INDEX `fk_teams_users_users2_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_teams_users_teams1`
    FOREIGN KEY (`team_id`)
    REFERENCES `teams` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_teams_users_users2`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `leads`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `leads` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NULL DEFAULT NULL,
  `phone` VARCHAR(15) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lead_journeys`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lead_journeys` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status_id` INT NOT NULL,
  `lead_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_lead_journeys_leads2_idx` (`lead_id` ASC) VISIBLE,
  CONSTRAINT `fk_lead_journeys_leads2`
    FOREIGN KEY (`lead_id`)
    REFERENCES `leads` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `supports`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `supports` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  `first_contact` DATETIME NULL DEFAULT NULL,
  `status_id` INT NULL,
  `contact_status_id` INT NULL,
  `lead_journey_id` INT NOT NULL,
  `team_user_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `archived_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_support_lead_journeys1_idx` (`lead_journey_id` ASC) VISIBLE,
  INDEX `fk_support_teams_users1_idx` (`team_user_id` ASC) VISIBLE,
  CONSTRAINT `fk_support_lead_journeys1`
    FOREIGN KEY (`lead_journey_id`)
    REFERENCES `lead_journeys` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_support_teams_users1`
    FOREIGN KEY (`team_user_id`)
    REFERENCES `teams_users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tokens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `token` VARCHAR(32) NOT NULL,
  `type` VARCHAR(50) NULL,
  `context` VARCHAR(255) NULL,
  `dead_end` DATETIME NULL,
  `status_id` INT NOT NULL DEFAULT 3,
  `user_id` INT NULL,
  `support_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_tokens_users1_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_tokens_supports1_idx` (`support_id` ASC) VISIBLE,
  CONSTRAINT `fk_tokens_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tokens_supports1`
    FOREIGN KEY (`support_id`)
    REFERENCES `supports` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `offers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `offers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'comum',
  `price` DECIMAL(19,2) NOT NULL,
  `discount` DECIMAL(2,2) NULL,
  `date_start` DATETIME NULL,
  `date_end` DATETIME NULL,
  `is_active` TINYINT NOT NULL,
  `product_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_offer_product1_idx` (`product_id` ASC) VISIBLE,
  CONSTRAINT `fk_offer_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lead_interactions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lead_interactions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(100) NULL,
  `url` VARCHAR(100) NULL,
  `utm` JSON NULL,
  `interaction_time` INT NULL DEFAULT 0,
  `lead_journey_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_lead_interactions_lead_journeys1_idx` (`lead_journey_id` ASC) VISIBLE,
  CONSTRAINT `fk_lead_interactions_lead_journeys1`
    FOREIGN KEY (`lead_journey_id`)
    REFERENCES `lead_journeys` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lead_profiles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lead_profiles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `data` JSON NOT NULL,
  `context` VARCHAR(255) NULL DEFAULT NULL,
  `status_id` INT NOT NULL DEFAULT 1,
  `lead_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_lead_profile_leads1_idx` (`lead_id` ASC) VISIBLE,
  CONSTRAINT `fk_lead_profile_leads1`
    FOREIGN KEY (`lead_id`)
    REFERENCES `leads` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `follow_ups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `follow_ups` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `contact_method` VARCHAR(50) NOT NULL,
  `schedule_time` DATETIME NOT NULL,
  `contact_made` TINYINT NULL DEFAULT 0,
  `support_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_follow_ups_support1_idx` (`support_id` ASC) VISIBLE,
  CONSTRAINT `fk_follow_ups_support1`
    FOREIGN KEY (`support_id`)
    REFERENCES `supports` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `teams_offers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `teams_offers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `offers_id` INT NOT NULL,
  `teams_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_teams_offers_offers1_idx` (`offers_id` ASC) VISIBLE,
  INDEX `fk_teams_offers_teams1_idx` (`teams_id` ASC) VISIBLE,
  CONSTRAINT `fk_teams_offers_offers1`
    FOREIGN KEY (`offers_id`)
    REFERENCES `offers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_teams_offers_teams1`
    FOREIGN KEY (`teams_id`)
    REFERENCES `teams` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `proposals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `proposals` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `contracted_price` INT NOT NULL,
  `support_id` INT NOT NULL,
  `offer_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_proposals_supports1_idx` (`support_id` ASC) VISIBLE,
  INDEX `fk_proposals_offers1_idx` (`offer_id` ASC) VISIBLE,
  CONSTRAINT `fk_proposals_supports1`
    FOREIGN KEY (`support_id`)
    REFERENCES `supports` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proposals_offers1`
    FOREIGN KEY (`offer_id`)
    REFERENCES `offers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE TABLE schema_migrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  migration VARCHAR(100) NOT NULL,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_migration (migration)
);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
