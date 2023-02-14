-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2023 at 05:19 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kviz_znam_baza`
--

-- --------------------------------------------------------

--
-- Table structure for table `kvizi`
--

CREATE TABLE `kvizi` (
  `id_kviza` int(11) NOT NULL,
  `id_uporabnika` int(11) NOT NULL,
  `naslov_kviza` char(50) COLLATE utf8_slovenian_ci NOT NULL,
  `opis` varchar(300) COLLATE utf8_slovenian_ci DEFAULT NULL,
  `geslo_kviza` char(32) COLLATE utf8_slovenian_ci NOT NULL,
  `datum_kviza` datetime NOT NULL,
  `enkrat` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `odgovori`
--

CREATE TABLE `odgovori` (
  `id_odgovora` int(11) NOT NULL,
  `id_uporabnika` int(11) NOT NULL,
  `id_vprasanja` int(11) NOT NULL,
  `odgovor` char(1) COLLATE utf8_slovenian_ci NOT NULL,
  `datum_odgovora` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rezultati`
--

CREATE TABLE `rezultati` (
  `id_rezultata` int(11) NOT NULL,
  `id_uporabnika` int(11) NOT NULL,
  `id_kviza` int(11) NOT NULL,
  `st_pravilnih` int(11) NOT NULL DEFAULT 0,
  `st_vprasanj` int(11) NOT NULL,
  `datum_rezultata` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uporabniki`
--

CREATE TABLE `uporabniki` (
  `id_uporabnika` int(11) NOT NULL,
  `ime` varchar(30) COLLATE utf8_slovenian_ci NOT NULL,
  `priimek` varchar(30) COLLATE utf8_slovenian_ci NOT NULL,
  `uporabnisko_ime` varchar(30) COLLATE utf8_slovenian_ci NOT NULL,
  `geslo` varchar(32) COLLATE utf8_slovenian_ci NOT NULL,
  `datum_registracije` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vprasanja`
--

CREATE TABLE `vprasanja` (
  `id_vprasanja` int(11) NOT NULL,
  `id_kviza` int(11) NOT NULL,
  `vprasanje` char(200) COLLATE utf8_slovenian_ci NOT NULL,
  `odgovor_A` char(50) COLLATE utf8_slovenian_ci NOT NULL,
  `odgovor_B` char(50) COLLATE utf8_slovenian_ci NOT NULL,
  `odgovor_C` char(50) COLLATE utf8_slovenian_ci NOT NULL,
  `odgovor_D` char(50) COLLATE utf8_slovenian_ci NOT NULL,
  `pravilen` char(1) COLLATE utf8_slovenian_ci NOT NULL,
  `datum_vprasanja` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kvizi`
--
ALTER TABLE `kvizi`
  ADD PRIMARY KEY (`id_kviza`),
  ADD KEY `kvizi_ibfk_1` (`id_uporabnika`);

--
-- Indexes for table `odgovori`
--
ALTER TABLE `odgovori`
  ADD PRIMARY KEY (`id_odgovora`),
  ADD KEY `odgovori_ibfk_1` (`id_uporabnika`),
  ADD KEY `odgovori_ibfk_2` (`id_vprasanja`);

--
-- Indexes for table `rezultati`
--
ALTER TABLE `rezultati`
  ADD PRIMARY KEY (`id_rezultata`),
  ADD KEY `rezultati_ibfk_1` (`id_uporabnika`),
  ADD KEY `rezultati_ibfk_2` (`id_kviza`);

--
-- Indexes for table `uporabniki`
--
ALTER TABLE `uporabniki`
  ADD PRIMARY KEY (`id_uporabnika`);

--
-- Indexes for table `vprasanja`
--
ALTER TABLE `vprasanja`
  ADD PRIMARY KEY (`id_vprasanja`),
  ADD KEY `vprasanja_ibfk_1` (`id_kviza`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kvizi`
--
ALTER TABLE `kvizi`
  MODIFY `id_kviza` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `odgovori`
--
ALTER TABLE `odgovori`
  MODIFY `id_odgovora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `rezultati`
--
ALTER TABLE `rezultati`
  MODIFY `id_rezultata` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

--
-- AUTO_INCREMENT for table `uporabniki`
--
ALTER TABLE `uporabniki`
  MODIFY `id_uporabnika` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `vprasanja`
--
ALTER TABLE `vprasanja`
  MODIFY `id_vprasanja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kvizi`
--
ALTER TABLE `kvizi`
  ADD CONSTRAINT `kvizi_ibfk_1` FOREIGN KEY (`id_uporabnika`) REFERENCES `uporabniki` (`id_uporabnika`) ON DELETE CASCADE;

--
-- Constraints for table `odgovori`
--
ALTER TABLE `odgovori`
  ADD CONSTRAINT `odgovori_ibfk_1` FOREIGN KEY (`id_uporabnika`) REFERENCES `uporabniki` (`id_uporabnika`) ON DELETE CASCADE,
  ADD CONSTRAINT `odgovori_ibfk_2` FOREIGN KEY (`id_vprasanja`) REFERENCES `vprasanja` (`id_vprasanja`) ON DELETE CASCADE;

--
-- Constraints for table `rezultati`
--
ALTER TABLE `rezultati`
  ADD CONSTRAINT `rezultati_ibfk_1` FOREIGN KEY (`id_uporabnika`) REFERENCES `uporabniki` (`id_uporabnika`) ON DELETE CASCADE,
  ADD CONSTRAINT `rezultati_ibfk_2` FOREIGN KEY (`id_kviza`) REFERENCES `kvizi` (`id_kviza`) ON DELETE CASCADE;

--
-- Constraints for table `vprasanja`
--
ALTER TABLE `vprasanja`
  ADD CONSTRAINT `vprasanja_ibfk_1` FOREIGN KEY (`id_kviza`) REFERENCES `kvizi` (`id_kviza`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
