-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 20 nov. 2020 à 09:56
-- Version du serveur :  10.4.14-MariaDB
-- Version de PHP : 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sante_connecte`
--

-- --------------------------------------------------------

--
-- Structure de la table `users`
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
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `email`, `created_at`, `role`, `password`, `token`, `datetoken`, `status`) VALUES
(1, 'Blin', 'Clément', 'clement.blin76@gmail.com', '2020-11-12 09:40:29', 'admin', '$2y$10$T.hOKw0fQfpLTVEcIvwKFuUslA8dS4HO5cN6GD2cxSjIoFoXRPrk6', '2jEWHOKvdxW8JDicznArxm5HY86wTgkqH4VTT4Ipa06QOHPy9RYlkOGDmQBx0ah40Ez95ZNNVfhxor1FShXJ8vnA0759QhmYTKrdkQ0lEA8daIMfWXyXEYntgWemHlJGd4TwC3Q6VoueMVsAbAYlGi54nXYz12HBB4Lt5LtGiQGaZAa7I9XCuJvfMMNx2H9xhuTYHKQQ3IKezNUFECiZhcKkAbQnOIS6jOPYRPLdaAQFFf52mkN6b7YUAQuyG8C', '2020-11-12 09:40:29', 'actif'),
(2, 'Morel', 'Julien', 'julien.morel@gmail.com', '2020-11-12 14:34:01', 'user', '$2y$10$6F6zIS5FRO14CoRdxRSPNe245EVagNBxm.pXU9n7biZZXqjO4WK06', 'SzbV2esbDvM6e9CCgsRfxqCdLc8MLZHMoel1DUoAknyHmEmwCAH0M7Rb7qY54mg9Bi28wfJLmNecXjnuPQXZQsjtYGf5AuwuMDOYimi1gF9CGpMJZ9H3BmeOZMOq9diGnVXz7VU8nchznMmu3gUiwtf83ANoHl41i04GRze3UaZfhSDmoGui560TFMbl2inAVFNG6QAcYQSbAlj5vL6zuugOBhQzHQthvc1RYC2krPw0yTazeVn06yG5CGbQA5L', '2020-11-12 14:34:01', 'actif'),
(9, 'Bationo', 'Cyril', 'cyril.bationo@gmail.com', '2020-11-12 14:49:01', 'user', '$2y$10$MxfQjPn9RrtlW.n7IkWMUeG79fL.HuuV5WZ0RbWkD3EQ9ezvUwpia', 'oeKZ9MNijWx8t5iRmUdY2z8X8j7YSDcB2CWLXwF3IxTIHhejYQ0VYd2dcMKwANuzYyk8Ap02702H5Uzi5ZEYDpXKv17V90KOyBJxefzkca2VN0tLKSW2a3yLOQXOzoybfozLP4j94cz97SX9kPDHUAn5YODbxnisobA2hLOeKPtzFjigp76itRTzBbSXw2Kn4MLrIfpVYVF9eZWRmMGSMoQ3P5eqezCxcEOoY370WHq8XaJCbS3ul5UUZDqhmpN', '2020-11-12 14:49:01', 'desactive'),
(10, 'admin', 'admin', 'admin.admin@admin.com', '2020-11-20 09:29:05', 'admin', '$2y$10$P3KhmFDYtcqXk3VsUBw5SeIh789gyCWLpQeUG9mOwZbzw6VJlEuIy', 'Dfz7LfUdPPckcvHvW4hVdIfHmPQoOjYPAanBJCih7Z9fyvZ9HXps9nhF1JIiTUuA7izCOGAk269k2AMmTUXCfbGuxUOf7Rj6ZhxchvdhwFIxmPDMQHA4f1coAYtPP5cQZ0se161CuEAUkjwfCJQ0jZQp7klpQLjSjP8ffWZdalEdztlVNmpE2jyoUUaly74kzWKM0r5OSI1L4A5SaPDJ1ZNQQWxzTvifBpQhFxi9BkIUsuwpNMzEbNDQaF9RG5z', '2020-11-20 09:29:05', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `user_vaccin`
--

CREATE TABLE `user_vaccin` (
  `id` int(11) NOT NULL,
  `id_vaccin` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_vaccin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `user_vaccin`
--

INSERT INTO `user_vaccin` (`id`, `id_vaccin`, `id_user`, `date_vaccin`) VALUES
(16, 1, 9, '2020-11-25'),
(17, 2, 9, NULL),
(38, 1, 1, '2020-11-04'),
(39, 2, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `vaccins`
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
-- Déchargement des données de la table `vaccins`
--

INSERT INTO `vaccins` (`id`, `name`, `description`, `created_at`, `delay`, `updated_at`, `status`) VALUES
(1, 'DTP', 'Le vaccin diphtérique, tétanique et poliomyélitique est un vaccin combiné trivalent dirigé contre la diphtérie, le tétanos et la poliomyélite. ', '2020-11-12 12:07:17', 315360000, NULL, 'actif'),
(2, 'Coqueluche', 'Le vaccin contre la coqueluche est un vaccin destiné à prévenir la coqueluche.', '2020-11-12 12:07:57', 315360000, NULL, 'actif'),
(3, 'Hépatite B', 'L’hépatite B est une infection du foie causée par le virus de l’hépatite B (VHB). Ce virus se transmet par le sang et par les autres fluides corporels, essentiellement les sécrétions vaginales et le sperme.', '2020-11-20 09:33:20', 2147483647, '2020-11-20 09:33:20', 'actif'),
(4, 'la rougeole', 'La vaccination contre la rougeole, les oreillons et la rubéole (ROR) est recommandée aux enfants à 9 et 12 mois.', '2020-11-20 09:35:48', 10000, NULL, 'actif'),
(5, 'Les oreillons', 'Les oreillons : une maladie contagieuse causée par un virus.', '2020-11-20 09:37:21', 200000, NULL, 'actif'),
(6, 'la rubéole', 'La rubéole (ou 3e maladie) est une maladie virale épidémique, d\'incubation voisine de 13 à 20 jours.', '2020-11-20 09:38:03', 20000, NULL, 'actif');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `e_mail` (`email`);

--
-- Index pour la table `user_vaccin`
--
ALTER TABLE `user_vaccin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_vaccin_vaccins_FK` (`id_vaccin`),
  ADD KEY `user_vaccin_users0_FK` (`id_user`);

--
-- Index pour la table `vaccins`
--
ALTER TABLE `vaccins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `user_vaccin`
--
ALTER TABLE `user_vaccin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `vaccins`
--
ALTER TABLE `vaccins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `user_vaccin`
--
ALTER TABLE `user_vaccin`
  ADD CONSTRAINT `user_vaccin_users0_FK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_vaccin_vaccins_FK` FOREIGN KEY (`id_vaccin`) REFERENCES `vaccins` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
