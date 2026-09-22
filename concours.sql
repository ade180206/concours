-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 20 mai 2026 à 14:00
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `concours`
--

-- --------------------------------------------------------

--
-- Structure de la table `candidats`
--

CREATE TABLE `candidats` (
  `id` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenoms` varchar(150) NOT NULL,
  `date_naissance` date NOT NULL,
  `filiere` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `candidats`
--

INSERT INTO `candidats` (`id`, `matricule`, `nom`, `prenoms`, `date_naissance`, `filiere`, `created_at`) VALUES
(1, 'C001', 'OGUNGBEMI', 'RODOLPHE', '2006-02-18', 'Informatique', '2026-05-20 09:07:55'),
(2, 'C002', 'ATANDA', 'DORCAS', '2003-07-22', 'Gestion', '2026-05-20 09:07:55'),
(3, 'C003', 'KRAMO', 'MOAYE', '2001-11-08', 'Commerce', '2026-05-20 09:07:55'),
(4, 'C004', 'MOULAUD', 'MARC', '2026-05-20', 'COMMERCE', '2026-05-20 11:14:11'),
(5, 'C005', 'AKAKPO', 'ANGE', '2026-05-20', 'INFORMATIQUE', '2026-05-20 11:37:44');

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `info_ecrit` decimal(4,2) NOT NULL DEFAULT 0.00,
  `info_oral` decimal(4,2) NOT NULL DEFAULT 0.00,
  `anglais_ecrit` decimal(4,2) NOT NULL DEFAULT 0.00,
  `anglais_oral` decimal(4,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `matricule`, `info_ecrit`, `info_oral`, `anglais_ecrit`, `anglais_oral`) VALUES
(1, 'C001', 12.00, 14.00, 15.00, 17.00),
(2, 'C002', 12.00, 13.00, 14.00, 15.00),
(3, 'C003', 15.00, 16.00, 14.00, 12.00),
(5, 'C004', 12.00, 13.00, 15.00, 14.00),
(6, 'C005', 9.00, 8.00, 10.75, 11.50);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `candidats`
--
ALTER TABLE `candidats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `candidats`
--
ALTER TABLE `candidats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `candidats` (`matricule`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
