-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2019 at 08:15 AM
-- Server version: 5.6.24
-- PHP Version: 5.5.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `aclc_ramp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `online_judges`
--

CREATE TABLE IF NOT EXISTS `online_judges` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_candidates`
--

CREATE TABLE IF NOT EXISTS `tbl_candidates` (
  `cand_id` int(11) NOT NULL,
  `cand_no` varchar(11) NOT NULL DEFAULT '0',
  `cand_name` varchar(50) NOT NULL,
  `cand_pic` varchar(200) NOT NULL DEFAULT 'default-user.png',
  `status` enum('Allow','Eliminate') NOT NULL DEFAULT 'Allow'
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_candidates`
--

INSERT INTO `tbl_candidates` (`cand_id`, `cand_no`, `cand_name`, `cand_pic`, `status`) VALUES
(1, '1F', 'GIALLIO', 'default-user.png', 'Allow'),
(2, '1M', 'GIALLIO', 'default-user.png', 'Allow'),
(3, '2F', 'AZUL', 'default-user.png', 'Allow'),
(4, '2M', 'AZUL', 'default-user.png', 'Allow'),
(5, '3F', 'ROXXO', 'default-user.png', 'Allow'),
(6, '3M', 'ROXXO', 'default-user.png', 'Allow'),
(7, '4F', 'VIERDDY', 'default-user.png', 'Allow'),
(8, '4M', 'VIERDDY', 'default-user.png', 'Allow'),
(9, '5F', 'AZUL', 'default-user.png', 'Allow'),
(10, '5M', 'AZUL', 'default-user.png', 'Allow'),
(11, '6F', 'VIERDDY', 'default-user.png', 'Allow'),
(12, '6M', 'VIERDDY', 'default-user.png', 'Allow'),
(13, '7F', 'CAHEL', 'default-user.png', 'Allow'),
(14, '7M', 'CAHEL', 'default-user.png', 'Allow'),
(15, '8F', 'ROXXO', 'default-user.png', 'Allow'),
(16, '8M', 'ROXXO', 'default-user.png', 'Allow'),
(17, '9F', 'GIALLIO', 'default-user.png', 'Allow'),
(18, '9M', 'GIALLIO', 'default-user.png', 'Allow'),
(19, 'XF', 'CAHEL', 'default-user.png', 'Allow'),
(20, 'XM', 'CAHEL', 'default-user.png', 'Allow');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE IF NOT EXISTS `tbl_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `percentage` int(11) NOT NULL DEFAULT '0',
  `status` enum('Show','Hide') NOT NULL DEFAULT 'Show'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`category_id`, `category_name`, `percentage`, `status`) VALUES
(1, 'HOUSE ATTIRE', 100, 'Show'),
(2, 'SCHOOL UNIFORM ATTIRE', 100, 'Show'),
(3, 'GODS AND GODDESS ATTIRE', 100, 'Show'),
(5, 'Total Ranking', 300, 'Show');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_config`
--

CREATE TABLE IF NOT EXISTS `tbl_config` (
  `config_id` int(11) NOT NULL,
  `event_title` varchar(50) NOT NULL,
  `based_type` enum('Candidate','House') NOT NULL DEFAULT 'House',
  `prompt_msg` varchar(200) NOT NULL,
  `chairman_status` enum('Open','Close') NOT NULL DEFAULT 'Open',
  `judge_status` enum('Open','Close') NOT NULL DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_config`
--

INSERT INTO `tbl_config` (`config_id`, `event_title`, `based_type`, `prompt_msg`, `chairman_status`, `judge_status`) VALUES
(1, 'RAMP REIGN AND RUNWAY 2019', 'House', 'Judging is now closed. Thank you.', 'Close', 'Close');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_criteria`
--

CREATE TABLE IF NOT EXISTS `tbl_criteria` (
  `criteria_id` int(11) NOT NULL,
  `criteria_name` varchar(50) NOT NULL,
  `criteria_points` int(11) NOT NULL DEFAULT '0',
  `criteria_descrp` varchar(50) NOT NULL,
  `category_id` int(11) NOT NULL,
  `status` enum('Show','Hide') NOT NULL DEFAULT 'Show'
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_criteria`
--

