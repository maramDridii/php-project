-- phpMyAdmin SQL Dump
-- version 5.2.1-4.fc40
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 03 oct. 2024 à 11:12
-- Version du serveur : 10.11.8-MariaDB
-- Version de PHP : 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tournament_manager`
--

-- --------------------------------------------------------

--
-- Structure de la table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `games`
--

INSERT INTO `games` (`id`, `name`, `description`) VALUES
(1, 'Valorant', NULL),
(2, 'LOL', NULL),
(3, 'Fortnite', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team1_id` int(11) DEFAULT NULL,
  `team2_id` int(11) DEFAULT NULL,
  `score1` int(11) DEFAULT 0,
  `score2` int(11) DEFAULT 0,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `tournament_status` varchar(20) NOT NULL DEFAULT 'On going',
  `name` varchar(100) NOT NULL,
  `members` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`members`)),
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `logo` varchar(255) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`))
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `teams`
--

INSERT INTO `teams` (`id`, `user_id`, `tournament_id`, `tournament_status`, `name`, `members`, `status`, `logo`, `images`) VALUES
(27, 1, 27, 'In Progress', 'Team Alpha', '[\"Alice\", \"Bob\", \"Charlie\"]', 'active', 'team_alpha_logo.png', '[\"image1_alpha.jpg\",\"image3_alpha.jpg\",\"image2_alpha.jpg\"]'),
(28, 1, 26, '', 'Team Bravo', '[\"David\", \"Eve\"]', 'active', 'team_bravo_logo.png', '[\"image1_bravo.jpg\"]'),
(37, 2, NULL, '', 'Team Charlie', '[\"Frank\",\"Grace\",\"Heidi\",\"Ivan\"]', 'active', 'logoV.png', '[\"viet1.jpg\",\"viet2.jpg\"]');

-- --------------------------------------------------------

--
-- Structure de la table `tournaments`
--

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  `status` enum('upcoming','ongoing','completed') DEFAULT 'upcoming',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `tournaments`
--

INSERT INTO `tournaments` (`id`, `game_id`, `name`, `date`, `status`, `image`) VALUES
(19, 1, 'Valorant Championship', '2024-11-10 15:00:00', 'upcoming', 't1.png'),
(20, 1, 'Valorant Battle Royale', '2024-12-05 18:00:00', 'upcoming', 't2.png'),
(21, 2, 'LOL Summer Finals', '2024-10-20 17:00:00', 'upcoming', 't4.png'),
(22, 2, 'LOL Spring Tournament', '2024-11-15 16:00:00', 'upcoming', 't4.png'),
(23, 3, 'Fortnite World Cup', '2024-11-30 14:00:00', 'upcoming', 't5.png'),
(24, 3, 'Fortnite Charity Tournament', '2024-12-12 19:00:00', 'upcoming', 't5.png'),
(25, 1, 'Valorant League', '2024-09-25 16:00:00', 'completed', 't3.png'),
(26, 2, 'LOL Grand Tournament', '2024-09-15 20:00:00', 'completed', '66fe5876235e6.png'),
(27, 3, 'Fortnite Pro League', '2024-10-15 15:00:00', 'upcoming', 't5.png'),
(32, 1, 'Champions Tour 2024 Pacific: Ascension', '2025-05-18 14:00:00', 'upcoming', '66fbfb11743b0.png');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'maram', 'maramdr18@gmail.com', '$2y$10$vbm9BFDLnF5IwUkVNmWu4.JwUTeffbwkNCtSRMnTrDhBbgopkeQ.K', 'admin'),
(2, 'maramdr18', 'maram@gmail.com', '$2y$10$obIe4ZLYMDcmJFaVIPlfKe5xMuW4TgySfgQqZ7b0BE8udOLuAXeDW', 'user');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `team1_id` (`team1_id`),
  ADD KEY `team2_id` (`team2_id`);

--
-- Index pour la table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Index pour la table `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`team1_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`team2_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tournaments`
--
ALTER TABLE `tournaments`
  ADD CONSTRAINT `tournaments_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
