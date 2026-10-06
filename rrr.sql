-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2026 at 06:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rrr`
--

-- --------------------------------------------------------

--
-- Table structure for table `online_judges`
--

CREATE TABLE `online_judges` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_candidates`
--

CREATE TABLE `tbl_candidates` (
  `cand_id` int(11) NOT NULL,
  `cand_no` varchar(11) NOT NULL DEFAULT '0',
  `cand_name` varchar(50) NOT NULL,
  `cand_pic` varchar(200) NOT NULL DEFAULT 'default-user.png',
  `status` enum('Allow','Eliminate') NOT NULL DEFAULT 'Allow'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_candidates`
--

INSERT INTO `tbl_candidates` (`cand_id`, `cand_no`, `cand_name`, `cand_pic`, `status`) VALUES
(25, '1F', 'AZUL', 'faye D. Sargadillos.jpg', 'Allow'),
(26, '1M', 'AZUL', 'Christian A. Tadena.jpg', 'Allow'),
(27, '2F', 'ROXXO', 'Juliana Mae S. Roma.jpg', 'Allow'),
(28, '2M', 'ROXXO', 'Vince Ivan Jay E Rico.jpg', 'Allow'),
(29, '3F', 'VIERRDY', 'Kimberly P. Ferrer.jpg', 'Allow'),
(30, '3M', 'VIERRDY', 'Godfreil Christian B. Dano.jpg', 'Allow'),
(31, '4F', 'CAHEL', 'Jerah Mae V. Villa.jpg', 'Allow'),
(32, '4M', 'CAHEL', 'Cedric Lee T. Macasero.jpg', 'Allow'),
(33, '5F', 'GIALLO', 'Loise Gabriela L. Palacio.jpg', 'Allow'),
(34, '5M', 'GIALLO', 'Ralph Edison M. Pajarillo.jpg', 'Allow');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `percentage` int(11) NOT NULL DEFAULT 0,
  `status` enum('Show','Hide') NOT NULL DEFAULT 'Show'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`category_id`, `category_name`, `percentage`, `status`) VALUES
(1, 'HOUSE SHIRT ATTIRE', 100, 'Show'),
(2, 'SPORTS ATTIRE', 100, 'Show'),
(3, 'PRODUCTION NUMBER', 100, 'Show'),
(5, 'Total Ranking', 300, 'Show');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_config`
--

CREATE TABLE `tbl_config` (
  `config_id` int(11) NOT NULL,
  `event_title` varchar(50) NOT NULL,
  `based_type` enum('Candidate','House') NOT NULL DEFAULT 'House',
  `prompt_msg` varchar(200) NOT NULL,
  `chairman_status` enum('Open','Close') NOT NULL DEFAULT 'Open',
  `judge_status` enum('Open','Close') NOT NULL DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_config`
--

INSERT INTO `tbl_config` (`config_id`, `event_title`, `based_type`, `prompt_msg`, `chairman_status`, `judge_status`) VALUES
(1, 'MR AND MRS INTRAMS 2026', 'House', 'Judging is now closed. Thank you.', 'Close', 'Close');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_criteria`
--

CREATE TABLE `tbl_criteria` (
  `criteria_id` int(11) NOT NULL,
  `criteria_name` varchar(50) NOT NULL,
  `criteria_points` int(11) NOT NULL DEFAULT 0,
  `criteria_descrp` varchar(50) NOT NULL,
  `category_id` int(11) NOT NULL,
  `status` enum('Show','Hide') NOT NULL DEFAULT 'Show'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_criteria`
--

INSERT INTO `tbl_criteria` (`criteria_id`, `criteria_name`, `criteria_points`, `criteria_descrp`, `category_id`, `status`) VALUES
(1, 'Stage Presence', 25, '', 1, 'Show'),
(2, 'Attire/Costume ', 20, '', 1, 'Show'),
(3, 'Personality, Projection & Poise', 35, '', 1, 'Show'),
(4, 'Audience Impact', 10, '', 1, 'Show'),
(5, 'Stage Presence', 15, '', 2, 'Show'),
(6, 'Props', 20, '', 2, 'Show'),
(7, 'Sports Identity', 30, '', 2, 'Show'),
(8, 'Audience Impact', 10, '', 2, 'Show'),
(9, 'Stage Presence', 25, '', 3, 'Show'),
(12, 'Audience Impact', 10, '', 3, 'Show'),
(16, 'House Attire Total', 100, 'Total Score for House Attire Category', 5, 'Show'),
(17, 'School Uniform Total', 100, 'Total Score for School Uniform Category', 5, 'Show'),
(18, 'Production Number Total', 100, 'Total Score for Production Number', 5, 'Show'),
(19, 'Gracefulness', 35, '', 3, 'Show'),
(20, 'Attire/Costume ', 15, '', 3, 'Show'),
(21, 'Attractiveness/Beauty', 15, '', 3, 'Show'),
(22, 'Attractiveness/Beauty', 10, '', 2, 'Show'),
(23, 'Attractiveness/Beauty', 10, '', 1, 'Show'),
(24, 'Personality, Projection & Poise', 15, '', 2, 'Show');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_scores`
--

CREATE TABLE `tbl_scores` (
  `score_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `criteria_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `score_points` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date_saved` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `pass_word` varchar(50) NOT NULL,
  `user_type` enum('Admin','Chairman','Judge') NOT NULL DEFAULT 'Judge',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

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
  ADD PRIMARY KEY (`criteria_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_scores`
--
ALTER TABLE `tbl_scores`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `criteria_id` (`criteria_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cand_id` (`cand_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `online_judges`
--
ALTER TABLE `online_judges`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `tbl_candidates`
--
ALTER TABLE `tbl_candidates`
  MODIFY `cand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_criteria`
--
ALTER TABLE `tbl_criteria`
  MODIFY `criteria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tbl_scores`
--
ALTER TABLE `tbl_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
