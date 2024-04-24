-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 24, 2024 at 08:49 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `telemed`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `service` varchar(255) DEFAULT NULL,
  `doctor` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`booking_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `service`, `doctor`, `time`, `patient_name`) VALUES
(1, '5', 'Kamau', '9:40 pm', 'tim.makori@gmail.com'),
(2, '5', 'Kamau', '9:40 pm', 'tim.makori@gmail.com'),
(3, '5', 'Kamau', '9:40 pm', 'tim.makori@gmail.com'),
(4, '7', 'Arap', '9:40 pm', 'tim.makori@gmail.com'),
(5, '5', 'Stella Mwangi', '9:40 am', 'tim.makori@gmail.com'),
(6, '5', 'Millicent', '9:40 am', 'tim.makori@gmail.com'),
(7, '10', 'Millicent', '9:40 am', 'tim.makori@gmail.com'),
(8, '8', 'Millicent', '9:40 am', 'tim.makori@gmail.com'),
(9, '8', 'Millicent', '9:40 am', 'tim.makori@gmail.com'),
(10, '8', 'Millicent', '9:40 am', 'tim.makori@gmail.com'),
(11, '5', 'dsds', 'cscs', 'tim.makori@gmail.com'),
(12, '8', 'Mwangi', '8:00 AM', 'tim.makori@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

DROP TABLE IF EXISTS `booking_details`;
CREATE TABLE IF NOT EXISTS `booking_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `doctor` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `passcode` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`id`, `user_id`, `doctor`, `service`, `platform`, `link`, `username`, `passcode`) VALUES
(4, 1, 'Dr. Sarah Connors', 'General Checkup', 'Zoom', 'https://zoom.us/j/100000000', 'john100', 'pass100'),
(5, 2, 'Dr. Michael Smith', 'Dermatology Consultation', 'Microsoft Teams', 'https://teams.microsoft.com/l/meetup-join/19%meeting_abc', 'jane200', 'pass200'),
(6, 3, 'Dr. Rajesh Koothrappali', 'Pediatric Review', 'Google Meet', 'https://meet.google.com/xyz-abcd-efg', 'emily300', 'pass300');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE IF NOT EXISTS `doctors` (
  `doctor_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `specialization` varchar(255) NOT NULL,
  PRIMARY KEY (`doctor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `specialization`) VALUES
(1, 'Dr. Stella Mwangi', 'Family Medicine'),
(2, 'Dr. Millicent', 'Psychology');

-- --------------------------------------------------------

--
-- Table structure for table `invoices_list`
--

