-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 26, 2013 at 10:46 PM
-- Server version: 5.5.31
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `transphorm`
--

-- --------------------------------------------------------

--
-- Table structure for table `agencies`
--

CREATE TABLE `agencies` (
  `agency_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `agency_name` varchar(80) NOT NULL,
  `admin_user_id` int(10) NOT NULL,
  PRIMARY KEY (`agency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `config_settings`
--

CREATE TABLE `config_settings` (
  `setting_name` varchar(60) NOT NULL,
  `value` varchar(600) NOT NULL,
  PRIMARY KEY (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `config_settings`
--

INSERT INTO `config_settings` (`setting_name`, `value`) VALUES
('default_lang_model_id', '0'),
('privacy_policy', ''),
('terms_of_use', '');

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

CREATE TABLE `discussions` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `doc_id` int(11) unsigned NOT NULL,
  `comment_text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `doc_uploads`
--

CREATE TABLE `doc_uploads` (
  `doc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orig_doc_id` int(11) unsigned NOT NULL,
  `translation_model_id` int(10) unsigned NOT NULL,
  `doc_title` varchar(200) NOT NULL,
  `doc_language` smallint(5) NOT NULL,
  `doc_topic` varchar(100) NOT NULL,
  `doc_audience` varchar(100) NOT NULL,
  `doc_reading_level` varchar(100) NOT NULL,
  `other_notes` varchar(700) NOT NULL,
  `claimed_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `postedited` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `completed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sentences_completed` int(10) unsigned NOT NULL,
  `total_sentences` int(10) unsigned NOT NULL,
  `date_uploaded` int(10) unsigned NOT NULL,
  `date_claimed` int(10) NOT NULL,
  `date_postedited` int(10) NOT NULL,
  `date_completed` int(10) NOT NULL,
  PRIMARY KEY (`doc_id`),
  KEY `revision_doc_id` (`orig_doc_id`),
  FULLTEXT KEY `doc_title` (`doc_title`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'admin', 'Administrative user, has access to everything.'),
(3, 'agency_admin', 'Agency administrator, can create users for a given agency');

-- --------------------------------------------------------

--
-- Table structure for table `roles_users`
--

CREATE TABLE `roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles_users`
--

INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `source_sentences`
--

CREATE TABLE `source_sentences` (
  `sentence_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `source_text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sentence_id`),
  KEY `doc_id` (`doc_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `target_languages`
--

CREATE TABLE `target_languages` (
  `lang_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `lang_code` varchar(6) NOT NULL,
  `lang_name` varchar(50) NOT NULL,
  PRIMARY KEY (`lang_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `target_languages`
--

INSERT INTO `target_languages` (`lang_id`, `lang_code`, `lang_name`) VALUES
(1, 'es', 'Spanish'),
(2, 'vi', 'Vietnamese'),
(3, 'ru', 'Russian'),
(4, 'zh-CHS', 'Chinese (Simplified)');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic` varchar(100) NOT NULL,
  PRIMARY KEY (`topic_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`topic_id`, `topic`) VALUES
(1, 'Abuse'),
(2, 'Accidents'),
(3, 'Addiction'),
(4, 'Alcohol and Drugs'),
(5, 'Behaviour'),
(6, 'Behavioural Disorders'),
(7, 'Behavioural Problems'),
(8, 'Beleifs'),
(9, 'Communication Disorders'),
(10, 'Community Care'),
(11, 'Conception'),
(12, 'Contraceptives'),
(13, 'Crime'),
(14, 'Diet'),
(15, 'Disabilities'),
(16, 'Disability Equipment'),
(17, 'Disasters'),
(18, 'Emergency Preparedness'),
(19, 'Diseases'),
(20, 'Cancer'),
(21, 'Infectious Diseases'),
(22, 'HIV'),
(23, 'Flu'),
(24, 'STD'),
(25, 'Tuberculosis'),
(26, 'Viral Gastroenteritis'),
(27, 'Geriatric Health'),
(28, 'Asthma'),
(29, 'Drugs'),
(30, 'Education'),
(31, 'Environment'),
(32, 'Families'),
(33, 'Foodstuff'),
(34, 'Hazardous substances'),
(35, 'Health care'),
(36, 'Maternal and Child Health'),
(37, 'Health promotion'),
(38, 'Oral Health'),
(39, 'Homosexual people'),
(40, 'Human body'),
(41, 'Industry'),
(42, 'Injuries'),
(43, 'Life events'),
(44, 'Living conditions'),
(45, 'Medicine'),
(46, 'Immunizations'),
(47, 'Medicines'),
(48, 'Nutrition'),
(49, 'Occupational health'),
(50, 'Parents'),
(51, 'People with disabilities'),
(52, 'Physical activity'),
(53, 'Pollution'),
(54, 'Pregnancy'),
(55, 'Pregnancy complications'),
(56, 'Prevention'),
(57, 'Psychotherapy'),
(58, 'NT1 Behaviour therapy'),
(59, 'NT1 Group therapy'),
(60, 'NT1 Music therapy'),
(61, 'Relationships'),
(62, 'Residential care'),
(63, 'Risks'),
(64, 'Safety'),
(65, 'Safety equipment'),
(66, 'Self care'),
(67, 'Sexual problems'),
(68, 'Sexuality'),
(69, 'Sleeping disorders'),
(70, 'Smoking'),
(71, 'Stress'),
(72, 'Surgery'),
(73, 'Symptoms'),
(74, 'Tests'),
(75, 'Therapy'),
(76, 'Victims'),
(77, 'Violence'),
(78, 'Waste'),
(79, 'Weight'),
(80, 'Women'),
(81, 'Workers');

-- --------------------------------------------------------

--
-- Table structure for table `translated_sentences`
--

CREATE TABLE `translated_sentences` (
  `sentence_id` int(11) unsigned NOT NULL,
  `doc_id` int(11) NOT NULL,
  `mt_text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `postedit_text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sentence_id`),
  KEY `doc_id` (`doc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `translated_sentences_generic`
--

CREATE TABLE `translated_sentences_generic` (
  `sentence_id` int(11) unsigned NOT NULL,
  `doc_id` int(11) NOT NULL,
  `mt_text_generic` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `preferred_over_default` char(1) NOT NULL,
  PRIMARY KEY (`sentence_id`),
  KEY `doc_id` (`doc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `translator_hub_models`
--

CREATE TABLE `translator_hub_models` (
  `model_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `descriptive_name` varchar(150) NOT NULL,
  `category_code` varchar(100) NOT NULL,
  `target_lang_id` smallint(5) unsigned NOT NULL,
  `date_added` int(10) unsigned NOT NULL,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `translator_hub_models`
--

INSERT INTO `translator_hub_models` (`model_id`, `descriptive_name`, `category_code`, `target_lang_id`, `date_added`) VALUES
(0, 'Generic model', '', 0, 1368774818);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` char(50) NOT NULL,
  `agency_id` int(10) unsigned NOT NULL,
  `date_created` int(10) NOT NULL,
  `hide_email` tinyint(1) unsigned NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `agency_id`, `date_created`, `hide_email`, `logins`, `last_login`) VALUES
(1, 'admin@example.com', 'admin', 'f0f31b48cede8af6ea5c922344d23f52477aef95e86440bac7', 0, 1371614400, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_languages`
--

CREATE TABLE `user_languages` (
  `user_id` int(11) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_pass_resets`
--

CREATE TABLE `user_pass_resets` (
  `reset_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `token` varchar(15) NOT NULL,
  `expire_timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`reset_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `roles_users`
--
ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
