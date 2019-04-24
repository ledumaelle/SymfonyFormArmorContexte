-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mer. 24 avr. 2019 à 08:41
-- Version du serveur :  5.7.21
-- Version de PHP :  7.0.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `formarmor`
--

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `SessionsAutorisees`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SessionsAutorisees` (IN `idClient` INT)  BEGIN
        DECLARE idSession, idFormation,maxSessions,nbPlaces,nbInscrits integer;
		DECLARE formation,niveau,description,date varchar(50);
		DECLARE ferme tinyint; 
         DECLARE fin TINYINT DEFAULT 0;  
        
        DECLARE curseur1 CURSOR FOR SELECT 
        session_formation.id, formation.id, FORMATION.libelle,FORMATION.niveau,FORMATION.description,SESSION_FORMATION.date_debut,SESSION_FORMATION.nb_places ,SESSION_FORMATION.nb_inscrits, SESSION_FORMATION.close
        FROM session_formation,plan_formation,formation
        WHERE session_formation.formation_id = plan_formation.formation_id
        AND formation.id = session_formation.formation_id
		AND close = 0
        AND (nb_places>  nb_inscrits)
        AND plan_formation.client_id = idClient
        AND formation.id IN
        (SELECT Formation_id
        FROM plan_formation
        WHERE Client_id = idClient) 
        AND date_debut BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE,INTERVAL 2 MONTH)
        AND session_formation.id NOT IN
        (SELECT session_formation_id 
         FROM inscription
         WHERE client_id = idClient)
         AND plan_formation.effectue = 0;
        
      	DECLARE CONTINUE HANDLER FOR NOT FOUND SET fin = 1; 
         
        OPEN curseur1; # ouverture du curseur1
        
        DELETE FROM sessions_autorisees; 
        
         loop_curseur: LOOP    
         
 			FETCH curseur1 INTO idSession, idFormation,formation,niveau,description,date,nbPlaces,nbInscrits,ferme;  
 
    		IF fin = 1 THEN 
        		LEAVE loop_curseur; # sort
        	END IF;
	        
        	INSERT INTO sessions_autorisees(libelle,niveau,description,date,nbPlaces,nbInscrits,ferme,session_id,formation_id,client_id)
			VALUES(formation,niveau,description,date,nbPlaces,nbInscrits,ferme,idSession, idFormation, idClient);
        END LOOP;
        
        CLOSE curseur1; # fermeture du curseur1
END$$

DROP PROCEDURE IF EXISTS `sessions_autorisees`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sessions_autorisees` (IN `matricule` INT(11))  BEGIN 
	DELETE FROM formation_autorisee;

	insert into formation_autorisee
    (select s.id, f.libelle, f.niveau, type_form, date_debut, duree, nb_places, nb_inscrits
        from session_formation s, client c, plan_formation p, formation f 
        where c.id = matricule
        and p.client_id = c.id 
        and nb_places > nb_inscrits 
        and p.formation_id = f.id
        and s.formation_id = f.id 
      	and date_debut > current_date()
        and close = 0 
        and effectue = 0
        and s.id Not In (Select session_formation_id
                                        From inscription
                                        Where id = matricule)
     );

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `statut_id` int(11) NOT NULL,
  `nom` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `prenom` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `adresse` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `cp` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `ville` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `telephone` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `nbhcpta` smallint(6) NOT NULL,
  `nbhbur` smallint(6) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C7440455F6203804` (`statut_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id`, `statut_id`, `nom`, `prenom`, `password`, `adresse`, `cp`, `ville`, `email`, `telephone`, `nbhcpta`, `nbhbur`, `admin`) VALUES
(1, 1, 'DUPONT', 'Alain', 'dupal', '3 rue de la gare', '22 200', 'Guingamp', 'ledu.maelle98@gmail.com', '', 70, 175, 0),
(2, 2, 'LAMBERT', 'Alain', 'lamal', '17 rue de la ville', '22 200', 'Guingamp', 'maelle.lolitadu22@gmail.com', '', 0, 105, 0),
(3, 3, 'SARGES', 'Annie', 'saran', '125 boulevard de Nantes', '35 000', 'Rennes', 'sarges.annie@laposte.net', '', 160, 70, 0),
(4, 4, 'CHARLES', 'Patrick', 'chapa', '27 Bd Lamartine', '22 000', 'Saint Brieuc', 'charles.patrick@hotmail.fr', '', 120, 105, 0),
(5, 5, 'DUMAS', 'Serge', 'dumse', 'Pors Braz', '22 200', 'Plouisy', 'dumas.serge@hotmail.com', '', 160, 175, 0),
(6, 1, 'SYLVESTRE', 'Marc', 'sylma', '17 rue des ursulines', '22 300', 'Lannion', 'sylvestre_marc3@gmail.com', '', 0, 70, 0),
(7, 2, 'LEROUX', 'Lena', 'leroux', '', '', '', 'leroux@gmail.com', '', 20, 20, 1);

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

DROP TABLE IF EXISTS `formation`;
CREATE TABLE IF NOT EXISTS `formation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `niveau` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `type_form` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `diplomante` tinyint(1) NOT NULL,
  `duree` int(11) NOT NULL,
  `coutrevient` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `formation`
--

