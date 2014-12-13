-- phpMyAdmin SQL Dump
-- version 4.3.0
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Sob 13. pro 2014, 11:54
-- Verze serveru: 5.6.21
-- Verze PHP: 5.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `oalysa`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `Action`
--

CREATE TABLE IF NOT EXISTS `Action` (
`id` int(11) NOT NULL,
  `name` varchar(63) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Name of action was done in Log.',
  `type` varchar(15) COLLATE utf8_czech_ci NOT NULL COMMENT 'visit edit insert delete login'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `Attachement`
--

CREATE TABLE IF NOT EXISTS `Attachement` (
`id` int(11) NOT NULL,
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL,
  `file` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Where is file locate in file system.',
  `topic_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Owner',
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `Attachement`
--

INSERT INTO `Attachement` (`id`, `name`, `file`, `topic_id`, `user_id`, `description`, `created_at`) VALUES
(1, 'Diktát ze čtvrtka', '1-1-diktat-ze-ctvrtka.pdf', 1, 1, 'Nejtežší diktát ever!', '2014-12-13 12:26:59');

-- --------------------------------------------------------

--
-- Struktura tabulky `comentary`
--

CREATE TABLE IF NOT EXISTS `comentary` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `comentary_id` int(11) DEFAULT NULL COMMENT 'This comentary is answer on another comentary.',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `comentary`
--

INSERT INTO `comentary` (`id`, `user_id`, `topic_id`, `comentary_id`, `created_at`, `content`) VALUES
(1, 1, 1, NULL, '2014-12-13 00:34:17', 'proč čj?'),
(2, 1, 1, 1, '2014-12-13 00:34:35', 'protože bagr'),
(3, 2, 1, NULL, '2014-12-13 11:08:37', 'Díky'),
(4, 2, 1, NULL, '2014-12-13 11:09:18', 'Díky'),
(5, 2, 1, NULL, '2014-12-13 11:22:59', 'funguje\ntady\nenter'),
(6, 2, 1, 5, '2014-12-13 11:42:21', 'Už ano');

-- --------------------------------------------------------

--
-- Struktura tabulky `Grade`
--

CREATE TABLE IF NOT EXISTS `Grade` (
`id` int(11) NOT NULL,
  `name` varchar(63) COLLATE utf8_czech_ci DEFAULT NULL,
  `grade_next` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `Grade`
--

INSERT INTO `Grade` (`id`, `name`, `grade_next`) VALUES
(1, '1. ročník', 2),
(2, '2. ročník', 3),
(3, '3. ročník', 4),
(4, '4. ročník', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `Log`
--

CREATE TABLE IF NOT EXISTS `Log` (
`id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_czech_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `News`
--

CREATE TABLE IF NOT EXISTS `News` (
`id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text COLLATE utf8_czech_ci,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `News`
--

INSERT INTO `News` (`id`, `created_at`, `content`, `user_id`) VALUES
(1, '2014-12-09 14:35:29', 'Web byl spuštěn v pilotním provozu na localhostu.', 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `Role`
--

CREATE TABLE IF NOT EXISTS `Role` (
`id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `inherite` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `Role`
--

INSERT INTO `Role` (`id`, `name`, `inherite`) VALUES
(1, 'guest', NULL),
(2, 'student', 1),
(3, 'redaktor', 2),
(4, 'učitel', 3),
(5, 'Admin', 4);

-- --------------------------------------------------------

--
-- Struktura tabulky `Subject`
--

CREATE TABLE IF NOT EXISTS `Subject` (
`id` int(11) NOT NULL,
  `shortcut` varchar(15) COLLATE utf8_czech_ci NOT NULL COMMENT 'Subject shortcut.',
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL COMMENT 'Subject full name.',
  `description` text COLLATE utf8_czech_ci
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Table of school subject';

--
-- Vypisuji data pro tabulku `Subject`
--

INSERT INTO `Subject` (`id`, `shortcut`, `name`, `description`) VALUES
(1, 'MAT', 'Matematika', 'Hrátky s čísly.'),
(2, 'ČJ', 'Český jazyk', 'Diktátky, literatura a jedním slovem zlo.');

-- --------------------------------------------------------

--
-- Struktura tabulky `Subject2Grade`
--

CREATE TABLE IF NOT EXISTS `Subject2Grade` (
`id` int(11) NOT NULL,
  `Subject_id` int(11) DEFAULT NULL,
  `Grade_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `Subject2Grade`
--

INSERT INTO `Subject2Grade` (`id`, `Subject_id`, `Grade_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 2, 1),
(6, 2, 2),
(7, 2, 3);

-- --------------------------------------------------------

--
-- Struktura tabulky `Topic`
--

CREATE TABLE IF NOT EXISTS `Topic` (
`id` int(11) NOT NULL,
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject2grade_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `isPublic` tinyint(4) NOT NULL DEFAULT '0',
  `anotation` text COLLATE utf8_czech_ci,
  `content` text COLLATE utf8_czech_ci
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `Topic`
--

INSERT INTO `Topic` (`id`, `name`, `created_at`, `subject2grade_id`, `user_id`, `isPublic`, `anotation`, `content`) VALUES
(1, 'Diktáty', '2014-12-10 21:43:19', 7, 1, 0, 'Nejlepší procvičení gramatiky najdete v této kapitole!', 'V této kapitole si můžete procvičit psaní velkých a malých písmen, psaní měkkého a tvrdého i,í/y,ý. Najdete zde výběr vzorových diktátů pro třetí ročníky gymnáziíí a SOŠ.');

-- --------------------------------------------------------

--
-- Struktura tabulky `User`
--

CREATE TABLE IF NOT EXISTS `User` (
`id` int(11) NOT NULL,
  `username` varchar(63) COLLATE utf8_czech_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'hash(password)',
  `name` varchar(127) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(127) COLLATE utf8_czech_ci DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `User`
--

INSERT INTO `User` (`id`, `username`, `password`, `name`, `email`, `role_id`) VALUES
(1, 'mlazovla', '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Vladimír Mlázovský', 'vml@seznam.cz', 5),
(2, 'admin', '$2y$10$4d5x44vMU9KtK3cvQjqsQ.NpSEroOBNRxTb3PnANp7SMw9aHYYudm', 'Administrátor', 'admin@oalysa.cz', 5);

-- --------------------------------------------------------

--
-- Struktura tabulky `User2UsersGroup`
--

CREATE TABLE IF NOT EXISTS `User2UsersGroup` (
`id` int(11) NOT NULL,
  `UsersGroup_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Decomposition of Users in UsersGroups.';

-- --------------------------------------------------------

--
-- Struktura tabulky `UsersGroup`
--

CREATE TABLE IF NOT EXISTS `UsersGroup` (
`id` int(11) NOT NULL,
  `name` varchar(127) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `Action`
--
ALTER TABLE `Action`
 ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `Attachement`
--
ALTER TABLE `Attachement`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `UQ_Attachment_file` (`file`), ADD KEY `user_id` (`user_id`), ADD KEY `topic_id` (`topic_id`);

--
-- Klíče pro tabulku `comentary`
--
ALTER TABLE `comentary`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `topic_id` (`topic_id`), ADD KEY `answer_on` (`comentary_id`);

--
-- Klíče pro tabulku `Grade`
--
ALTER TABLE `Grade`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `UQ_Grade_name` (`name`), ADD UNIQUE KEY `UQ_Grade_grade_next` (`grade_next`), ADD KEY `IXFK_Grade_Grade` (`grade_next`);

--
-- Klíče pro tabulku `Log`
--
ALTER TABLE `Log`
 ADD PRIMARY KEY (`id`), ADD KEY `action_id` (`action_id`), ADD KEY `user_id` (`user_id`), ADD KEY `topic_id` (`topic_id`);

--
-- Klíče pro tabulku `News`
--
ALTER TABLE `News`
 ADD PRIMARY KEY (`id`), ADD KEY `IXFK_News_User` (`user_id`);

--
-- Klíče pro tabulku `Role`
--
ALTER TABLE `Role`
 ADD PRIMARY KEY (`id`), ADD KEY `IXFK_Role_Role` (`inherite`);

--
-- Klíče pro tabulku `Subject`
--
ALTER TABLE `Subject`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `UQ_subject_shortcut` (`shortcut`);

--
-- Klíče pro tabulku `Subject2Grade`
--
ALTER TABLE `Subject2Grade`
 ADD PRIMARY KEY (`id`), ADD KEY `IXFK_Subject2Grade_Subject` (`Subject_id`), ADD KEY `IXFK_Subject2Grade_Grade` (`Grade_id`);

--
-- Klíče pro tabulku `Topic`
--
ALTER TABLE `Topic`
 ADD PRIMARY KEY (`id`), ADD KEY `subject2grade_id` (`subject2grade_id`), ADD KEY `user_id` (`user_id`);

--
-- Klíče pro tabulku `User`
--
ALTER TABLE `User`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `UQ_User_login` (`username`), ADD KEY `IXFK_User_Role` (`role_id`);

--
-- Klíče pro tabulku `User2UsersGroup`
--
ALTER TABLE `User2UsersGroup`
 ADD PRIMARY KEY (`id`), ADD KEY `IXFK_User2UsersGroup_User` (`User_id`), ADD KEY `IXFK_User2UsersGroup_UsersGroup` (`UsersGroup_id`);

--
-- Klíče pro tabulku `UsersGroup`
--
ALTER TABLE `UsersGroup`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `Action`
--
ALTER TABLE `Action`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `Attachement`
--
ALTER TABLE `Attachement`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `comentary`
--
ALTER TABLE `comentary`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pro tabulku `Grade`
--
ALTER TABLE `Grade`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pro tabulku `Log`
--
ALTER TABLE `Log`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `News`
--
ALTER TABLE `News`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `Role`
--
ALTER TABLE `Role`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pro tabulku `Subject`
--
ALTER TABLE `Subject`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pro tabulku `Subject2Grade`
--
ALTER TABLE `Subject2Grade`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pro tabulku `Topic`
--
ALTER TABLE `Topic`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `User`
--
ALTER TABLE `User`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pro tabulku `User2UsersGroup`
--
ALTER TABLE `User2UsersGroup`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `UsersGroup`
--
ALTER TABLE `UsersGroup`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `Attachement`
--
ALTER TABLE `Attachement`
ADD CONSTRAINT `FK_Attachment_Topic` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE NO ACTION,
ADD CONSTRAINT `FK_Attachment_User` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

--
-- Omezení pro tabulku `comentary`
--
ALTER TABLE `comentary`
ADD CONSTRAINT `FK_Comentary_Topic` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_Comentary_User` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION,
ADD CONSTRAINT `comentary_ibfk_1` FOREIGN KEY (`comentary_id`) REFERENCES `Comentary` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `Grade`
--
ALTER TABLE `Grade`
ADD CONSTRAINT `FK_Grade_Grade` FOREIGN KEY (`grade_next`) REFERENCES `Grade` (`id`);

--
-- Omezení pro tabulku `Log`
--
ALTER TABLE `Log`
ADD CONSTRAINT `FK_Log_Action` FOREIGN KEY (`action_id`) REFERENCES `Action` (`id`),
ADD CONSTRAINT `FK_Log_Topic` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `FK_Log_User` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE SET NULL;

--
-- Omezení pro tabulku `News`
--
ALTER TABLE `News`
ADD CONSTRAINT `FK_News_User` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

--
-- Omezení pro tabulku `Role`
--
ALTER TABLE `Role`
ADD CONSTRAINT `FK_Role_Role` FOREIGN KEY (`inherite`) REFERENCES `Role` (`id`);

--
-- Omezení pro tabulku `Subject2Grade`
--
ALTER TABLE `Subject2Grade`
ADD CONSTRAINT `FK_Subject2Grade_Grade` FOREIGN KEY (`Grade_id`) REFERENCES `Grade` (`id`),
ADD CONSTRAINT `FK_Subject2Grade_Subject` FOREIGN KEY (`Subject_id`) REFERENCES `Subject` (`id`) ON DELETE SET NULL;

--
-- Omezení pro tabulku `Topic`
--
ALTER TABLE `Topic`
ADD CONSTRAINT `FK_Topic_Subject2Grade` FOREIGN KEY (`subject2grade_id`) REFERENCES `Subject2Grade` (`id`),
ADD CONSTRAINT `FK_Topic_User` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE SET NULL;

--
-- Omezení pro tabulku `User`
--
ALTER TABLE `User`
ADD CONSTRAINT `FK_User_Role` FOREIGN KEY (`role_id`) REFERENCES `Role` (`id`);

--
-- Omezení pro tabulku `User2UsersGroup`
--
ALTER TABLE `User2UsersGroup`
ADD CONSTRAINT `FK_User2UsersGroup_User` FOREIGN KEY (`User_id`) REFERENCES `User` (`id`),
ADD CONSTRAINT `FK_User2UsersGroup_UsersGroup` FOREIGN KEY (`UsersGroup_id`) REFERENCES `UsersGroup` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