INSERT INTO `tbl_criteria` (`criteria_id`, `criteria_name`, `criteria_points`, `criteria_descrp`, `category_id`, `status`) VALUES
(1, 'Stage Presence', 30, '', 1, 'Show'),
(2, 'Theme Suitability', 20, '', 1, 'Show'),
(3, 'Bearing (Confidence and Poise)', 40, '', 1, 'Show'),
(4, 'Audience Impact', 10, '', 1, 'Show'),
(5, 'Stage Presence', 30, '', 2, 'Show'),
(6, 'Theme Suitability', 20, '', 2, 'Show'),
(7, 'Bearing (Confidence and Poise)', 40, '', 2, 'Show'),
(8, 'Audience Impact', 10, '', 2, 'Show'),
(9, 'Stage Presence', 30, '', 3, 'Show'),
(10, 'Theme Suitability', 20, '', 3, 'Show'),
(11, 'Bearing (Confidence and Poise)', 40, '', 3, 'Show'),
(12, 'Audience Impact', 10, '', 3, 'Show'),
(16, 'House Attire Total', 100, 'Total Score for House Attire Category', 5, 'Show'),
(17, 'School Uniform Total', 100, 'Total Score for School Uniform Category', 5, 'Show'),
(18, 'G & G Total', 100, 'Total Score for God and Goddess Attire', 5, 'Show');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_scores`
--

CREATE TABLE IF NOT EXISTS `tbl_scores` (
  `score_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `criteria_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `score_points` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date_saved` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `pass_word` varchar(50) NOT NULL,
  `user_type` enum('Admin','Chairman','Judge') NOT NULL DEFAULT 'Judge',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `full_name`, `user_name`, `pass_word`, `user_type`, `status`) VALUES
(1, '', 'admin', 'admin123', 'Admin', 'Active'),
(2, 'Judge # 1', 'judge1', 'judge1', 'Judge', 'Active'),
(3, 'Judge # 2', 'judge2', 'judge2', 'Judge', 'Active'),
(4, 'Judge # 3', 'judge3', 'judge3', 'Judge', 'Active'),
(5, 'Judge # 4', 'judge4', 'judge4', 'Judge', 'Active'),
(6, 'Committee', 'comm', 'c0mm', 'Chairman', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `online_judges`
--
ALTER TABLE `online_judges`
  ADD PRIMARY KEY (`session_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_candidates`
--
ALTER TABLE `tbl_candidates`
  ADD PRIMARY KEY (`cand_id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `tbl_config`
--
ALTER TABLE `tbl_config`
  ADD PRIMARY KEY (`config_id`);

--
-- Indexes for table `tbl_criteria`
--
ALTER TABLE `tbl_criteria`
  ADD PRIMARY KEY (`criteria_id`), ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_scores`
--
ALTER TABLE `tbl_scores`
  ADD PRIMARY KEY (`score_id`), ADD KEY `criteria_id` (`criteria_id`), ADD KEY `user_id` (`user_id`), ADD KEY `cand_id` (`cand_id`), ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`), ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `online_judges`
--
ALTER TABLE `online_judges`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `tbl_candidates`
--
ALTER TABLE `tbl_candidates`
  MODIFY `cand_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `tbl_criteria`
--
ALTER TABLE `tbl_criteria`
  MODIFY `criteria_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `tbl_scores`
--
ALTER TABLE `tbl_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `online_judges`
--
ALTER TABLE `online_judges`
ADD CONSTRAINT `online_judges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_criteria`
--
ALTER TABLE `tbl_criteria`
ADD CONSTRAINT `tbl_criteria_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tbl_category` (`category_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_scores`
--
ALTER TABLE `tbl_scores`
ADD CONSTRAINT `tbl_scores_ibfk_1` FOREIGN KEY (`cand_id`) REFERENCES `tbl_candidates` (`cand_id`) ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_scores_ibfk_2` FOREIGN KEY (`criteria_id`) REFERENCES `tbl_criteria` (`criteria_id`) ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_scores_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_scores_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `tbl_category` (`category_id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