INSERT INTO `formation` (`id`, `libelle`, `niveau`, `type_form`, `description`, `diplomante`, `duree`, `coutrevient`) VALUES
(1, 'Access', 'Initiation', 'Bureautique', 'Decouverte du logiciel Access', 0, 35, 140),
(2, 'Access', 'Perfectionnement', 'Bureautique', 'Fonctions avancees du logiciel Access', 0, 35, 100),
(3, 'Compta1', 'Initiation compta', 'Compta', 'Découverte des principes d ecriture comptable', 1, 70, 120),
(4, 'Compta2', 'Perfectionnement', 'Bureautique', 'Bilan et compte de résultat', 0, 70, 180),
(5, 'Compta3', 'Perfectionnement', 'Compta', 'Analyse du bilan', 0, 70, 100),
(6, 'Compta4', 'Perfectionnement', 'Bureautique', 'Operations d inventaire', 1, 70, 140),
(7, 'Excel', 'Initiation', 'Bureautique', 'Decouverte du logiciel Excel', 0, 35, 100),
(8, 'Excel', 'Perfectionnement', 'Bureautique', 'Fonctions avancees du logiciel Excel', 0, 35, 110),
(9, 'Word', 'Initiation', 'Bureautique', 'Decouverte du logiciel Word', 0, 35, 100),
(10, 'Word', 'Perfectionnement', 'Bureautique', 'Fonctions avancees du logiciel Word', 0, 35, 110);

-- --------------------------------------------------------

--
-- Structure de la table `formation_autorisee`
--

DROP TABLE IF EXISTS `formation_autorisee`;
CREATE TABLE IF NOT EXISTS `formation_autorisee` (
  `session_id` int(11) NOT NULL,
  `formation_libelle` varchar(50) NOT NULL,
  `formation_niveau` varchar(40) NOT NULL,
  `type_formation` varchar(50) NOT NULL,
  `date_debut` date NOT NULL,
  `duree` int(11) NOT NULL,
  `nb_places` smallint(6) NOT NULL,
  `nb_inscrits` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

DROP TABLE IF EXISTS `inscription`;
CREATE TABLE IF NOT EXISTS `inscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `session_formation_id` int(11) NOT NULL,
  `date_inscription` date NOT NULL,
  `presence` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5E90F6D619EB6921` (`client_id`),
  KEY `IDX_5E90F6D69C9D95AF` (`session_formation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `inscription`
--

INSERT INTO `inscription` (`id`, `client_id`, `session_formation_id`, `date_inscription`, `presence`) VALUES
(16, 1, 4, '2019-02-02', 1),
(17, 2, 1, '2012-03-10', 0),
(18, 2, 5, '2019-03-10', 1),
(20, 1, 5, '2019-04-24', 0);

-- --------------------------------------------------------

--
-- Structure de la table `plan_formation`
--

DROP TABLE IF EXISTS `plan_formation`;
CREATE TABLE IF NOT EXISTS `plan_formation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `effectue` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F09EDCAA19EB6921` (`client_id`),
  KEY `IDX_F09EDCAA5200282E` (`formation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `plan_formation`
--

INSERT INTO `plan_formation` (`id`, `client_id`, `formation_id`, `effectue`) VALUES
(1, 2, 1, 1),
(2, 1, 10, 1),
(3, 2, 1, 1),
(4, 3, 1, 1),
(5, 1, 9, 1),
(6, 2, 10, 1);

-- --------------------------------------------------------

--
-- Structure de la table `sessions_autorisees`
--

DROP TABLE IF EXISTS `sessions_autorisees`;
CREATE TABLE IF NOT EXISTS `sessions_autorisees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `niveau` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nbPlaces` int(11) NOT NULL,
  `nbInscrits` int(11) NOT NULL,
  `ferme` tinyint(1) NOT NULL,
  `session_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_848E5A99613FECDF` (`session_id`),
  KEY `IDX_848E5A995200282E` (`formation_id`),
  KEY `IDX_848E5A9919EB6921` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `session_formation`
--

DROP TABLE IF EXISTS `session_formation`;
CREATE TABLE IF NOT EXISTS `session_formation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formation_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `nb_places` smallint(6) NOT NULL,
  `nb_inscrits` smallint(6) NOT NULL,
  `close` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3A264B55200282E` (`formation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `session_formation`
--

INSERT INTO `session_formation` (`id`, `formation_id`, `date_debut`, `nb_places`, `nb_inscrits`, `close`) VALUES
(1, 7, '2019-03-30', 18, 1, 0),
(2, 1, '2019-04-28', 16, 1, 0),
(3, 2, '2019-02-15', 16, 0, 0),
(4, 9, '2019-02-19', 18, 0, 0),
(5, 10, '2019-04-28', 18, 7, 0),
(6, 8, '2019-01-08', 18, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `statut`
--

DROP TABLE IF EXISTS `statut`;
CREATE TABLE IF NOT EXISTS `statut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `taux_horaire` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `statut`
--

INSERT INTO `statut` (`id`, `type`, `taux_horaire`) VALUES
(1, '1%', 14),
(2, 'Autre', 9),
(3, 'CIF', 6),
(4, 'SIFE_collectif', 10),
(5, 'SIFE_individuel', 11);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `FK_C7440455F6203804` FOREIGN KEY (`statut_id`) REFERENCES `statut` (`id`);

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `FK_5E90F6D619EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_5E90F6D69C9D95AF` FOREIGN KEY (`session_formation_id`) REFERENCES `session_formation` (`id`);

--
-- Contraintes pour la table `plan_formation`
--
ALTER TABLE `plan_formation`
  ADD CONSTRAINT `FK_F09EDCAA19EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_F09EDCAA5200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`);

--
-- Contraintes pour la table `sessions_autorisees`
--
ALTER TABLE `sessions_autorisees`
  ADD CONSTRAINT `FK_848E5A9919EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_848E5A995200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`),
  ADD CONSTRAINT `FK_848E5A99613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session_formation` (`id`);

--
-- Contraintes pour la table `session_formation`
--
ALTER TABLE `session_formation`
  ADD CONSTRAINT `FK_3A264B55200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
