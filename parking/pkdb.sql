-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2024 at 06:00 AM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pkdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aid` int(9) NOT NULL,
  `firstname` varchar(99) NOT NULL,
  `lastname` varchar(99) NOT NULL,
  `contact_number` varchar(99) NOT NULL,
  `username` varchar(99) NOT NULL,
  `password` varchar(99) NOT NULL,
  `pic` varchar(99) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aid`, `firstname`, `lastname`, `contact_number`, `username`, `password`, `pic`) VALUES
(1, '', '', '', 'LFP-Admin', 'lookingforpark', ''),
(11, 'treeASD', 'lifeASD', '09638527410', 'treeAA', 'zxc', 'uploads/Untitled design (3).png'),
(12, 'tree', 'life', '09638527410', 'tree', 'life', 'uploads/Untitled design (3).png'),
(13, 'scott', 'paurom', '12365897456', 'wind', 'wind', 'uploads/Untitled design (2).png'),
(14, 'asdasd', 'sadasda', 'dsadasd', 'asd', 'asd', 'uploads/Untitled design (4).png'),
(15, 'po', 'po', '03698969636', 'po', 'po', 'uploads/Untitled design (1).png'),
(16, 'scott', 'life', '09638527410', 'tree', 'qwert', 'uploads/Untitled design (4).png'),
(17, 'Line', 'life', '03698969636', 'go', 'go', 'uploads/423619645_343030901417346_8924192821082723110_n.png'),
(18, 'scott', 'paurom', '12365897456', 'gh', 'gh', 'uploads/107993581.png'),
(19, 'myckel', 'dael', '09654581324', 'navi', 'ggbet', 'uploads/419852755_802761958277814_3588920208405655576_n.jpg'),
(20, 'tests', 'test', '09683254569', 'test', 'test', 'uploads/Untitled.png'),
(21, 'admin', 'admin', '03698762356', 'admin', 'admin', 'uploads/default.jpg'),
(22, 'scotts', 'life', '09638527410', 'admin1', 'tree', 'uploads/423619645_343030901417346_8924192821082723110_n.png');

-- --------------------------------------------------------

--
-- Table structure for table `archives`
--

CREATE TABLE `archives` (
  `id` int(9) NOT NULL,
  `Name` varchar(99) NOT NULL,
  `Contact_Number` varchar(99) NOT NULL,
  `Plate_Num` varchar(99) NOT NULL,
  `Vehicle_Type` varchar(99) NOT NULL,
  `Slot` varchar(99) NOT NULL,
  `SlotNumber` varchar(99) NOT NULL,
  `Slot_Code` varchar(99) NOT NULL,
  `Date` varchar(99) NOT NULL,
  `TimeIn` varchar(99) NOT NULL,
  `TimeOut` varchar(99) NOT NULL,
  `In_By` varchar(255) NOT NULL,
  `Out_By` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `archives`
--

INSERT INTO `archives` (`id`, `Name`, `Contact_Number`, `Plate_Num`, `Vehicle_Type`, `Slot`, `SlotNumber`, `Slot_Code`, `Date`, `TimeIn`, `TimeOut`, `In_By`, `Out_By`) VALUES
(1, 'qwer rrty', '789468241', 'ASD 444', 'Motorcycle', 'Slot D', '1', 'Slot D1', '03/17/2024', '01:59 PM', '03:51 PM', '', ''),
(2, 'Pancit Canton', '3265481', 'LKOI 986', 'Car', 'Slot A', '5', 'Slot A5', '03/22/2024', '06:53 PM', '12:12 AM', 'scott paurom', ''),
(5, 'asdasd TEststt', '12359879', 'TYU 789', 'Car', 'Slot A', '1', 'Slot A1', '03/23/2024', '11:59 PM', '01:42 PM', '', 'po po'),
(6, 'Pancit Canton', '65469898321', 'ASD 2131', 'Car', 'Slot D', '2', 'Slot D2', '03/24/2024', '12:01 AM', '11:32 AM', '', 'admin admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(9) NOT NULL,
  `Name` varchar(99) NOT NULL,
  `Contact_Number` varchar(99) NOT NULL,
  `Plate_Num` varchar(99) NOT NULL,
  `Vehicle_Type` varchar(99) NOT NULL,
  `Slot` varchar(99) NOT NULL,
  `SlotNumber` varchar(99) NOT NULL,
  `Slot_Code` varchar(99) NOT NULL,
  `Date` varchar(99) NOT NULL,
  `TimeIn` varchar(99) NOT NULL,
  `TimeOut` varchar(99) NOT NULL,
  `In_By` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Name`, `Contact_Number`, `Plate_Num`, `Vehicle_Type`, `Slot`, `SlotNumber`, `Slot_Code`, `Date`, `TimeIn`, `TimeOut`, `In_By`) VALUES
(2, 'Line Llausas', '7986233658', 'TYU 789', 'Motorcycle', 'Slot B', '1', 'Slot B1', '03/17/2024', '01:44 PM', '', ''),
(4, 'Pancit Canton', '65469898321', 'ASD 213', 'Car', 'Slot D', '5', 'Slot D5', '03/17/2024', '01:58 PM', '', ''),
(6, 'TEST 1', '123654', 'BNM 657', 'Car', 'Slot B', '8', 'Slot B8', '03/17/2024', '02:02 PM', '', ''),
(7, 'Scott Pauroms', '7986233658', 'asd 2333', 'Car', 'Slot A', '2', 'Slot A2', '03/17/2024', '03:50 PM', '', ''),
(12, 'Scott Paurom', '64574652', 'DSG 6587', 'Car', 'Slot C', '3', 'Slot C3', '03/25/2024', '10:59 AM', '', 'po po');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`aid`);

--
-- Indexes for table `archives`
--
ALTER TABLE `archives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `aid` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `archives`
--
ALTER TABLE `archives`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
