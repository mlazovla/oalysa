-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `oalysa` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci */;
USE `oalysa`;

DROP TABLE IF EXISTS `Action`;
CREATE TABLE `Action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Name of action was done in Log.',
  `type` varchar(15) COLLATE utf8_czech_ci NOT NULL COMMENT 'visit edit insert delete login',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `attachement`;
CREATE TABLE `attachement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL,
  `mimeType` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `file` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Where is file locate in file system.',
  `topic_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Owner',
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_Attachment_file` (`file`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`),
  CONSTRAINT `attachement_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `attachement_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `attachement` (`id`, `name`, `mimeType`, `file`, `topic_id`, `user_id`, `description`, `created_at`) VALUES
(1, 'Diktát ze čtvrtka',  'application/pdf',  '1-1-diktat-ze-ctvrtka.pdf',  1,  1,  'Nejtežší diktát ever!',  '2014-12-13 12:26:59');

DROP TABLE IF EXISTS `comentary`;
CREATE TABLE `comentary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `topic_id` int(11) NOT NULL,
  `comentary_id` int(11) DEFAULT NULL COMMENT 'This comentary is answer on another comentary.',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`),
  KEY `answer_on` (`comentary_id`),
  CONSTRAINT `comentary_ibfk_4` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comentary_ibfk_5` FOREIGN KEY (`comentary_id`) REFERENCES `comentary` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `comentary_ibfk_6` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `comentary` (`id`, `user_id`, `topic_id`, `comentary_id`, `created_at`, `content`) VALUES
(1, 1,  1,  NULL, '2014-12-13 00:34:17',  'proč čj?'),
(2, 1,  1,  1,  '2014-12-13 00:34:35',  'protože bagr'),
(3, 2,  1,  NULL, '2014-12-13 11:08:37',  'Díky'),
(4, 2,  1,  NULL, '2014-12-13 11:09:18',  'Díky'),
(5, 2,  1,  NULL, '2014-12-13 11:22:59',  'funguje\ntady\nenter'),
(6, 2,  1,  5,  '2014-12-13 11:42:21',  'Už ano'),
(7, 5,  1,  1,  '2014-12-26 21:38:15',  'fakt\njde \nenter?');

DROP TABLE IF EXISTS `garantedprivilegies`;
CREATE TABLE `garantedprivilegies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `privilege_id` int(11) DEFAULT NULL,
  `resource_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `privilege_id` (`privilege_id`),
  KEY `role_id` (`role_id`),
  KEY `resource_id` (`resource_id`),
  CONSTRAINT `garantedprivilegies_ibfk_5` FOREIGN KEY (`privilege_id`) REFERENCES `privilege` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `garantedprivilegies_ibfk_6` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `garantedprivilegies_ibfk_7` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Garants privilegies on resource for role.';

INSERT INTO `garantedprivilegies` (`id`, `role_id`, `privilege_id`, `resource_id`) VALUES
(1, 7,  1,  4),
(2, 7,  5,  4),
(3, 7,  NULL, 4),
(4, 7,  1,  2),
(5, 7,  1,  5),
(6, 7,  1,  1),
(7, 7,  1,  6),
(8, 2,  2,  6),
(10,  2,  3,  6),
(11,  2,  4,  6);

DROP TABLE IF EXISTS `grade`;
CREATE TABLE `grade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) COLLATE utf8_czech_ci DEFAULT NULL,
  `grade_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_Grade_name` (`name`),
  UNIQUE KEY `UQ_Grade_grade_next` (`grade_id`),
  KEY `IXFK_Grade_Grade` (`grade_id`),
  CONSTRAINT `grade_ibfk_1` FOREIGN KEY (`grade_id`) REFERENCES `grade` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `grade` (`id`, `name`, `grade_id`) VALUES
(1, '1. ročník',  2),
(2, '2. ročník',  3),
(3, '3. ročník',  4),
(4, '4. ročník',  NULL);

DROP TABLE IF EXISTS `Log`;
CREATE TABLE `Log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_czech_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`),
  CONSTRAINT `FK_Log_Action` FOREIGN KEY (`action_id`) REFERENCES `Action` (`id`),
  CONSTRAINT `FK_Log_Topic` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_Log_User` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `News`;
CREATE TABLE `News` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text COLLATE utf8_czech_ci,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IXFK_News_User` (`user_id`),
  CONSTRAINT `news_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `News` (`id`, `created_at`, `content`, `user_id`) VALUES
(1, '2014-12-09 14:35:29',  'Web byl spuštěn v pilotním provozu na localhostu.',  2);

DROP TABLE IF EXISTS `privilege`;
CREATE TABLE `privilege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `privilege` (`id`, `name`) VALUES
(1, 'read'),
(2, 'insert'),
(3, 'update'),
(4, 'delete'),
(5, 'use');

DROP TABLE IF EXISTS `resource`;
CREATE TABLE `resource` (
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Resources of pages';

INSERT INTO `resource` (`name`, `id`) VALUES
('comentary', 1),
('topic', 2),
('usersgroups', 3),
('login', 4),
('attachement', 5),
('selfComentary', 6);

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `role` (`id`, `name`, `role_id`) VALUES
(1, 'Guest',  NULL),
(2, 'Žák',  7),
(4, 'Učitel', 2),
(5, 'Admin',  4),
(6, 'Zablokovaný účet', 1),
(7, 'Prohlídka',  6);

DROP TABLE IF EXISTS `Subject`;
CREATE TABLE `Subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shortcut` varchar(15) COLLATE utf8_czech_ci NOT NULL COMMENT 'Subject shortcut.',
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL COMMENT 'Subject full name.',
  `description` text COLLATE utf8_czech_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_subject_shortcut` (`shortcut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Table of school subject';

INSERT INTO `Subject` (`id`, `shortcut`, `name`, `description`) VALUES
(1, 'MAT',  'Matematika', 'Hrátky s čísly.'),
(2, 'ČJ', 'Český jazyk',  'Diktátky, literatura a jedním slovem zlo.');

DROP TABLE IF EXISTS `subject2grade`;
CREATE TABLE `subject2grade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Subject_id` int(11) NOT NULL,
  `Grade_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IXFK_Subject2Grade_Subject` (`Subject_id`),
  KEY `IXFK_Subject2Grade_Grade` (`Grade_id`),
  CONSTRAINT `subject2grade_ibfk_4` FOREIGN KEY (`Subject_id`) REFERENCES `Subject` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subject2grade_ibfk_5` FOREIGN KEY (`Grade_id`) REFERENCES `grade` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `subject2grade` (`id`, `Subject_id`, `Grade_id`) VALUES
(1, 1,  1),
(2, 1,  2),
(3, 1,  3),
(4, 1,  4),
(5, 2,  1),
(6, 2,  2),
(7, 2,  3);

DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject2grade_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `isPublic` tinyint(4) NOT NULL DEFAULT '0',
  `anotation` text COLLATE utf8_czech_ci,
  `content` text COLLATE utf8_czech_ci,
  `enableDiscussion` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `subject2grade_id` (`subject2grade_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_Topic_User` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE SET NULL,
  CONSTRAINT `topic_ibfk_1` FOREIGN KEY (`subject2grade_id`) REFERENCES `Subject2Grade` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `topic` (`id`, `name`, `created_at`, `subject2grade_id`, `user_id`, `isPublic`, `anotation`, `content`, `enableDiscussion`) VALUES
(1, 'Diktáty',  '2014-12-10 21:43:19',  7,  1,  0,  'Nejlepší procvičení gramatiky najdete v této kapitole!', 'V této kapitole si můžete procvičit psaní velkých a malých písmen, psaní měkkého a tvrdého i,í/y,ý. Najdete zde výběr vzorových diktátů pro třetí ročníky gymnáziíí a SOŠ.', 1);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(63) COLLATE utf8_czech_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'hash(password)',
  `name` varchar(127) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(127) COLLATE utf8_czech_ci DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `grade_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_User_login` (`username`),
  KEY `IXFK_User_Role` (`role_id`),
  KEY `grade_id` (`grade_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_ibfk_2` FOREIGN KEY (`grade_id`) REFERENCES `grade` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`id`, `username`, `password`, `name`, `email`, `role_id`, `grade_id`) VALUES
(1, 'mlazovla', '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Vladimír Mlázovský', 'vml@seznam.cz',  5,  NULL),
(2, 'admin',  '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Administrátor',  'admin@oalysa.cz',  5,  NULL),
(3, 'blocked',  '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Zablokovaný účet', 'blocked@user.com', 6,  1),
(4, 'prohlidka',  '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Prohlídka webu', 'prohlidka@test.com', 7,  1),
(5, 'zak',  '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Žák',  'zak@test.com', 2,  1),
(6, 'ucitel', '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Učitel', 'ucitel@test.com',  4,  1);

DROP TABLE IF EXISTS `user2Role`;
CREATE TABLE `user2Role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user2role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user2role_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Sets roles for users.';


DROP TABLE IF EXISTS `User2UsersGroup`;
CREATE TABLE `User2UsersGroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UsersGroup_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IXFK_User2UsersGroup_User` (`User_id`),
  KEY `IXFK_User2UsersGroup_UsersGroup` (`UsersGroup_id`),
  CONSTRAINT `user2usersgroup_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `Action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user2usersgroup_ibfk_2` FOREIGN KEY (`UsersGroup_id`) REFERENCES `UsersGroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Decomposition of Users in UsersGroups.';


DROP TABLE IF EXISTS `UsersGroup`;
CREATE TABLE `UsersGroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2014-12-26 21:05:34