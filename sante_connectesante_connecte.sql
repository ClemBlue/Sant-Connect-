-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2020 at 02:06 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sante_connecte`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `role` varchar(15) NOT NULL DEFAULT 'user',
  `password` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `datetoken` datetime DEFAULT current_timestamp(),
  `status` varchar(15) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `email`, `created_at`, `role`, `password`, `token`, `datetoken`, `status`) VALUES
(4, 'Blin', 'Clément', 'clement.blin76@gmail.com', '2020-11-12 09:40:29', 'admin', '$2y$10$T.hOKw0fQfpLTVEcIvwKFuUslA8dS4HO5cN6GD2cxSjIoFoXRPrk6', '2jEWHOKvdxW8JDicznArxm5HY86wTgkqH4VTT4Ipa06QOHPy9RYlkOGDmQBx0ah40Ez95ZNNVfhxor1FShXJ8vnA0759QhmYTKrdkQ0lEA8daIMfWXyXEYntgWemHlJGd4TwC3Q6VoueMVsAbAYlGi54nXYz12HBB4Lt5LtGiQGaZAa7I9XCuJvfMMNx2H9xhuTYHKQQ3IKezNUFECiZhcKkAbQnOIS6jOPYRPLdaAQFFf52mkN6b7YUAQuyG8C', '2020-11-12 09:40:29', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_vaccin`
--

CREATE TABLE `user_vaccin` (
  `id` int(11) NOT NULL,
  `id_vaccin` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_vaccin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vaccins`
--

CREATE TABLE `vaccins` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `delay` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vaccins`
--

INSERT INTO `vaccins` (`id`, `name`, `description`, `created_at`, `delay`, `updated_at`, `status`) VALUES
(1, 'DTP', 'Le vaccin diphtérique, tétanique et poliomyélitique est un vaccin combiné trivalent dirigé contre la diphtérie, le tétanos et la poliomyélite. ', '2020-11-12 12:07:17', 315360000, NULL, 'actif'),
(2, 'Coqueluche', 'Le vaccin contre la coqueluche est un vaccin destiné à prévenir la coqueluche.', '2020-11-12 12:07:57', 315360000, NULL, 'actif');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `e_mail` (`email`);

--
-- Indexes for table `user_vaccin`
--
ALTER TABLE `user_vaccin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_vaccin_vaccins_FK` (`id_vaccin`),
  ADD KEY `user_vaccin_users0_FK` (`id_user`);

--
-- Indexes for table `vaccins`
--
ALTER TABLE `vaccins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_vaccin`
--
ALTER TABLE `user_vaccin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vaccins`
--
ALTER TABLE `vaccins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_vaccin`
--
ALTER TABLE `user_vaccin`
  ADD CONSTRAINT `user_vaccin_users0_FK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_vaccin_vaccins_FK` FOREIGN KEY (`id_vaccin`) REFERENCES `vaccins` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
