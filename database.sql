-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 28 nov. 2025 à 22:45
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pawproject`
--
CREATE DATABASE IF NOT EXISTS `pawproject` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pawproject`;

-- --------------------------------------------------------

--
-- Structure de la table `attendance_records`
--

DROP TABLE IF EXISTS `attendance_records`;
CREATE TABLE `attendance_records` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(1, 2, 2, 1, '2025-11-27 23:20:07');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(2, 2, 3, 0, '2025-11-27 23:20:07');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(3, 2, 4, 0, '2025-11-27 23:20:08');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(4, 2, 5, 0, '2025-11-27 23:20:10');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(5, 2, 6, 0, '2025-11-27 23:20:11');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(10, 4, 2, 1, '2025-11-27 23:35:21');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(11, 4, 1, 1, '2025-11-27 23:35:22');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(12, 4, 3, 1, '2025-11-27 23:35:24');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(13, 4, 4, 1, '2025-11-27 23:38:24');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(14, 3, 5, 1, '2025-11-27 23:38:25');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(15, 1, 5, 1, '2025-11-27 23:38:26');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(16, 1, 4, 1, '2025-11-27 23:38:27');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(17, 1, 3, 1, '2025-11-27 23:38:27');
INSERT INTO `attendance_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(18, 6, 1, 1, '2025-11-28 21:20:05');

-- --------------------------------------------------------

--
-- Structure de la table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `courses`
--

INSERT INTO `courses` (`id`, `name`, `code`, `description`) VALUES(1, 'Computer Science', 'CS101', NULL);
INSERT INTO `courses` (`id`, `name`, `code`, `description`) VALUES(2, 'Mathematics', 'MATH101', NULL);
INSERT INTO `courses` (`id`, `name`, `code`, `description`) VALUES(3, 'Physics', 'PHY101', NULL);
INSERT INTO `courses` (`id`, `name`, `code`, `description`) VALUES(4, 'Chemistry', 'CHEM101', NULL);
INSERT INTO `courses` (`id`, `name`, `code`, `description`) VALUES(5, 'Biology', 'BIO101', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `participation_records`
--

DROP TABLE IF EXISTS `participation_records`;
CREATE TABLE `participation_records` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participation_records`
--

INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(1, 2, 1, 1, '2025-11-27 23:20:13');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(2, 4, 6, 1, '2025-11-27 23:38:30');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(3, 4, 5, 1, '2025-11-27 23:38:31');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(5, 2, 2, 1, '2025-11-28 21:26:02');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(6, 2, 4, 1, '2025-11-28 21:26:03');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(7, 1, 4, 1, '2025-11-28 21:26:05');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(8, 2, 6, 1, '2025-11-28 21:26:18');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(11, 4, 4, 1, '2025-11-28 21:26:24');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(14, 3, 6, 1, '2025-11-28 21:26:54');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(15, 3, 5, 1, '2025-11-28 21:26:55');
INSERT INTO `participation_records` (`id`, `student_id`, `session_id`, `status`, `recorded_at`) VALUES(16, 3, 4, 1, '2025-11-28 21:26:56');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `session_date` date NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `name`, `session_date`, `description`) VALUES(1, 'Session 1', '2025-11-27', NULL);
INSERT INTO `sessions` (`id`, `name`, `session_date`, `description`) VALUES(2, 'Session 2', '2025-11-28', NULL);
INSERT INTO `sessions` (`id`, `name`, `session_date`, `description`) VALUES(3, 'Session 3', '2025-11-29', NULL);
INSERT INTO `sessions` (`id`, `name`, `session_date`, `description`) VALUES(4, 'Session 4', '2025-11-30', NULL);
INSERT INTO `sessions` (`id`, `name`, `session_date`, `description`) VALUES(5, 'Session 5', '2025-12-01', NULL);
INSERT INTO `sessions` (`id`, `name`, `session_date`, `description`) VALUES(6, 'Session 6', '2025-12-02', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`id`, `matricule`, `firstname`, `lastname`, `course`, `email`, `phone`, `created_at`, `status`) VALUES(1, 'ST001', 'John', 'Doe', 'Computer Science', 'john.doe@email.com', NULL, '2025-11-27 21:54:04', 'active');
INSERT INTO `students` (`id`, `matricule`, `firstname`, `lastname`, `course`, `email`, `phone`, `created_at`, `status`) VALUES(2, 'ST002', 'Jane', 'Smith', 'Mathematics', 'jane.smith@email.com', NULL, '2025-11-27 21:54:04', 'active');
INSERT INTO `students` (`id`, `matricule`, `firstname`, `lastname`, `course`, `email`, `phone`, `created_at`, `status`) VALUES(3, 'ST003', 'Robert', 'Johnson', 'Physics', 'robert.johnson@email.com', NULL, '2025-11-27 21:54:04', 'active');
INSERT INTO `students` (`id`, `matricule`, `firstname`, `lastname`, `course`, `email`, `phone`, `created_at`, `status`) VALUES(4, '202131096981', 'zineb', 'Merzougui', 'Advanced Web Programming', NULL, NULL, '2025-11-27 23:35:14', 'active');
INSERT INTO `students` (`id`, `matricule`, `firstname`, `lastname`, `course`, `email`, `phone`, `created_at`, `status`) VALUES(6, '202374934943', 'manel', 'manel', 'Advanced Web Programming', NULL, NULL, '2025-11-28 21:19:47', 'active');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`session_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Index pour la table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Index pour la table `participation_records`
--
ALTER TABLE `participation_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_participation` (`student_id`,`session_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `participation_records`
--
ALTER TABLE `participation_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_records_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `participation_records`
--
ALTER TABLE `participation_records`
  ADD CONSTRAINT `participation_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `participation_records_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
