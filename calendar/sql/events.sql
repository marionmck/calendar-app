-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2021 at 12:16 PM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `root`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` tinytext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `location` tinytext NOT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `user_id`, `title`, `description`, `location`, `start_date_time`, `end_date_time`) VALUES
(1, 16, 'Test Event 3', 'Test event description 3.', 'London', '2021-04-21 11:30:00', '2021-04-21 15:30:00'),
(4, 4, 'Teaching Python Basics', 'Teaching the python basics class.', 'Room 350', '2021-05-07 14:00:00', '2021-05-07 16:00:00'),
(5, 16, 'Volunteering Work', 'Volunteering Work in London.', 'London', '2021-04-24 08:00:00', '2021-04-24 10:15:00'),
(6, 4, 'Test Event - Time 9pm', 'Test event 9pm description.', 'Newcastle', '2021-04-19 21:00:00', '2021-04-19 22:30:00'),
(11, 16, 'Test Event 2', 'Test event description 2.', 'Newcastle', '2021-04-19 09:30:00', '2021-04-19 13:00:00'),
(13, 16, 'Test Event 1', 'Test description one.', 'Newcastle', '2021-04-19 02:45:00', '2021-04-19 06:00:00'),
(25, 16, 'New Event', 'Studying at the library.', 'Library', '2021-05-23 04:45:00', '2021-05-23 08:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `events_fk1` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
