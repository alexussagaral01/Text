-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2025 at 02:50 PM
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
-- Database: `practice`
--

-- --------------------------------------------------------

--
-- Table structure for table `consultation`
--

CREATE TABLE `consultation` (
  `consultID` int(11) NOT NULL,
  `petID` int(11) NOT NULL,
  `vetID` int(11) NOT NULL,
  `consultDate` date NOT NULL,
  `diagnoses` varchar(30) NOT NULL,
  `prescription` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pet`
--

CREATE TABLE `pet` (
  `petID` int(11) NOT NULL,
  `petName` varchar(30) NOT NULL,
  `petType` varchar(30) NOT NULL,
  `petBreed` varchar(30) NOT NULL,
  `petBdate` date NOT NULL,
  `petOwnerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petowner`
--

CREATE TABLE `petowner` (
  `petOwnerID` int(11) NOT NULL,
  `petOwnerFName` varchar(30) NOT NULL,
  `petOwnerLName` varchar(30) NOT NULL,
  `petOwnerBDate` date NOT NULL,
  `petOwnerTelNo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petowner`
--

INSERT INTO `petowner` (`petOwnerID`, `petOwnerFName`, `petOwnerLName`, `petOwnerBDate`, `petOwnerTelNo`) VALUES
(1, 'testing (DELETED)', 'test (DELETED)', '2025-10-06', 999292929),
(2, 'sample', 'sample', '2025-09-02', 2131231213),
(4, 'examples', 'example', '2025-10-01', 2131312);

-- --------------------------------------------------------

--
-- Table structure for table `veterinarians`
--

CREATE TABLE `veterinarians` (
  `vetID` int(11) NOT NULL,
  `vetFName` varchar(30) DEFAULT NULL,
  `vetLName` varchar(30) DEFAULT NULL,
  `vetAddress` varchar(255) DEFAULT NULL,
  `vetSpecial` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `veterinarians`
--

INSERT INTO `veterinarians` (`vetID`, `vetFName`, `vetLName`, `vetAddress`, `vetSpecial`) VALUES
(1, 'alexus', '', 'cebu', 'sample  '),
(2, 'alexus', '', 'cebu', 'sample  '),
(3, 'test (DELETED)', 'tesst (DELETED)', 'test', 'test  '),
(4, 'test', 'test', 'cebu', 'sample  ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `consultation`
--
ALTER TABLE `consultation`
  ADD PRIMARY KEY (`consultID`),
  ADD KEY `petID` (`petID`),
  ADD KEY `vetID` (`vetID`);

--
-- Indexes for table `pet`
--
ALTER TABLE `pet`
  ADD PRIMARY KEY (`petID`),
  ADD KEY `petOwnerID` (`petOwnerID`);

--
-- Indexes for table `petowner`
--
ALTER TABLE `petowner`
  ADD PRIMARY KEY (`petOwnerID`);

--
-- Indexes for table `veterinarians`
--
ALTER TABLE `veterinarians`
  ADD PRIMARY KEY (`vetID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consultation`
--
ALTER TABLE `consultation`
  MODIFY `consultID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pet`
--
ALTER TABLE `pet`
  MODIFY `petID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `petowner`
--
ALTER TABLE `petowner`
  MODIFY `petOwnerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `veterinarians`
--
ALTER TABLE `veterinarians`
  MODIFY `vetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consultation`
--
ALTER TABLE `consultation`
  ADD CONSTRAINT `consultation_ibfk_1` FOREIGN KEY (`petID`) REFERENCES `pet` (`petID`),
  ADD CONSTRAINT `consultation_ibfk_2` FOREIGN KEY (`vetID`) REFERENCES `veterinarians` (`vetID`);

--
-- Constraints for table `pet`
--
ALTER TABLE `pet`
  ADD CONSTRAINT `pet_ibfk_1` FOREIGN KEY (`petOwnerID`) REFERENCES `petowner` (`petOwnerID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
