-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `Action`;
CREATE TABLE `Action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Name of action was done in Log.',
  `type` varchar(15) COLLATE utf8_czech_ci NOT NULL COMMENT 'visit edit insert delete login',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `Action`;

DROP TABLE IF EXISTS `attachement`;
CREATE TABLE `attachement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL,
  `mimeType` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Where is file locate in file system.',
  `topic_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Owner',
  `description` text COLLATE utf8_czech_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`),
  CONSTRAINT `attachement_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `attachement_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `attachement`;
INSERT INTO `attachement` (`id`, `name`, `mimeType`, `extension`, `topic_id`, `user_id`, `description`, `created_at`) VALUES
(1, 'Diktát ze čtvrtka',  'application/pdf',  '1-1-diktat-ze-ctvrtka.pdf',  1,  NULL, 'Nejtežší diktát ever!',  '2014-12-13 11:26:59'),
(9, 'pdf.png',  'image/png',  'png',  NULL, 2,  NULL, '2015-01-03 13:58:24'),
(12,  'pdf.png',  'image/png',  'png',  NULL, 2,  NULL, '2015-01-03 14:02:13'),
(14,  'pdf.png',  'image/png',  'png',  NULL, NULL, NULL, '2015-01-04 13:13:31'),
(15,  'Mlazovsky.pdf',  'application/pdf',  'pdf',  33, NULL, 'bakule', '2015-01-04 13:14:25'),
(16,  'pdf.png',  'image/png',  'png',  33, NULL, 'obrázek pdfka',  '2015-01-04 13:14:25'),
(33,  'thesis (kopie).pdf', 'application/pdf',  'pdf',  NULL, NULL, NULL, '2015-01-07 09:08:18'),
(35,  'obrázek.png',  'image/png',  'png',  35, NULL, 'Ne kecám, není.',  '2015-01-07 10:18:53'),
(36,  'thesis.pdf', 'application/pdf',  'pdf',  35, NULL, 'bakule', '2015-01-07 10:18:53');

DROP TABLE IF EXISTS `comentary`;
CREATE TABLE `comentary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `topic_id` int(11) NOT NULL,
  `comentary_id` int(11) DEFAULT NULL COMMENT 'This comentary is answer on another comentary.',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`),
  KEY `answer_on` (`comentary_id`),
  CONSTRAINT `comentary_ibfk_4` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comentary_ibfk_7` FOREIGN KEY (`comentary_id`) REFERENCES `comentary` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comentary_ibfk_8` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `comentary`;
INSERT INTO `comentary` (`id`, `user_id`, `topic_id`, `comentary_id`, `created_at`, `content`) VALUES
(3, 2,  1,  NULL, '2014-12-13 10:08:37',  'Díky'),
(4, 2,  1,  NULL, '2014-12-13 10:09:18',  'Díky'),
(6, 2,  1,  NULL, '2014-12-13 10:42:21',  'Už ano'),
(9, NULL, 1,  6,  '2015-01-04 12:18:14',  'už ne'),
(10,  NULL, 1,  NULL, '2015-01-04 12:33:06',  'komentář'),
(11,  NULL, 1,  NULL, '2015-01-04 12:33:37',  'koment'),
(12,  NULL, 1,  NULL, '2015-01-04 12:33:48',  'wert'),
(13,  NULL, 1,  NULL, '2015-01-04 12:34:24',  'rrrrrrr');

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

TRUNCATE `garantedprivilegies`;
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
(11,  2,  4,  6),
(12,  2,  4,  6),
(13,  4,  4,  1),
(14,  4,  2,  2),
(15,  4,  1,  7),
(16,  4,  2,  7),
(17,  4,  3,  7),
(18,  4,  4,  7),
(19,  4,  3,  2),
(20,  4,  2,  5),
(21,  4,  3,  5),
(22,  4,  4,  5),
(23,  4,  2,  8),
(24,  4,  4,  8),
(25,  4,  1,  9),
(27,  4,  1,  10),
(28,  7,  1,  11);

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

TRUNCATE `grade`;
INSERT INTO `grade` (`id`, `name`, `grade_id`) VALUES
(1, '1. ročník',  2),
(2, '2. ročník',  3),
(3, '3. ročník',  4),
(4, '4. ročník',  NULL);

DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_czech_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`),
  CONSTRAINT `FK_Log_Action` FOREIGN KEY (`action_id`) REFERENCES `Action` (`id`),
  CONSTRAINT `FK_Log_Topic` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_Log_User` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `log`;

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content` text COLLATE utf8_czech_ci,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IXFK_News_User` (`user_id`),
  CONSTRAINT `news_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `news`;
INSERT INTO `news` (`id`, `created_at`, `content`, `user_id`) VALUES
(1, '2014-12-09 13:35:29',  'Web byl spuštěn v pilotním provozu na localhostu.',  2);

DROP TABLE IF EXISTS `privilege`;
CREATE TABLE `privilege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `privilege`;
INSERT INTO `privilege` (`id`, `name`) VALUES
(1, 'read'),
(2, 'insert'),
(3, 'update'),
(4, 'delete'),
(5, 'use'),
(6, 'resetPassword'),
(7, 'block');

