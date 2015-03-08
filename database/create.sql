CREATE DATABASE IF NOT EXISTS wishtosay;
USE wishtosay;

CREATE TABLE IF NOT EXISTS `continent` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `continent` VARCHAR(1024) NOT NULL,
  `code` VARCHAR(16) NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `country` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `country` VARCHAR(1024) NOT NULL,
  `continentId` INT,
  `code` VARCHAR(16) NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subdivision` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `subdivision` VARCHAR(1024) NOT NULL,
  `countryId` INT,
  `continentId` INT,
  `code` VARCHAR(16) NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `city` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `continentId` INT,
  `countryId` INT,
  `subdivisionId` INT,
  `city` VARCHAR(1024) NOT NULL,
  `latitude` FLOAT NOT NULL,
  `longitude` FLOAT NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gender` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `gender` VARCHAR(1024) NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `post` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `text` TEXT NOT NULL,
  `timestamp` INT NOT NULL,
  `upVotes` INT DEFAULT 0,
  `downVotes` INT DEFAULT 0,
  `ageFrom` INT DEFAULT 0,
  `ageTo` INT DEFAULT 100
) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posttag` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `postId` INT NOT NULL,
  `tagId` INT NOT NULL,
  `type` ENUM('continent', 'country', 'subdivision', 'city', 'gender', 'tag') NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tag` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `tag` VARCHAR(1024) NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `post` ADD `userHash` VARCHAR(255) NOT NULL;

CREATE TABLE IF NOT EXISTS `postvotelog` (
  `postId` INT NOT NULL,
  `userHash` VARCHAR(255) NOT NULL,
  `upvote` SMALLINT DEFAULT 0
) DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

