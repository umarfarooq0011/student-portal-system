-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 10, 2025 at 10:04 AM
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
(3, 'Fee submission details ', 'Academic', 'Due to off down systen challans are not geenrated by Faham so please visit ssc office to get challans for fee submission', 'Active', '2025-07-09 22:49:10');

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
(11, 'Version control system', 'Software re engneering', '2025-07-14', 'Submission:\r\n•	GitHub repo link\r\n•	2–3 screenshots (commit, branch, merge)\r\n•	Short write-up: What did you learn?\r\n________________________________________\r\n', '1752082927_Software Reeng Assignment 2 & 3.docx', 1, '2025-07-09 17:42:07');

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
(1, 7, 13, '1752056445_1752052697_Software Requirement Engineering.doc', '', '2025-07-09 15:20:45', '8/10', 'good effort'),
(2, 6, 13, '1752057845_delete_student.png', '', '2025-07-09 15:44:05', '9/10', 'best'),
(3, 1, 13, '1752058403_Mid Term Batch Spring 2024.Spring 2023 ,Fall 2022 Semester III,V(R),VI(R),VI(L),VII(L) Finalized by 2952.xlsx', 'done', '2025-07-09 15:53:23', '', ''),
(4, 8, 13, '1752061500_download.jpg', '', '2025-07-09 16:45:00', '', ''),
(5, 11, 13, '1752083081_logo.png', '', '2025-07-09 22:44:41', '0/10', 'wrong file');

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
(2, 'Friday', 'Software re engneering', '10:30:00', '12:00:00', 'C-120', 'sir zubair', '2025-07-09 14:37:21'),
(3, 'Friday', 'Software requirement engneering', '12:30:00', '13:45:00', 'c-108', 'Mam Amna sabha', '2025-07-09 14:44:28'),
(5, 'Tuesday', 'ITM', '10:22:00', '12:21:00', 'm-104', 'mr ali', '2025-07-09 16:00:10'),
(6, 'Thursday', 'Software Qulaity engneering', '10:20:00', '12:23:00', 'c-303', 'hamna ', '2025-07-09 16:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`) VALUES
(13, 'OMER AWAN', 'Umarfarooq6153@gmail.com', '$2y$10$/vAp8Ttoo9Gex7wiZIM0x.z5zPWlbAUCMGtFv4FmylTNmvXqcHYKS', 'student'),
(14, 'zarishnasir', 'zarishnasir12345@gmail.com', '$2y$10$J6Y2wBpyrHFe9fR/pmV9feJVNmC/4cAFqdFv90FFKa2tDwCxgNrSS', 'admin'),
(15, 'ALI', 'usamanasir1234@gmail.com', '$2y$10$qS5u0pgn2DYpqWj.KkwSkuGSOB2RbWLuHf0zFaMnbPXYV55IOJdE6', 'student'),
(16, 'ALI', 'ali123@gmail.com', '$2y$10$czUBlfTSHUoFgVUb42Wsfu2ERy.FHiSL4r9xcxwT6rdUPMqKbETvC', 'admin');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