DROP TABLE IF EXISTS `resource`;
CREATE TABLE `resource` (
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Resources of pages';

TRUNCATE `resource`;
INSERT INTO `resource` (`name`, `id`) VALUES
('comentary', 1),
('topic', 2),
('usersgroups', 3),
('login', 4),
('attachement', 5),
('selfComentary', 6),
('selfTopic', 7),
('news',  8),
('acount',  9),
('initPassword',  10),
('subject', 11);

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `assignable` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Is assignable to an user account.',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `role`;
INSERT INTO `role` (`id`, `name`, `role_id`, `assignable`) VALUES
(1, 'Guest',  NULL, 0),
(2, 'Žák',  7,  1),
(4, 'Učitel', 2,  1),
(5, 'Admin',  4,  0),
(6, 'Zablokovaný účet', 1,  1),
(7, 'Prohlídka',  6,  1);

DROP TABLE IF EXISTS `Subject`;
CREATE TABLE `Subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shortcut` varchar(15) COLLATE utf8_czech_ci NOT NULL COMMENT 'Subject shortcut.',
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL COMMENT 'Subject full name.',
  `description` text COLLATE utf8_czech_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_subject_shortcut` (`shortcut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Table of school subject';

TRUNCATE `Subject`;
INSERT INTO `Subject` (`id`, `shortcut`, `name`, `description`) VALUES
(1, 'MAT',  'Matematika', 'Hrátky s čísly.'),
(2, 'ČJ', 'Český jazyk',  'Diktátky, literatura a jedním slovem zlo.'),
(4, 'FYZ',  'Fyzika', '<p>Houbeles, fyzika .</p>'),
(5, 'CHE',  'Chemie', '<p>vařen&iacute;</p>');

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

TRUNCATE `subject2grade`;
INSERT INTO `subject2grade` (`id`, `Subject_id`, `Grade_id`) VALUES
(1, 1,  1),
(2, 1,  2),
(3, 1,  3),
(4, 1,  4),
(5, 2,  1),
(6, 2,  2),
(7, 2,  3),
(9, 5,  2),
(10,  5,  3),
(11,  5,  4),
(12,  1,  2),
(13,  1,  3),
(16,  4,  4);

DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject2grade_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `anotation` text COLLATE utf8_czech_ci,
  `content` text COLLATE utf8_czech_ci,
  `enableDiscussion` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `subject2grade_id` (`subject2grade_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `topic_ibfk_2` FOREIGN KEY (`subject2grade_id`) REFERENCES `subject2grade` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `topic_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `topic`;
INSERT INTO `topic` (`id`, `name`, `created_at`, `subject2grade_id`, `user_id`, `anotation`, `content`, `enableDiscussion`) VALUES
(1, 'Diktáty',  '2014-12-10 20:43:19',  7,  NULL, 'Nejlepší procvičení gramatiky najdete v této kapitole!', 'V této kapitole si můžete procvičit psaní velkých a malých písmen, psaní měkkého a tvrdého i,í/y,ý. Najdete zde výběr vzorových diktátů pro třetí ročníky gymnáziíí a SOŠ.', 1),
(2, 'Karel Čapek',  '2015-01-02 20:09:07',  7,  2,  '<p>anotace ke Karel Čapek</p>',  '<p>čl&aacute;nek&nbsp;ke Karel Čapek</p>', 1),
(3, 'Karel Čapek',  '2015-01-02 20:17:03',  7,  2,  '<p>anotace ke Karel Čapek</p>',  '<p>čl&aacute;nek&nbsp;ke Karel Čapek</p>', 1),
(4, 'Karel Čapek',  '2015-01-02 20:19:23',  7,  2,  '<p>anotace ke Karel Čapek</p>',  '<p>čl&aacute;nek&nbsp;ke Karel Čapek</p>', 1),
(33,  'test rychlosti', '2015-01-04 13:14:25',  3,  NULL, '<p>testov&aacute;n&iacute; rychlosti je důležit&eacute;</p>',  '<p><span style=\"color: #ff00ff;\">testov&aacute;n&iacute; rychlosti je důležit&eacute; pro zji&scaron;těn&iacute; jak dobře jsem web navrhl.</span></p>', 1),
(35,  'test příloh',  '2015-01-07 10:18:53',  5,  NULL, '<p>testing, testing...</p>', '', 1),
(36,  'Archimédův zákon', '2015-01-16 13:01:19',  16, 28, '<p>Těleso do vody blablabla</p>',  '', 1);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(63) COLLATE utf8_czech_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'hash(password)',
  `name` varchar(127) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(127) COLLATE utf8_czech_ci DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `grade_id` int(11) DEFAULT NULL,
  `activate` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'active account can be logged in',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_User_login` (`username`),
  KEY `IXFK_User_Role` (`role_id`),
  KEY `grade_id` (`grade_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_ibfk_2` FOREIGN KEY (`grade_id`) REFERENCES `grade` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `user`;
INSERT INTO `user` (`id`, `username`, `password`, `name`, `email`, `role_id`, `grade_id`, `activate`) VALUES
(2, 'admin',  '$2y$10$kI9ykVyWDW.slvwpcK//vuY3vhstkZUwAotRzMNxsQqcP07xWWNka', 'Administrátor',  'admin@oalysa.cz',  5,  NULL, 1),
(3, 'blocked',  '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Zablokovaný účet', 'blocked@user.com', 6,  1,  1),
(6, 'ucitel', '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Pan Garison',  'ucitel@test.com',  4,  NULL, 1),
(28,  'vml',  '$2y$10$5/3VJ0tjpOaQEx/pn9G1Su/yYNsX7xIA4j3olVXy6WNOQ7lK85VJq', 'Mlázovský Vladimír', 'vml@seznam.cz',  5,  NULL, 1),
(29,  'cjmikey',  '$2y$10$fpDOtzOO.uCeRWw2vtzRY.fo9z0EFEIGwrjztVxgcrWg9UdeGE0LS', 'Frank Michal', '', 5,  NULL, 1);

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

TRUNCATE `User2UsersGroup`;

DROP TABLE IF EXISTS `UsersGroup`;
CREATE TABLE `UsersGroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

TRUNCATE `UsersGroup`;

-- 2015-01-16 14:23:59