DROP TABLE IF EXISTS `invoices_list`;
CREATE TABLE IF NOT EXISTS `invoices_list` (
  `invoice_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `time_raised` datetime NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','cancelled','not paid') DEFAULT 'pending',
  `payer_phone` varchar(15) DEFAULT NULL,
  `code` varchar(200) DEFAULT NULL,
  `invoice_code` varchar(50) NOT NULL,
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `invoice_code` (`invoice_code`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `invoices_list`
--

INSERT INTO `invoices_list` (`invoice_id`, `user_id`, `service_id`, `time_raised`, `amount`, `status`, `payer_phone`, `code`, `invoice_code`) VALUES
(1, 1, 101, '2024-04-23 22:19:52', 150.00, 'paid', '123-456-7890', NULL, 'INV00123'),
(2, 1, 5, '2024-04-24 00:04:49', 4500.00, '', NULL, NULL, '6628227198bb9'),
(3, 1, 7, '2024-04-24 00:10:50', 500.00, 'paid', '0721548569', NULL, 'EGD5TEST'),
(4, 1, 5, '2024-04-24 00:41:12', 4500.00, 'paid', '0721548569', '3FBDGTE2', '66282af8f0860'),
(5, 1, 5, '2024-04-24 00:48:59', 4500.00, 'paid', '0721548569', 'dffv3432432', '66282ccb33cd2'),
(6, 1, 10, '2024-04-24 01:18:07', 5000.00, 'paid', '0721548569', 'wow3434', '6628339fb097b'),
(7, 1, 8, '2024-04-24 01:21:54', 4000.00, 'pending', NULL, NULL, '662834828eb5d'),
(8, 1, 8, '2024-04-24 01:23:20', 4000.00, 'pending', NULL, NULL, '662834d8522a6'),
(9, 1, 8, '2024-04-24 01:24:20', 4000.00, 'paid', '0721548569', '3FBDGTE2d', '662835149d9ce'),
(10, 1, 5, '2024-04-24 02:04:59', 4500.00, 'paid', 'SCS', 'SCS', '66283e9bdc688'),
(11, 1, 8, '2024-04-24 10:00:45', 4000.00, 'paid', '0721548569', '3FBDGTE22', '6628ae1d21c60');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE IF NOT EXISTS `schedules` (
  `schedule_id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` int NOT NULL,
  `service_id` int NOT NULL,
  `week_day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `time_slot` time NOT NULL,
  `is_booked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`schedule_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `service_id` (`service_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `doctor_id`, `service_id`, `week_day`, `time_slot`, `is_booked`) VALUES
(1, 1, 1, 'Monday', '09:00:00', 0),
(2, 1, 2, 'Tuesday', '14:00:00', 1),
(3, 2, 1, 'Wednesday', '11:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `services_list`
--

DROP TABLE IF EXISTS `services_list`;
CREATE TABLE IF NOT EXISTS `services_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services_list`
--

INSERT INTO `services_list` (`id`, `name`, `description`, `price`, `status`, `date_created`, `date_updated`) VALUES
(5, 'Tele-Adult psychiatry', '\r\nAIC Kijabe Hospital offers Tele-Adult Psychiatry, enabling remote mental health diagnosis, treatment, and prevention via telecommunication technology.', 4500, 1, '2024-04-08 21:09:21', '2024-04-08 21:09:21'),
(6, 'Tele Family Medicine Consultation', '\r\nAIC Kijabe Hospital offers Tele Family Medicine Consultation, providing standard family medicine services remotely through digital communication platforms.', 500, 1, '2024-04-08 21:10:28', '2024-04-08 21:10:28'),
(7, 'Tele General Medicine Consultation', '\r\nAIC Kijabe Hospital provides Tele General Medicine Consultation, delivering primary care services remotely via digital communication technologies.', 500, 1, '2024-04-08 21:11:14', '2024-04-08 21:11:14'),
(8, 'Tele-Adult psychiatry Followup', 'AIC Kijabe Hospital offers Tele-Adult Psychiatry Follow-up, facilitating ongoing psychiatric care and support remotely through digital communication.', 4000, 1, '2024-04-08 21:11:59', '2024-04-08 21:11:59'),
(9, 'Tele-Child psychiatry', '\r\nAIC Kijabe Hospital offers Tele-Child Psychiatry, providing remote mental health services for children and adolescents through digital communication technology.', 6000, 1, '2024-04-08 21:13:03', '2024-04-08 21:13:03'),
(10, 'Tele-Child psychiatry Folowup', '\r\nAIC Kijabe Hospital offers Tele-Child Psychiatry followup, providing remote mental health services for children and adolescents through digital communication technology.', 5000, 1, '2024-04-08 21:13:40', '2024-04-08 21:13:40'),
(12, 'Tele-Nutrition Consultation', 'Tele-Nutrition Consultation utilizes digital communication tools to offer personalized dietary advice and nutritional planning remotely, enhancing overall health.', 500, 1, '2024-04-08 21:17:16', '2024-04-08 21:17:16'),
(13, 'Tele-Psychotherapy', 'Tele-Psychotherapy provides mental health therapy remotely through digital platforms, facilitating access to psychological support and treatment.', 1000, 1, '2024-04-08 21:17:57', '2024-04-08 21:17:57');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
CREATE TABLE IF NOT EXISTS `uploads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `user_id`, `file_path`, `uploaded_at`) VALUES
(1, 1, 'uploads/invoice_receipt.pdf', '2024-04-23 23:35:31'),
(2, 1, 'uploads/S24-1225, Ellen Gould, Path.pdf', '2024-04-23 23:36:25'),
(3, 1, 'uploads/php_tutorial (1).pdf', '2024-04-24 07:02:41'),
(4, 1, 'uploads/Branding-Identity-1.pdf', '2024-04-24 07:02:55'),
(5, 1, 'uploads/PHP MASTER CLASS.docx', '2024-04-24 07:02:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lastlogin` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `lastlogin`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'tim.makori@gmail.com', '$2y$10$XF/pVmvI.jN/cpJJMRMR7ublNvnQY68wXBMQ1s7SHeeWMflOGGlPq', '2024-04-22 13:52:45', '2024-04-22 13:52:45', '2024-04-22 14:18:03');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
