-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 03, 2021 at 11:00 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bncc_hackathon`
--

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `nama` tinytext NOT NULL,
  `ketua` text NOT NULL,
  `anggota1` text NOT NULL,
  `anggota2` text NOT NULL,
  `pembayaran` tinytext DEFAULT NULL,
  `verifikasi` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `nama`, `ketua`, `anggota1`, `anggota2`, `pembayaran`, `verifikasi`) VALUES
(1, 'pItung', 'animationfar@gmail.com', 'farhan.sabran@binus.ac.id', 'test1@email.com', '1608229552_14752_pItung.png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` smallint(6) NOT NULL,
  `nama` text NOT NULL,
  `foto` tinytext NOT NULL,
  `email` text NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `line` tinytext NOT NULL,
  `telepon` tinytext NOT NULL,
  `cv` tinytext DEFAULT NULL,
  `passhash` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `foto`, `email`, `tanggal_lahir`, `line`, `telepon`, `cv`, `passhash`) VALUES
(3, 'Farhan Muhammad Sabran', '1607794599_47429_FarhanMuhammadSabran.png', 'animationfar@gmail.com', '2020-12-11', '@farhanms134', '+6281234567890', '1607797730_68080_FarhanMuhammadSabran.pdf', '$2y$10$vmw8i2p6v6cDzB9XhBvqvu5EGxZUFNuyDX3In6QxUSTFfbDe5RVF2'),
(4, 'Felis Cato', '1608197497_65179_FelisCato.png', 'farhan.sabran@binus.ac.id', '2020-12-17', 'farhanms134', '+6281298433723', NULL, '$2y$10$zO1dv7ZEffUI5brWTMqTYOWo.5VucJbrxqzsvpa4xDx50dFmgHY0u'),
(5, 'Felis MS', '1608197579_58824_FelisMS.png', 'test1@email.com', '2020-12-30', '@farhanms123', '+6281234567890', NULL, '$2y$10$braDwB1V.5hKNtbQPFO94.0sKdAd0K.vSLHecg.mbbCSXnS5UivG6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
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
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
