-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 19, 2025 at 07:28 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `studentportalsystem_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` enum('Academic','Event','General') NOT NULL,
  `content` text NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `category`, `content`, `status`, `created_at`) VALUES
(5, 'Fee submission details', 'Academic', 'Dear Students,\r\n\r\nThis is to inform you that the fee submission window for the upcoming term is now open.\r\n\r\n📅 Last Date to Submit Fees: [Insert Due Date]\r\n🏦 Payment Methods: Online Transfer | Bank Deposit | Campus Office\r\n\r\nPlease ensure timely payment to avoid any late fines or enrollment issues.\r\nFor assistance, contact the accounts office at [insert email or number].\r\n\r\nThank you for your cooperation.\r\n\r\n– Student Affairs / Accounts Department', 'Active', '2025-07-19 17:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `due_date` date NOT NULL,
  `instructions` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `allow_late` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `title`, `subject`, `due_date`, `instructions`, `attachment`, `allow_late`, `created_at`) VALUES
(16, 'Git and Github (version control system)', 'Software re engneering', '2025-07-22', 'Print hard form Avoid palagrism', '1752929678_1752052697_Software Requirement Engineering.doc', 0, '2025-07-19 12:54:38'),
(17, 'MANAGEMENT AND ORGANIZATIONS', 'ITM', '2025-07-19', 'Handwritten ', '1752929978_A1&2 Management.docx', 0, '2025-07-19 12:59:38');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `grade` varchar(10) NOT NULL,
  `feedback` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `assignment_id`, `student_id`, `file`, `comment`, `submitted_at`, `grade`, `feedback`) VALUES
(13, 16, 30, '1752936128_abstract.jpg', 'name:umar', '2025-07-19 19:42:08', '2/10', 'wrong code'),
(14, 17, 30, '1752936144_download.jpg', '149', '2025-07-19 19:42:24', '8/10', 'good effort');

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

CREATE TABLE `timetables` (
  `id` int(11) NOT NULL,
  `day_of_week` varchar(10) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(50) NOT NULL,
  `teacher` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetables`
--

INSERT INTO `timetables` (`id`, `day_of_week`, `subject`, `start_time`, `end_time`, `room`, `teacher`, `created_at`) VALUES
(8, 'Tuesday', 'Software re engneering', '08:30:00', '21:45:00', 'c-102', 'sir zubair', '2025-07-19 13:00:39'),
(9, 'Tuesday', 'Software requirement engneering', '09:45:00', '11:00:00', 'c-103', 'Mam Amna sabha', '2025-07-19 14:37:15'),
(10, 'Tuesday', 'ITM', '11:00:00', '12:15:00', 'c-106', 'mam mariyam', '2025-07-19 14:38:15'),
(11, 'Tuesday', 'Software Qulaity engneering', '12:15:00', '13:32:00', 'C-209', 'MAM AMINA SABHA ', '2025-07-19 14:39:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `profile_image`) VALUES
(29, 'sir jubair', 'Umarfarooq6153@gmail.com', '$2y$10$foiUY4h.NxCqi7jfHPpeseF80DzGCQ8m./I/7sd5FhekJqd3/WOzm', 'admin', 'profile_29_1752945140.png'),
(30, 'ayesha', 'ayesha@123gmail.com', '$2y$10$h34CxRJhTYzpyNK9WgzGWOCYyWLGQcwCWmj3IKpSAym5VRu4onOae', 'student', 'profile_30_1752944987.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
