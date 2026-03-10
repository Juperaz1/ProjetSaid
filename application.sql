-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mar. 10 mars 2026 à 19:25
-- Version du serveur : 10.11.14-MariaDB-0+deb12u2
-- Version de PHP : 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `applicationSaid`
--

-- --------------------------------------------------------

--
-- Structure de la table `AFFECTATIONS_TACHES`
--

CREATE TABLE `AFFECTATIONS_TACHES` (
  `IdAffectation` int(11) NOT NULL,
  `IdTache` int(11) NOT NULL,
  `IdEmploye` int(11) NOT NULL,
  `Role` varchar(100) DEFAULT NULL,
  `DateAffectation` date NOT NULL,
  `DateFinAffectation` date DEFAULT NULL,
  `Statut` enum('active','terminée','annulée') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `AFFECTATIONS_TACHES`
--

INSERT INTO `AFFECTATIONS_TACHES` (`IdAffectation`, `IdTache`, `IdEmploye`, `Role`, `DateAffectation`, `DateFinAffectation`, `Statut`) VALUES
(1, 1, 1, 'Chef de mission', '2025-01-10', NULL, 'active'),
(2, 1, 2, 'Auditeur junior', '2025-01-10', NULL, 'active'),
(3, 2, 3, 'Analyste', '2025-02-01', NULL, 'active'),
(4, 2, 1, 'Superviseur', '2025-02-01', NULL, 'active'),
(5, 3, 6, 'Rédacteur', '2025-02-20', NULL, 'active'),
(6, 4, 3, 'Expert fiscal', '2025-02-01', NULL, 'active'),
(7, 8, 1, 'Consultant', '2025-02-15', '2025-02-28', 'terminée'),
(8, 12, 1, 'Intervenant', '2026-03-10', NULL, 'active'),
(9, 12, 2, 'Chef d\'équipe', '2026-03-10', NULL, 'active'),
(10, 14, 1, 'Intervenant', '2026-03-10', NULL, 'active'),
(11, 15, 1, 'Intervenant', '2026-03-10', NULL, 'active'),
(12, 16, 5, 'junior', '2026-03-10', NULL, 'active');

-- --------------------------------------------------------

--
-- Structure de la table `CLIENTS`
--

CREATE TABLE `CLIENTS` (
  `IdClient` int(11) NOT NULL,
  `NomClient` varchar(150) NOT NULL,
  `SiretClient` varchar(14) DEFAULT NULL,
  `EmailClient` varchar(100) DEFAULT NULL,
  `TelephoneClient` varchar(20) DEFAULT NULL,
  `AdresseClient` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `CLIENTS`
--

INSERT INTO `CLIENTS` (`IdClient`, `NomClient`, `SiretClient`, `EmailClient`, `TelephoneClient`, `AdresseClient`) VALUES
(1, 'Entreprise ABC', '12345678901234', 'contact@abc.fr', '0123456789', '10 rue des Entreprises, 75001 Paris'),
(2, 'Société XYZ', '98765432109876', 'contact@xyz.fr', '0987654321', '25 avenue des Affaires, 69001 Lyon'),
(3, 'Startup 123', '45678901234567', 'contact@startup123.fr', '0678901234', '8 rue de l\'Innovation, 13001 Marseille');

-- --------------------------------------------------------

--
-- Structure de la table `COMPETENCES`
--

CREATE TABLE `COMPETENCES` (
  `IdCompetence` int(11) NOT NULL,
  `LibelleCompetence` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `COMPETENCES`
--

INSERT INTO `COMPETENCES` (`IdCompetence`, `LibelleCompetence`) VALUES
(6, 'Analyse financière'),
(3, 'Audit financier'),
(1, 'Comptabilité générale'),
(5, 'Conseil en stratégie'),
(2, 'Fiscalité'),
(4, 'Gestion de paie'),
(7, 'Juridique'),
(8, 'Reporting');

-- --------------------------------------------------------

--
-- Structure de la table `EMPLOYES`
--

CREATE TABLE `EMPLOYES` (
  `IdEmploye` int(11) NOT NULL,
  `NomEmploye` varchar(50) NOT NULL,
  `PrenomEmploye` varchar(50) NOT NULL,
  `EmailEmploye` varchar(100) NOT NULL,
  `IdSite` int(11) DEFAULT NULL,
  `Statut` enum('actif','inactif','congé') DEFAULT NULL,
  `EstResponsable` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `EMPLOYES`
--

INSERT INTO `EMPLOYES` (`IdEmploye`, `NomEmploye`, `PrenomEmploye`, `EmailEmploye`, `IdSite`, `Statut`, `EstResponsable`) VALUES
(1, 'Dupont', 'Jean', 'jean.dupont@email.com', 1, 'actif', 1),
(2, 'Martin', 'Sophie', 'sophie.martin@email.com', 1, 'actif', 0),
(3, 'Bernard', 'Pierre', 'pierre.bernard@email.com', 2, 'actif', 1),
(4, 'Petit', 'Marie', 'marie.petit@email.com', 2, 'actif', 0),
(5, 'Durand', 'Thomas', 'thomas.durand@email.com', 3, 'actif', 0),
(6, 'Leroy', 'Julie', 'julie.leroy@email.com', 3, 'actif', 1),
(7, 'Test', 'User', 'test@mail.com', NULL, 'actif', 0);

-- --------------------------------------------------------

--
-- Structure de la table `EMPLOYES_COMPETENCES`
--

CREATE TABLE `EMPLOYES_COMPETENCES` (
  `IdEmploye` int(11) NOT NULL,
  `IdCompetence` int(11) NOT NULL,
  `Niveau` enum('débutant','intermédiaire','avancé','expert') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `EMPLOYES_COMPETENCES`
--

INSERT INTO `EMPLOYES_COMPETENCES` (`IdEmploye`, `IdCompetence`, `Niveau`) VALUES
(1, 1, 'expert'),
(1, 2, 'avancé'),
(1, 3, 'avancé'),
(2, 3, 'avancé'),
(2, 4, 'expert'),
(3, 2, 'expert'),
(3, 5, 'avancé'),
(4, 3, 'intermédiaire'),
(4, 6, 'avancé'),
(5, 7, 'expert'),
(5, 8, 'intermédiaire'),
(6, 1, 'expert'),
(6, 6, 'expert');

-- --------------------------------------------------------

--
-- Structure de la table `FEUILLES_TEMPS`
--

CREATE TABLE `FEUILLES_TEMPS` (
  `IdFeuilleTemps` int(11) NOT NULL,
  `IdAffectation` int(11) NOT NULL,
  `DateTravail` date NOT NULL,
  `HeuresEffectuees` decimal(5,2) NOT NULL,
  `Description` longtext DEFAULT NULL,
  `Statut` enum('brouillon','validé','rejeté') DEFAULT NULL,
  `DateSoumission` datetime DEFAULT NULL,
  `DateValidation` datetime DEFAULT NULL,
  `CommentaireRejet` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `FEUILLES_TEMPS`
--

INSERT INTO `FEUILLES_TEMPS` (`IdFeuilleTemps`, `IdAffectation`, `DateTravail`, `HeuresEffectuees`, `Description`, `Statut`, `DateSoumission`, `DateValidation`, `CommentaireRejet`) VALUES
(1, 1, '2025-01-20', 8.00, 'Collecte des documents - jour 1', 'validé', '2025-01-21 10:00:00', '2025-01-22 14:30:00', NULL),
(2, 1, '2025-01-21', 7.50, 'Collecte des documents - jour 2', 'validé', '2025-01-22 09:15:00', '2025-01-23 11:20:00', NULL),
(3, 2, '2025-01-20', 8.00, 'Assistance collecte documents', 'validé', '2025-01-21 16:30:00', '2025-01-22 14:30:00', NULL),
(4, 3, '2025-02-05', 8.00, 'Analyse comptes fournisseurs', 'validé', '2025-02-06 18:00:00', '2025-02-07 09:45:00', NULL),
(5, 3, '2025-02-06', 8.00, 'Analyse comptes clients', 'validé', '2025-02-07 17:30:00', '2025-02-08 10:15:00', NULL),
(6, 4, '2025-02-05', 4.00, 'Supervision analyse', 'brouillon', '2025-02-06 12:00:00', NULL, NULL),
(7, 1, '2026-03-10', 6.50, '', 'brouillon', '2026-03-10 17:25:43', NULL, NULL),
(8, 11, '2026-03-10', 2.00, 'réalisation du test2', 'validé', '2026-03-10 18:38:02', '2026-03-10 18:38:02', NULL),
(9, 10, '2026-03-10', 2.00, 'terminé test1', 'validé', '2026-03-10 18:39:48', '2026-03-10 18:39:48', NULL),
(10, 10, '2026-03-10', 2.00, 'fini', 'validé', '2026-03-10 18:42:09', '2026-03-10 18:42:09', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `MISSIONS`
--

CREATE TABLE `MISSIONS` (
  `IdMission` int(11) NOT NULL,
  `NoMission` varchar(20) NOT NULL,
  `IdClient` int(11) NOT NULL,
  `IdTypeMission` int(11) NOT NULL,
  `IdResponsable` int(11) DEFAULT NULL,
  `DateDebut` date NOT NULL,
  `DateFinPrevue` date NOT NULL,
  `Description` longtext DEFAULT NULL,
  `BudgetEuro` decimal(10,2) DEFAULT NULL,
  `BudgetHeures` decimal(8,2) DEFAULT NULL,
  `Statut` enum('prévue','en cours','en pause','terminée','annulée') DEFAULT NULL,
  `AvancementPourcentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `DateCreation` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `MISSIONS`
--

INSERT INTO `MISSIONS` (`IdMission`, `NoMission`, `IdClient`, `IdTypeMission`, `IdResponsable`, `DateDebut`, `DateFinPrevue`, `Description`, `BudgetEuro`, `BudgetHeures`, `Statut`, `AvancementPourcentage`, `DateCreation`) VALUES
(1, 'MISSION-2025-001', 1, 1, 1, '2025-01-15', '2025-03-30', 'Audit annuel des comptes 2024', 15000.00, 120.00, 'en cours', 0.00, '2026-02-18 10:15:10'),
(2, 'MISSION-2025-002', 2, 2, 3, '2025-02-01', '2025-04-15', 'Préparation déclaration fiscale 2024', 8000.00, 60.00, 'en cours', 0.00, '2026-02-18 10:15:10'),
(3, 'MISSION-2025-003', 3, 4, 6, '2025-03-01', '2025-05-30', 'Bilan comptable annuel', 12000.00, 100.00, 'prévue', 0.00, '2026-02-18 10:15:10'),
(4, 'MISSION-2025-004', 1, 3, 1, '2025-02-15', '2025-03-15', 'Conseil en optimisation fiscale', 5000.00, 40.00, 'terminée', 100.00, '2026-02-18 10:15:10'),
(5, 'MISSION-2026-001', 1, 1, 6, '2026-02-25', '2026-03-27', 'test tâche', 10.00, 10.00, 'prévue', 0.00, '2026-02-25 16:00:30'),
(6, 'MISSION-2026-002', 3, 3, 1, '2026-03-10', '2026-04-09', '', 500.00, 60.00, 'en cours', 66.67, '2026-03-10 17:06:51');

-- --------------------------------------------------------

--
-- Structure de la table `PLANNING_GANTT`
--

CREATE TABLE `PLANNING_GANTT` (
  `IdPlanning` int(11) NOT NULL,
  `IdMission` int(11) NOT NULL,
  `DateDebut` date NOT NULL,
  `DateFin` date NOT NULL,
  `CheminCritique` tinyint(4) NOT NULL DEFAULT 0,
  `MargeTotale` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `PLANNING_GANTT`
--

INSERT INTO `PLANNING_GANTT` (`IdPlanning`, `IdMission`, `DateDebut`, `DateFin`, `CheminCritique`, `MargeTotale`) VALUES
(1, 1, '2025-01-15', '2025-03-30', 1, 5.00),
(2, 2, '2025-02-01', '2025-04-15', 0, 10.00),
(3, 3, '2025-03-01', '2025-05-30', 0, 15.00);

-- --------------------------------------------------------

--
-- Structure de la table `SITES`
--

CREATE TABLE `SITES` (
  `IdSite` int(11) NOT NULL,
  `VilleSite` varchar(100) NOT NULL,
  `AdresseSite` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `SITES`
--

INSERT INTO `SITES` (`IdSite`, `VilleSite`, `AdresseSite`) VALUES
(1, 'Paris', '15 rue de la Paix, 75002 Paris'),
(2, 'Lyon', '45 rue de la République, 69002 Lyon'),
(3, 'Marseille', '12 rue Paradis, 13001 Marseille');

-- --------------------------------------------------------

--
-- Structure de la table `TACHES`
--

CREATE TABLE `TACHES` (
  `IdTache` int(11) NOT NULL,
  `IdMission` int(11) NOT NULL,
  `LibelleTache` varchar(200) NOT NULL,
  `Description` longtext DEFAULT NULL,
  `DureeEstimee` decimal(8,2) DEFAULT NULL,
  `DateDebutPrevue` date DEFAULT NULL,
  `DateFinPrevue` date DEFAULT NULL,
  `Priorite` enum('basse','moyenne','haute','critique') DEFAULT NULL,
  `Statut` enum('à faire','en cours','terminée','bloquée') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `TACHES`
--

INSERT INTO `TACHES` (`IdTache`, `IdMission`, `LibelleTache`, `Description`, `DureeEstimee`, `DateDebutPrevue`, `DateFinPrevue`, `Priorite`, `Statut`) VALUES
(1, 1, 'Collecte des documents', 'Récupérer tous les justificatifs comptables', 20.00, '2025-01-15', '2025-01-31', 'haute', 'à faire'),
(2, 1, 'Analyse des comptes', 'Vérification des écritures comptables', 50.00, '2025-02-01', '2025-02-28', 'haute', 'en cours'),
(3, 1, 'Rédaction du rapport', 'Préparation du rapport d\'audit', 30.00, '2025-03-01', '2025-03-15', 'moyenne', 'à faire'),
(4, 2, 'Calcul des impôts', 'Préparation de la liasse fiscale', 40.00, '2025-02-01', '2025-03-01', 'haute', 'en cours'),
(5, 2, 'Vérification des déclarations', 'Contrôle des calculs fiscaux', 20.00, '2025-03-02', '2025-03-15', 'moyenne', 'à faire'),
(6, 3, 'Analyse bilan', 'Examen du bilan comptable', 50.00, '2025-03-01', '2025-04-15', 'haute', 'à faire'),
(7, 3, 'Rédaction rapport bilan', 'Préparation du rapport', 30.00, '2025-04-16', '2025-05-15', 'moyenne', 'à faire'),
(8, 4, 'Analyse fiscale', 'Étude de la situation fiscale', 20.00, '2025-02-15', '2025-02-28', 'haute', 'terminée'),
(9, 4, 'Recommandations', 'Propositions d\'optimisation', 20.00, '2025-03-01', '2025-03-10', 'haute', 'terminée'),
(12, 5, 'test', 'test de nouvelles fonctionnalités', 10.00, '2026-02-25', '2026-03-27', 'critique', 'à faire'),
(14, 6, 'test1', '', 2.00, '2026-03-10', '2026-04-09', 'moyenne', 'terminée'),
(15, 6, 'test2', '', 6.00, '2026-03-10', '2026-04-09', 'moyenne', 'terminée'),
(16, 6, 'test3', '', 50.00, '2026-03-10', '2026-04-09', 'moyenne', 'à faire');

-- --------------------------------------------------------

--
-- Structure de la table `TACHES_COMPETENCES_REQUISES`
--

CREATE TABLE `TACHES_COMPETENCES_REQUISES` (
  `IdTache` int(11) NOT NULL,
  `IdCompetence` int(11) NOT NULL,
  `NiveauRequis` enum('débutant','intermédiaire','avancé','expert') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `TACHES_COMPETENCES_REQUISES`
--

INSERT INTO `TACHES_COMPETENCES_REQUISES` (`IdTache`, `IdCompetence`, `NiveauRequis`) VALUES
(1, 1, 'intermédiaire'),
(1, 3, 'débutant'),
(2, 1, 'avancé'),
(2, 6, 'intermédiaire'),
(3, 1, 'expert'),
(3, 3, 'avancé'),
(4, 2, 'expert'),
(4, 7, 'intermédiaire'),
(5, 2, 'avancé'),
(5, 8, 'intermédiaire'),
(6, 1, 'expert'),
(6, 6, 'avancé'),
(7, 1, 'avancé'),
(7, 8, 'expert'),
(8, 2, 'avancé'),
(8, 5, 'intermédiaire'),
(9, 5, 'expert'),
(9, 7, 'intermédiaire'),
(12, 3, 'avancé'),
(14, 1, 'débutant'),
(15, 1, 'avancé'),
(16, 7, 'débutant');

-- --------------------------------------------------------

--
-- Structure de la table `TYPES_MISSIONS`
--

CREATE TABLE `TYPES_MISSIONS` (
  `IdTypeMission` int(11) NOT NULL,
  `LibelleTypeMission` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `TYPES_MISSIONS`
--

INSERT INTO `TYPES_MISSIONS` (`IdTypeMission`, `LibelleTypeMission`) VALUES
(1, 'Audit'),
(2, 'Déclaration fiscale'),
(3, 'Conseil'),
(4, 'Bilan'),
(5, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `UTILISATEURS`
--

CREATE TABLE `UTILISATEURS` (
  `IdUtilisateur` int(11) NOT NULL,
  `IdEmploye` int(11) DEFAULT NULL,
  `Login` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Roles`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `UTILISATEURS`
--

INSERT INTO `UTILISATEURS` (`IdUtilisateur`, `IdEmploye`, `Login`, `Password`, `Roles`) VALUES
(1, 1, 'jdupont', '$2y$13$F9OgQhCiyMh7MAWK8ypeBuBkqemvP/p03FE5cACuEejY3YrQapNxS', '[]'),
(2, 2, 'smartin', 'password_a_hasher', '[]'),
(3, 3, 'pbernard', 'password_a_hasher', '[]'),
(4, 7, 'test', '$2y$13$25RaWYFf3PLNi.JwbmWDp.DlzbGcM3yYnu5W1oJcgrDt37rN/EtrO', '[]'),
(5, 1, 'testtest', '$2y$13$IQHbHEGf7KAhmvhfxxhFwuduiNpKNhZTpOzcOdEsNdbRrAS114Jfa', '[]'),
(6, NULL, 'testuser1234', '$2y$13$ujvNMV.nthFAOtTh4AkdMehSqjB5tvR.W5Q4r.A5QA1/gxeCGwG9y', '[]');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `AFFECTATIONS_TACHES`
--
ALTER TABLE `AFFECTATIONS_TACHES`
  ADD PRIMARY KEY (`IdAffectation`),
  ADD KEY `IDX_401989216A4C3AE8` (`IdEmploye`),
  ADD KEY `IDX_401989214BFE5AF4` (`IdTache`);

--
-- Index pour la table `CLIENTS`
--
ALTER TABLE `CLIENTS`
  ADD PRIMARY KEY (`IdClient`),
  ADD UNIQUE KEY `UNIQ_3F8DD06E887A3407` (`SiretClient`);

--
-- Index pour la table `COMPETENCES`
--
ALTER TABLE `COMPETENCES`
  ADD PRIMARY KEY (`IdCompetence`),
  ADD UNIQUE KEY `UNIQ_5623E526A7DC11F8` (`LibelleCompetence`);

--
-- Index pour la table `EMPLOYES`
--
ALTER TABLE `EMPLOYES`
  ADD PRIMARY KEY (`IdEmploye`),
  ADD UNIQUE KEY `UNIQ_6F785CBCAB2105AE` (`EmailEmploye`),
  ADD KEY `IDX_6F785CBC1D12524C` (`IdSite`);

--
-- Index pour la table `EMPLOYES_COMPETENCES`
--
ALTER TABLE `EMPLOYES_COMPETENCES`
  ADD PRIMARY KEY (`IdEmploye`,`IdCompetence`),
  ADD KEY `IDX_6D9A8747E11AD6D` (`IdCompetence`);

--
-- Index pour la table `FEUILLES_TEMPS`
--
ALTER TABLE `FEUILLES_TEMPS`
  ADD PRIMARY KEY (`IdFeuilleTemps`),
  ADD KEY `IDX_A2C5FD7F7FED55E` (`IdAffectation`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `MISSIONS`
--
ALTER TABLE `MISSIONS`
  ADD PRIMARY KEY (`IdMission`),
  ADD UNIQUE KEY `UNIQ_F2C248323B246AF2` (`NoMission`),
  ADD KEY `IDX_F2C248325D23CE99` (`IdClient`),
  ADD KEY `IDX_F2C24832761DF396` (`IdTypeMission`),
  ADD KEY `IDX_F2C24832A171B98A` (`IdResponsable`);

--
-- Index pour la table `PLANNING_GANTT`
--
ALTER TABLE `PLANNING_GANTT`
  ADD PRIMARY KEY (`IdPlanning`),
  ADD KEY `IDX_4DF0E64F22F1B6D` (`IdMission`);

--
-- Index pour la table `SITES`
--
ALTER TABLE `SITES`
  ADD PRIMARY KEY (`IdSite`);

--
-- Index pour la table `TACHES`
--
ALTER TABLE `TACHES`
  ADD PRIMARY KEY (`IdTache`),
  ADD KEY `IDX_CB0A0A1E22F1B6D` (`IdMission`);

--
-- Index pour la table `TACHES_COMPETENCES_REQUISES`
--
ALTER TABLE `TACHES_COMPETENCES_REQUISES`
  ADD PRIMARY KEY (`IdTache`,`IdCompetence`),
  ADD KEY `IDX_72FB45BEE11AD6D` (`IdCompetence`);

--
-- Index pour la table `TYPES_MISSIONS`
--
ALTER TABLE `TYPES_MISSIONS`
  ADD PRIMARY KEY (`IdTypeMission`);

--
-- Index pour la table `UTILISATEURS`
--
ALTER TABLE `UTILISATEURS`
  ADD PRIMARY KEY (`IdUtilisateur`),
  ADD UNIQUE KEY `UNIQ_DC49784E6BC9E414` (`Login`),
  ADD KEY `IDX_DC49784E6A4C3AE8` (`IdEmploye`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `AFFECTATIONS_TACHES`
--
ALTER TABLE `AFFECTATIONS_TACHES`
  MODIFY `IdAffectation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `CLIENTS`
--
ALTER TABLE `CLIENTS`
  MODIFY `IdClient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `COMPETENCES`
--
ALTER TABLE `COMPETENCES`
  MODIFY `IdCompetence` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `EMPLOYES`
--
ALTER TABLE `EMPLOYES`
  MODIFY `IdEmploye` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `FEUILLES_TEMPS`
--
ALTER TABLE `FEUILLES_TEMPS`
  MODIFY `IdFeuilleTemps` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `MISSIONS`
--
ALTER TABLE `MISSIONS`
  MODIFY `IdMission` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `PLANNING_GANTT`
--
ALTER TABLE `PLANNING_GANTT`
  MODIFY `IdPlanning` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `SITES`
--
ALTER TABLE `SITES`
  MODIFY `IdSite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `TACHES`
--
ALTER TABLE `TACHES`
  MODIFY `IdTache` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `TYPES_MISSIONS`
--
ALTER TABLE `TYPES_MISSIONS`
  MODIFY `IdTypeMission` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `UTILISATEURS`
--
ALTER TABLE `UTILISATEURS`
  MODIFY `IdUtilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `AFFECTATIONS_TACHES`
--
ALTER TABLE `AFFECTATIONS_TACHES`
  ADD CONSTRAINT `FK_401989214BFE5AF4` FOREIGN KEY (`IdTache`) REFERENCES `TACHES` (`IdTache`),
  ADD CONSTRAINT `FK_401989216A4C3AE8` FOREIGN KEY (`IdEmploye`) REFERENCES `EMPLOYES` (`IdEmploye`);

--
-- Contraintes pour la table `EMPLOYES`
--
ALTER TABLE `EMPLOYES`
  ADD CONSTRAINT `EMPLOYES_ibfk_1` FOREIGN KEY (`IdSite`) REFERENCES `SITES` (`IdSite`);

--
-- Contraintes pour la table `EMPLOYES_COMPETENCES`
--
ALTER TABLE `EMPLOYES_COMPETENCES`
  ADD CONSTRAINT `FK_6D9A87476A4C3AE8` FOREIGN KEY (`IdEmploye`) REFERENCES `EMPLOYES` (`IdEmploye`),
  ADD CONSTRAINT `FK_6D9A8747E11AD6D` FOREIGN KEY (`IdCompetence`) REFERENCES `COMPETENCES` (`IdCompetence`);

--
-- Contraintes pour la table `FEUILLES_TEMPS`
--
ALTER TABLE `FEUILLES_TEMPS`
  ADD CONSTRAINT `FK_A2C5FD7F7FED55E` FOREIGN KEY (`IdAffectation`) REFERENCES `AFFECTATIONS_TACHES` (`IdAffectation`);

--
-- Contraintes pour la table `MISSIONS`
--
ALTER TABLE `MISSIONS`
  ADD CONSTRAINT `MISSIONS_ibfk_1` FOREIGN KEY (`IdClient`) REFERENCES `CLIENTS` (`IdClient`),
  ADD CONSTRAINT `MISSIONS_ibfk_2` FOREIGN KEY (`IdTypeMission`) REFERENCES `TYPES_MISSIONS` (`IdTypeMission`),
  ADD CONSTRAINT `MISSIONS_ibfk_3` FOREIGN KEY (`IdResponsable`) REFERENCES `EMPLOYES` (`IdEmploye`);

--
-- Contraintes pour la table `PLANNING_GANTT`
--
ALTER TABLE `PLANNING_GANTT`
  ADD CONSTRAINT `FK_4DF0E64F22F1B6D` FOREIGN KEY (`IdMission`) REFERENCES `MISSIONS` (`IdMission`);

--
-- Contraintes pour la table `TACHES`
--
ALTER TABLE `TACHES`
  ADD CONSTRAINT `FK_CB0A0A1E22F1B6D` FOREIGN KEY (`IdMission`) REFERENCES `MISSIONS` (`IdMission`);

--
-- Contraintes pour la table `TACHES_COMPETENCES_REQUISES`
--
ALTER TABLE `TACHES_COMPETENCES_REQUISES`
  ADD CONSTRAINT `FK_72FB45BE4BFE5AF4` FOREIGN KEY (`IdTache`) REFERENCES `TACHES` (`IdTache`),
  ADD CONSTRAINT `FK_72FB45BEE11AD6D` FOREIGN KEY (`IdCompetence`) REFERENCES `COMPETENCES` (`IdCompetence`);

--
-- Contraintes pour la table `UTILISATEURS`
--
ALTER TABLE `UTILISATEURS`
  ADD CONSTRAINT `UTILISATEURS_ibfk_1` FOREIGN KEY (`IdEmploye`) REFERENCES `EMPLOYES` (`IdEmploye`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
