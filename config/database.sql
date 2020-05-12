CREATE DATABASE IF NOT EXISTS `matcha`;

USE `matcha`;

CREATE TABLE `user` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `birthdate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `gender` TINYINT(1) DEFAULT 0,
    `orientation` TINYINT(1) DEFAULT 2,
    `verification_code` VARCHAR(13) DEFAULT NULL,
    `verified` TINYINT(1) DEFAULT 0,
    `bio` TEXT DEFAULT NULL,
    `score` INT DEFAULT 0,
    `last_connection` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `online` TINYINT(1) NOT NULL DEFAULT 0,
    `latitude` DECIMAL(10, 8),
    `longitude` DECIMAL(11, 8),
    `picture` VARCHAR(13),
    PRIMARY KEY (`id`)
);

CREATE TABLE `interest` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
);

CREATE TABLE `user_interest` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `interest_id` INT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    FOREIGN KEY (`interest_id`) REFERENCES `interest`(`id`),
    UNIQUE KEY `user_interest_uk` (`user_id`,`interest_id`)
);

CREATE TABLE `like` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user0_id` INT NOT NULL,
    `user1_id` INT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user0_id`) REFERENCES `user`(`id`),
    FOREIGN KEY (`user1_id`) REFERENCES `user`(`id`)
);

CREATE TABLE `report` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user0_id` INT NOT NULL,
    `user1_id` INT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user0_id`) REFERENCES `user`(`id`),
    FOREIGN KEY (`user1_id`) REFERENCES `user`(`id`)
);

CREATE TABLE `block` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user0_id` INT NOT NULL,
    `user1_id` INT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user0_id`) REFERENCES `user`(`id`),
    FOREIGN KEY (`user1_id`) REFERENCES `user`(`id`)
);

CREATE TABLE `message` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `value` TEXT DEFAULT NULL,
    `user0_id` INT NOT NULL,
    `user1_id` INT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user0_id`) REFERENCES `user`(`id`),
    FOREIGN KEY (`user1_id`) REFERENCES `user`(`id`)
);

CREATE TABLE `notification` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user0_id` INT NOT NULL,
    `user1_id` INT NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `activated` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user0_id`) REFERENCES `user`(`id`),
    FOREIGN KEY (`user1_id`) REFERENCES `user`(`id`)
);

