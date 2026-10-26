-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2026
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
-- Database: `neighborhood_waste`
--

-- --------------------------------------------------------

--
-- Table structure for table `users` (For Login & Registration)
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Staff') DEFAULT 'Staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping default user account (Username: admin, Password: password123)
--
INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', '2026-10-21 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `resident_name` varchar(150) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `purok_name` varchar(100) NOT NULL,
  `collection_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `waste_type` enum('Biodegradable','Non-Biodegradable','Recyclable','Bulk') NOT NULL,
  `status` enum('Scheduled','In Progress','Completed','Delayed') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--[cite: 2]
--

INSERT INTO `schedules` (`schedule_id`, `resident_name`, `street_address`, `purok_name`, `collection_date`, `start_time`, `end_time`, `waste_type`, `status`, `created_at`) VALUES
(1, 'Maria Clara', 'poblacion street', 'purok mangga', '2026-08-27', '13:33:00', '13:34:00', 'Biodegradable', 'Delayed', '2026-08-25 13:30:05'),
(2, 'Cluwe Yuji', 'daanbantayan,bagay', 'Purok Sabana 4', '2026-08-28', '11:07:00', '15:01:00', 'Recyclable', 'In Progress', '2026-08-26 11:04:14'),
(3, 'Rave Clint Ramirez', 'Bancal Poblacion', 'Purok Mangga', '2026-08-31', '13:00:00', '14:00:00', 'Non-Biodegradable', 'In Progress', '2026-08-26 11:07:15'),
(4, 'Rai Khen Ramirez', 'Bancal Poblacion', 'Purok Kulo', '2026-09-28', '22:15:00', '13:12:00', 'Biodegradable', 'Scheduled', '2026-09-02 11:13:03');

-- --------------------------------------------------------

--
-- Table structure for table `transactions` (For activity logs / transaction history)
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action_details` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`transaction_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;