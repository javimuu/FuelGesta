-- MySQL dump 10.14  Distrib 5.5.33a-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: gesta7
-- ------------------------------------------------------
-- Server version	5.5.33a-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activite`
--

DROP TABLE IF EXISTS `activite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activite` (
  `id_activite` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(50) DEFAULT NULL,
  `t_schema` varchar(10) DEFAULT NULL,
  `i_position` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id_activite`),
  KEY `position` (`i_position`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activite`
--

LOCK TABLES `activite` WRITE;
/*!40000 ALTER TABLE `activite` DISABLE KEYS */;
INSERT INTO `activite` VALUES (1,'Maladie','/',7),(2,'Conge','%',5),(3,'Absent','*',4),(4,'Récup','-',3),(5,'Cefa','$',6),(6,'Debriefing_ateliers','+',8),(7,'Gestion_Collective','+',11),(8,'Suivi_individuel','+',10),(9,'Animations_thematiques','@',9),(10,'alpha','@',12),(11,'Convocation','/',2),(12,'Alpha-EXT','%',13),(13,'Travail','+',1),(14,'Stage','=',14),(15,'PM2.V-Isolation','$',15),(16,'PM2.V-Alimentation_durable','§',16);
/*!40000 ALTER TABLE `activite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adresse`
--

DROP TABLE IF EXISTS `adresse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse` (
  `id_adresse` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom_rue` varchar(255) DEFAULT NULL,
  `t_bte` varchar(10) DEFAULT NULL,
  `t_code_postal` varchar(10) DEFAULT NULL,
  `t_commune` varchar(120) DEFAULT NULL,
  `t_telephone` varchar(20) DEFAULT NULL,
  `t_courrier` tinyint(1) NOT NULL DEFAULT '1',
  `participant_id` int(11) DEFAULT NULL,
  `t_type` varchar(255) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_adresse`),
  KEY `fk_adresse_participant` (`participant_id`) USING BTREE,
  KEY `fk_contact` (`contact_id`) USING BTREE,
  CONSTRAINT `adresse_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id_participant`),
  CONSTRAINT `adresse_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id_contact`)
) ENGINE=InnoDB AUTO_INCREMENT=273270 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `agrement`
--

DROP TABLE IF EXISTS `agrement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agrement` (
  `id_agrement` int(11) NOT NULL AUTO_INCREMENT,
  `t_agrement` varchar(45) DEFAULT NULL,
  `t_origine_agrement` varchar(45) DEFAULT NULL,
  `centre_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`id_agrement`),
  KEY `fk_agrement_centre1_idx` (`centre_id`) USING BTREE,
  KEY `fk_agrement_users1_idx` (`users_id`) USING BTREE,
  CONSTRAINT `agrement_ibfk_1` FOREIGN KEY (`centre_id`) REFERENCES `centre` (`id_centre`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `agrement_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `ajout_deplacement`
--

DROP TABLE IF EXISTS `ajout_deplacement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ajout_deplacement` (
  `id_ajout_deplacement` int(11) NOT NULL AUTO_INCREMENT,
  `i_sommes` float(10,2) DEFAULT NULL,
  `t_mois` date DEFAULT NULL,
  `participant_id` int(11) NOT NULL,
  PRIMARY KEY (`id_ajout_deplacement`),
  KEY `fk_ajout_deplacemement` (`participant_id`) USING BTREE,
  CONSTRAINT `ajout_deplacement_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id_participant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Gestion des frais de deplacement suplementaire';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ajout_deplacement`
--

LOCK TABLES `ajout_deplacement` WRITE;
/*!40000 ALTER TABLE `ajout_deplacement` DISABLE KEYS */;
/*!40000 ALTER TABLE `ajout_deplacement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `centre`
--

DROP TABLE IF EXISTS `centre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `centre` (
  `id_centre` int(11) NOT NULL AUTO_INCREMENT,
  `t_responsable` varchar(255) NOT NULL,
  `t_statut` varchar(50) DEFAULT NULL,
  `t_denomination` varchar(255) DEFAULT NULL,
  `t_nom_centre` varchar(255) NOT NULL,
  `t_objet_social` varchar(255) DEFAULT NULL,
  `t_agregation` varchar(255) DEFAULT NULL,
  `t_agence` varchar(255) DEFAULT NULL,
  `t_adresse` varchar(255) DEFAULT NULL,
  `t_code_postal` int(11) DEFAULT NULL,
  `t_localite` varchar(120) DEFAULT NULL,
  `t_telephone` varchar(20) DEFAULT NULL,
  `t_email` varchar(255) DEFAULT NULL,
  `t_tva` varchar(50) DEFAULT NULL,
  `t_enregistrement` varchar(50) DEFAULT NULL,
  `t_responsable_pedagogique` varchar(255) DEFAULT NULL,
  `t_secretaire` varchar(255) DEFAULT NULL,
  `i_position` int(11) NOT NULL,
  PRIMARY KEY (`id_centre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `centre`
--


--
-- Table structure for table `checklist`
--

DROP TABLE IF EXISTS `checklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklist` (
  `id_checklist` int(11) NOT NULL AUTO_INCREMENT,
  `t_liste` text,
  `stagiaire_id` int(11) DEFAULT NULL,
  `participant_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_checklist`),
  KEY `fk_checklist_participant` (`participant_id`) USING BTREE,
  CONSTRAINT `checklist_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id_participant`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `checklist_section`
--

DROP TABLE IF EXISTS `checklist_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklist_section` (
  `id_checklist_section` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id_checklist_section`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `checklist_valeur`
--

DROP TABLE IF EXISTS `checklist_valeur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklist_valeur` (
  `id_checklist_valeur` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(255) NOT NULL,
  `section_id` int(11) NOT NULL,
  PRIMARY KEY (`id_checklist_valeur`),
  KEY `fk_section` (`section_id`) USING BTREE,
  CONSTRAINT `checklist_valeur_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `checklist_section` (`id_checklist_section`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `id_contact` int(11) NOT NULL AUTO_INCREMENT,
  `t_civilite` varchar(15) DEFAULT NULL,
  `t_nom` varchar(50) DEFAULT NULL,
  `t_prenom` varchar(50) DEFAULT NULL,
  `participant_id` int(11) NOT NULL,
  `stage_id` int(11) DEFAULT NULL,
  `t_type` varchar(255) DEFAULT NULL,
  `t_cb_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_contact`),
  KEY `fk_contact_participant1` (`participant_id`) USING BTREE,
  KEY `fk_contact_stage1` (`stage_id`) USING BTREE,
  CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id_participant`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `contact_ibfk_2` FOREIGN KEY (`stage_id`) REFERENCES `stage` (`id_stage`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `contrat`
--

DROP TABLE IF EXISTS `contrat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contrat` (
  `id_contrat` int(11) NOT NULL AUTO_INCREMENT,
  `i_temps_travail` int(11) DEFAULT NULL,
  `d_date_debut_contrat` date NOT NULL,
  `d_date_fin_contrat` date DEFAULT NULL,
  `d_date_fin_contrat_prevu` date DEFAULT NULL,
  `t_remarque` text,
  `f_frais_deplacement` float DEFAULT '0',
  `t_duree_innoccupation` varchar(255) DEFAULT NULL,
  `b_derogation_rw` char(1) DEFAULT NULL,
  `t_abonnement` varchar(25) DEFAULT NULL,
  `f_tarif_horaire` float DEFAULT '1',
  `t_situation_sociale` varchar(255) DEFAULT NULL,
  `d_avertissement1` date DEFAULT NULL,
  `d_avertissement2` date DEFAULT NULL,
  `d_avertissement3` date DEFAULT NULL,
  `t_motif_avertissement1` varchar(45) DEFAULT NULL,
  `t_motif_avertissement2` varchar(45) DEFAULT NULL,
  `t_motif_avertissement3` varchar(45) DEFAULT NULL,
  `d_date_demande_derogation_rw` date DEFAULT NULL,
  `t_connaissance_eft` varchar(45) DEFAULT NULL,
  `t_ressource` varchar(45) DEFAULT NULL,
  `t_passe_professionnel` varchar(45) DEFAULT NULL,
  `d_date_reponse_onem` date DEFAULT NULL,
  `d_date_demande_onem` date DEFAULT NULL,
  `b_dispense_onem` tinyint(4) DEFAULT NULL,
  `b_reponse_rw` tinyint(4) DEFAULT NULL,
  `d_date_demande_forem` date DEFAULT NULL,
  `b_reponse_forem` tinyint(4) DEFAULT NULL,
  `t_moyen_transport` varchar(50) NOT NULL DEFAULT '',
  `b_necessaire` tinyint(4) DEFAULT NULL,
  `groupe_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `type_contrat_id` int(11) NOT NULL,
  PRIMARY KEY (`id_contrat`),
  KEY `fk_contrat_type_contrat` (`type_contrat_id`) USING BTREE,
  KEY `fk_contrat_participant1` (`participant_id`) USING BTREE,
  KEY `fk_contrat_groupe1` (`groupe_id`) USING BTREE,
  CONSTRAINT `contrat_ibfk_2` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id_participant`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `contrat_ibfk_3` FOREIGN KEY (`type_contrat_id`) REFERENCES `type_contrat` (`id_type_contrat`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `contrat_ibfk_4` FOREIGN KEY (`groupe_id`) REFERENCES `groupe` (`id_groupe`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=120419 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `enseignement`
--

DROP TABLE IF EXISTS `enseignement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enseignement` (
  `id_enseignement` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(255) NOT NULL,
  `t_valeur` varchar(10) NOT NULL,
  `i_position` int(11) NOT NULL,
  `type_enseignement_id` int(11) NOT NULL,
  PRIMARY KEY (`id_enseignement`),
  KEY `fk_type_enseignement` (`type_enseignement_id`) USING BTREE,
  CONSTRAINT `enseignement_ibfk_1` FOREIGN KEY (`type_enseignement_id`) REFERENCES `type_enseignement` (`id_type_enseignement`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enseignement`
--

LOCK TABLES `enseignement` WRITE;
/*!40000 ALTER TABLE `enseignement` DISABLE KEYS */;
INSERT INTO `enseignement` VALUES (1,'Enseignement spécial','S',1,1),(2,'Enseignement ordinaire','O',2,1),(3,'Sans diplôme','98',1,2),(4,'Certificat d\'études de base (primaire)','00',2,2),(8,'Enseignement secondaire complémentaire','30',6,2),(9,'Brevet de l\'enseignement post-secondaire non supérieur (1, 2, 3 ans)','31',7,2),(10,'Professionnel avec accès 7ème CESS (accès à l\'enseignement supérieur)','32',8,2),(11,'7ème de perfectionnement ou de spécialisation','33',9,2),(12,'Enseignement supérieur non universitaire de type court','40',10,2),(13,'Enseignement supérieur non universitaire de type long','50',11,2),(14,'Enseignement universitaire','60',12,2),(19,'Enseignement secondaire deuxième degré (CESI ou CQ4)','10',10,2),(22,'Professionnel 2ème degré ou CESI','13',13,2),(23,'Enseignement spécial de forme 4 - 2ème degré','14',14,2),(24,'Promotion sociale si diplômé','15',15,2),(25,'Enseignement secondaire troisième degré (CESS ou CQ6)','20',20,2),(26,'Général et technique ou artistique de transition - CESS','21',21,2),(27,'Technique ou artistique de qualification - CESS','22',22,2),(28,'Professionnel - CESS','23',23,2),(29,'Enseignement spécial de forme 4 - 3ème degré','24',24,2),(37,'Diplôme non reconnu','70',70,2),(38,'Diplôme inconnu','80',80,2);
/*!40000 ALTER TABLE `enseignement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fiche_defraiement`
--

DROP TABLE IF EXISTS `fiche_defraiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fiche_defraiement` (
  `id_fiche_defraiement` int(11) NOT NULL AUTO_INCREMENT,
  `d_date_impression` date DEFAULT NULL,
  `t_paf_repas` varchar(45) DEFAULT NULL,
  `f_ajout_deplacement` float DEFAULT NULL,
  `t_motif_ajout_deplacememnt` varchar(45) DEFAULT NULL,
  `d_date_c98` date DEFAULT NULL,
  `t_mention_c98` varchar(45) DEFAULT NULL,
  `login_id` int(11) NOT NULL,
  `contrat_id` int(11) NOT NULL,
  PRIMARY KEY (`id_fiche_defraiement`),
  KEY `fk_fiche_defraiement_login1` (`login_id`) USING BTREE,
  KEY `fk_fiche_defraiement_contrat1` (`contrat_id`) USING BTREE,
  CONSTRAINT `fiche_defraiement_ibfk_1` FOREIGN KEY (`contrat_id`) REFERENCES `contrat` (`id_contrat`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fiche_defraiement_ibfk_2` FOREIGN KEY (`login_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fiche_defraiement`
--

LOCK TABLES `fiche_defraiement` WRITE;
/*!40000 ALTER TABLE `fiche_defraiement` DISABLE KEYS */;
/*!40000 ALTER TABLE `fiche_defraiement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filiere`
--

DROP TABLE IF EXISTS `filiere`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filiere` (
  `id_filiere` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(45) DEFAULT NULL,
  `t_code_forem` varchar(45) DEFAULT NULL,
  `i_code_cedefop` varchar(45) DEFAULT NULL,
  `agrement_id` int(11) NOT NULL,
  PRIMARY KEY (`id_filiere`),
  KEY `fk_filiere_agrement1_idx` (`agrement_id`) USING BTREE,
  CONSTRAINT `filiere_ibfk_1` FOREIGN KEY (`agrement_id`) REFERENCES `agrement` (`id_agrement`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `fin_formation`
--

DROP TABLE IF EXISTS `fin_formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fin_formation` (
  `id_fin_formation` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(255) NOT NULL,
  `t_valeur` varchar(10) NOT NULL,
  `i_position` int(11) NOT NULL,
  `type_formation_id` int(11) NOT NULL,
  PRIMARY KEY (`id_fin_formation`),
  KEY `fk_type_formation` (`type_formation_id`) USING BTREE,
  CONSTRAINT `fin_formation_ibfk_1` FOREIGN KEY (`type_formation_id`) REFERENCES `type_formation` (`id_type_formation`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fin_formation`
--

LOCK TABLES `fin_formation` WRITE;
/*!40000 ALTER TABLE `fin_formation` DISABLE KEYS */;
INSERT INTO `fin_formation` VALUES (1,'A10 - Mise à l\\\'emploi avant la fin de la formation','A10',1,1),(2,'A11 - Emploi indépendant','A11',2,1),(3,'A12 - Emploi salarié','A12',3,1),(4,'Emploi non subsidié','A1201',4,1),(5,'Contrat de travail CDD temps plein','A120101',5,1),(6,'Contrat de travail CDD temps partiel','A120102',6,1),(7,'Contrat de travail CDI temps plein','A120103',7,1),(8,'Contrat de travail CDI temps partiel','A120104',8,1),(9,'Intérim','A120105',9,1),(10,'Contrat de travail conditions non connues','A120106',10,1),(11,'Emploi subsidié','A1202',11,1),(12,'Programme de résorption de chômage','A120201',12,1),(13,'Programme d\'activation','A120202',13,1),(14,'Article 60 l\\\'essais','A120203',14,1),(15,'A20 - Arrêt de la formation pour raison personnelle','A20',15,5),(16,'A30 - Arrêt de la formation pour cas de force majeure motifs impérieux obligeant à interrompre la formation','A30',16,5),(17,'A40 - Abandon','A40',17,5),(18,'A50 - Autre (à préciser, notamment exclusion)','A50',18,5),(19,'B10 - Emploi après la formation','B10',1,2),(20,'B11 - Emploi indépendant','B11',2,2),(21,'B12 - Emploi salarié','B12',3,2),(22,'Emploi non subsidié','B1201',4,2),(23,'Contrat de travail CDD temps plein','B120101',5,2),(24,'Contrat de travail CDD temps partiel','B120102',6,2),(25,'Contrat de travail CDI temps plein','B120103',7,2),(26,'Contrat de travail CDI temps partiel','B120104',8,2),(27,'Intérim','B120105',9,2),(28,'Contrat de travail conditions non connues','B120106',10,2),(29,'Emploi subsidié','B1202',11,2),(30,'Programme de résorption de chômage','B120201',12,2),(31,'Programme d\'activation','B120202',13,2),(32,'Article 60','B120203',14,2),(33,'B20 - Poursuite d\\\'une formation','B20',15,6),(34,'B21 - Poursuite d\\\'une formation dans le cadre du dispositif du parcours d\\\'insertion','B21',16,6),(35,'B22 - Poursuite d\\\'une formation hors cadre parcours d\\\'insertion (enseignement)','B22',17,6),(36,'B30 - Aide à la recherche d\\\'emploi après la formation','B30',18,7),(37,'B31 - Recherche d\\\'emploi dans un autre organisme','B31',19,7),(38,'B32 - Orientation SPE (Forem ou Actiris)','B32',20,7),(39,'B40 - Réorientation vers un autre type d\\\'action (aide thérapeutique, aide sociale spécialisée, etc.)','B40',21,7),(40,'B50 - Fin de formation sans suite connue','B50',22,7),(41,'WALLONIE','B2101',1,3),(42,'Conditions non connues','B210101',2,3),(43,'FOREM','B210102',3,3),(44,'Promotion Sociale','B210103',4,3),(45,'IFPME','B210104',5,3),(46,'CEFA','B210105',6,3),(47,'OISP','B210106',7,3),(48,'EFT','B210107',8,3),(49,'AWIPH','B210108',9,3),(50,'PFI','B210109',10,3),(51,'Autre (à préciser)','B210110',11,3),(52,'BRUXELLES','B2102',1,4),(53,'Conditions non connues','B210201',2,4),(54,'Bruxelles-Formation','B210202',3,4),(55,'Promotion Sociale','B210203',4,4),(56,'IFPME','B210204',5,4),(57,'CEFA','B210205',6,4),(58,'OISP','B210206',7,4),(59,'EFT','B210207',8,4),(60,'SBFPH','B210208',9,4),(61,'Autre (à préciser)','B210209',10,4);
/*!40000 ALTER TABLE `fin_formation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formation`
--

DROP TABLE IF EXISTS `formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formation` (
  `id_formation` int(11) NOT NULL AUTO_INCREMENT,
  `d_date_fin_formation` date DEFAULT NULL,
  `t_fin_formation_suite` varchar(45) DEFAULT NULL,
  `t_fin_formation` varchar(255) DEFAULT NULL,
  `t_groupe_formation` varchar(45) DEFAULT NULL,
  `t_motif_sortie_formation` varchar(255) DEFAULT NULL,
  `contrat_id` int(11) NOT NULL,
  PRIMARY KEY (`id_formation`),
  KEY `fk_formation_contrat1` (`contrat_id`) USING BTREE,
  CONSTRAINT `formation_ibfk_1` FOREIGN KEY (`contrat_id`) REFERENCES `contrat` (`id_contrat`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21616 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `groupe`
--

DROP TABLE IF EXISTS `groupe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupe` (
  `id_groupe` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(45) DEFAULT NULL,
  `t_filiere` varchar(45) DEFAULT NULL,
  `login_id` int(11) NOT NULL,
  `i_lundi` int(11) NOT NULL DEFAULT '0',
  `i_mardi` int(11) NOT NULL DEFAULT '0',
  `i_mercredi` int(11) NOT NULL DEFAULT '0',
  `i_jeudi` int(11) NOT NULL DEFAULT '0',
  `i_vendredi` int(11) NOT NULL DEFAULT '0',
  `i_samedi` int(11) NOT NULL DEFAULT '0',
  `i_dimanche` int(11) NOT NULL DEFAULT '0',
  `filiere_id` int(11) NOT NULL,
  `localisation_id` int(11) NOT NULL,
  PRIMARY KEY (`id_groupe`),
  KEY `fk_groupe_login1` (`login_id`) USING BTREE,
  KEY `fk_groupe_filiere1_idx` (`filiere_id`) USING BTREE,
  KEY `fk_groupe_localisation1_idx` (`localisation_id`) USING BTREE,
  CONSTRAINT `groupe_ibfk_1` FOREIGN KEY (`filiere_id`) REFERENCES `filiere` (`id_filiere`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `groupe_ibfk_2` FOREIGN KEY (`localisation_id`) REFERENCES `localisation` (`id_localisation`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `groupe_ibfk_3` FOREIGN KEY (`login_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `heures`
--

DROP TABLE IF EXISTS `heures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `heures` (
  `id_heures` int(11) NOT NULL AUTO_INCREMENT,
  `i_secondes` bigint(100) NOT NULL,
  `d_date` date NOT NULL,
  `t_motif` varchar(50) NOT NULL DEFAULT 'TRAVAIL',
  `t_schema` varchar(10) NOT NULL DEFAULT '+',
  `formateur` int(11) NOT NULL,
  `subside` tinyint(4) NOT NULL DEFAULT '0',
  `contrat_id` int(11) NOT NULL DEFAULT '0',
  `login_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  PRIMARY KEY (`id_heures`),
  KEY `index_heures` (`t_schema`) USING BTREE,
  KEY `fk_heures_contrat1` (`contrat_id`) USING BTREE,
  KEY `fk_heures_login1` (`login_id`) USING BTREE,
  KEY `fk_heures__participant1` (`participant_id`) USING BTREE,
  CONSTRAINT `heures_ibfk_1` FOREIGN KEY (`contrat_id`) REFERENCES `contrat` (`id_contrat`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `heures_ibfk_2` FOREIGN KEY (`login_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `heures_ibfk_3` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id_participant`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=308401 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `heures_fixer`
--

DROP TABLE IF EXISTS `heures_fixer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `heures_fixer` (
  `id_heures_fixer` int(11) NOT NULL AUTO_INCREMENT,
  `d_date` date NOT NULL,
  `i_heures` bigint(100) NOT NULL DEFAULT '0',
  `t_motif` varchar(25) NOT NULL DEFAULT 'regime_travail',
  `participant_id` int(11) NOT NULL,
  PRIMARY KEY (`id_heures_fixer`),
  KEY `fk_heures_fixer_participant1` (`participant_id`) USING BTREE,
  CONSTRAINT `heures_fixer_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id_participant`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=80567 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `heures_prestations`
--

DROP TABLE IF EXISTS `heures_prestations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `heures_prestations` (
  `id_heures_prestations` int(11) NOT NULL AUTO_INCREMENT,
  `annee` varchar(5) NOT NULL,
  `janvier` bigint(100) NOT NULL DEFAULT '0',
  `fevrier` bigint(100) NOT NULL DEFAULT '0',
  `mars` bigint(100) NOT NULL DEFAULT '0',
  `avril` bigint(100) NOT NULL DEFAULT '0',
  `mai` bigint(100) NOT NULL DEFAULT '0',
  `juin` bigint(100) NOT NULL DEFAULT '0',
  `juillet` bigint(100) NOT NULL DEFAULT '0',
  `aout` bigint(100) NOT NULL DEFAULT '0',
  `septembre` bigint(100) NOT NULL DEFAULT '0',
  `octobre` bigint(100) NOT NULL DEFAULT '0',
  `novembre` bigint(100) NOT NULL DEFAULT '0',
  `decembre` bigint(100) NOT NULL DEFAULT '0',
  `jours_janvier` int(11) DEFAULT NULL,
  `jours_fevrier` int(11) DEFAULT NULL,
  `jours_mars` int(11) DEFAULT NULL,
  `jours_avril` int(11) DEFAULT NULL,
  `jours_mai` int(11) DEFAULT NULL,
  `jours_juin` int(11) DEFAULT NULL,
  `jours_juillet` int(11) DEFAULT NULL,
  `jours_aout` int(11) DEFAULT NULL,
  `jours_septembre` int(11) DEFAULT NULL,
  `jours_octobre` int(11) DEFAULT NULL,
  `jours_novembre` int(11) DEFAULT NULL,
  `jours_decembre` int(11) DEFAULT NULL,
  `groupe_id` int(11) NOT NULL,
  PRIMARY KEY (`id_heures_prestations`),
  KEY `fk_heures_prestations_groupe1` (`groupe_id`) USING BTREE,
  CONSTRAINT `heures_prestations_ibfk_1` FOREIGN KEY (`groupe_id`) REFERENCES `groupe` (`id_groupe`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3267 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `liste_attente`
--

DROP TABLE IF EXISTS `liste_attente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liste_attente` (
  `id_liste_attente` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(50) NOT NULL,
  `t_prenom` varchar(50) NOT NULL,
  `d_date_naissance` date NOT NULL,
  `d_date_entretien` date NOT NULL,
  `t_contact` varchar(255) DEFAULT NULL,
  `adresse_id` int(11) DEFAULT NULL,
  `groupe_id` int(11) DEFAULT NULL,
  `b_is_actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_liste_attente`),
  KEY `fk_adresse` (`adresse_id`) USING BTREE,
  KEY `fk_groupe` (`groupe_id`) USING BTREE,
  CONSTRAINT `liste_attente_ibfk_1` FOREIGN KEY (`adresse_id`) REFERENCES `adresse` (`id_adresse`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `liste_attente_ibfk_2` FOREIGN KEY (`groupe_id`) REFERENCES `groupe` (`id_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liste_attente`
--

LOCK TABLES `liste_attente` WRITE;
/*!40000 ALTER TABLE `liste_attente` DISABLE KEYS */;
/*!40000 ALTER TABLE `liste_attente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `localisation`
--

DROP TABLE IF EXISTS `localisation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `localisation` (
  `id_localisation` int(11) NOT NULL AUTO_INCREMENT,
  `t_lieu` varchar(45) DEFAULT NULL,
  `adresse_id` int(11) NOT NULL,
  PRIMARY KEY (`id_localisation`),
  KEY `fk_localisation_adresse1` (`adresse_id`) USING BTREE,
  CONSTRAINT `localisation_ibfk_1` FOREIGN KEY (`adresse_id`) REFERENCES `adresse` (`id_adresse`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `type` varchar(25) NOT NULL,
  `name` varchar(50) NOT NULL,
  `migration` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `participant`
--

DROP TABLE IF EXISTS `participant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participant` (
  `id_participant` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(50) NOT NULL,
  `t_prenom` varchar(50) NOT NULL,
  `t_nationalite` varchar(50) DEFAULT NULL,
  `t_lieu_naissance` varchar(255) DEFAULT NULL,
  `d_date_naissance` date DEFAULT NULL,
  `t_sexe` char(1) DEFAULT NULL,
  `t_type_etude` varchar(255) DEFAULT NULL,
  `t_diplome` varchar(255) DEFAULT NULL,
  `d_fin_etude` date DEFAULT NULL,
  `t_annee_etude` varchar(255) DEFAULT NULL,
  `t_etat_civil` varchar(11) DEFAULT NULL,
  `t_registre_national` char(15) DEFAULT NULL,
  `t_compte_bancaire` char(14) DEFAULT NULL,
  `t_pointure` varchar(10) DEFAULT NULL,
  `t_taille` varchar(10) DEFAULT NULL,
  `t_enfants_charge` char(3) DEFAULT NULL,
  `t_mutuelle` varchar(50) DEFAULT NULL,
  `t_organisme_paiement` varchar(255) DEFAULT NULL,
  `t_permis` varchar(10) DEFAULT NULL,
  `i_frais_stagiaire` float DEFAULT NULL,
  `d_date_inscription_onem` date DEFAULT NULL,
  `d_date_fin_stage_onem` date DEFAULT NULL,
  `t_numero_inscription_onem` varchar(45) DEFAULT NULL,
  `d_date_expiration_carte_sejour` date DEFAULT NULL,
  `d_date_examen_medical` date DEFAULT NULL,
  `t_lieu_examen_medical` varchar(45) DEFAULT NULL,
  `i_identification_bob` int(11) DEFAULT NULL,
  `t_gsm` varchar(20) DEFAULT NULL,
  `t_numero_inscription_forem` varchar(45) DEFAULT NULL,
  `d_date_inscription_forem` date DEFAULT NULL,
  `b_attestation_reussite` tinyint(4) DEFAULT NULL,
  `t_gsm2` varchar(20) DEFAULT NULL,
  `t_organisme_paiement_phone` varchar(20) DEFAULT NULL,
  `d_date_permis_theorique` date DEFAULT NULL,
  `t_email` varchar(255) DEFAULT NULL,
  `t_children` text,
  `b_is_actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_participant`)
) ENGINE=InnoDB AUTO_INCREMENT=583 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `photogramme`
--

DROP TABLE IF EXISTS `photogramme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photogramme` (
  `id_photogramme` int(11) NOT NULL AUTO_INCREMENT,
  `phoage` varchar(4) DEFAULT NULL,
  `phocreationdate` date DEFAULT NULL,
  `item1` text,
  `item2` text,
  `item3` text,
  `item4` text,
  `item5` text,
  `item6` text,
  `rect1` text,
  `rect2` text,
  `rect3` text,
  `rect4` text,
  PRIMARY KEY (`id_photogramme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photogramme`
--

LOCK TABLES `photogramme` WRITE;
/*!40000 ALTER TABLE `photogramme` DISABLE KEYS */;
/*!40000 ALTER TABLE `photogramme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rows`
--

DROP TABLE IF EXISTS `rows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rows` (
  `t_nom` varchar(255) DEFAULT NULL,
  `t_prenom` varchar(255) DEFAULT NULL,
  `compteur_formation` varchar(255) DEFAULT NULL,
  `time_partenaire_formation` varchar(255) DEFAULT NULL,
  `time_total_formation` varchar(255) DEFAULT NULL,
  `t_registre_national` varchar(255) DEFAULT NULL,
  `deplacement` varchar(255) DEFAULT NULL,
  `time_partenaire_stage` varchar(255) DEFAULT NULL,
  `time_total_stage` varchar(255) DEFAULT NULL,
  `compteur_stage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rows`
--

LOCK TABLES `rows` WRITE;
/*!40000 ALTER TABLE `rows` DISABLE KEYS */;
/*!40000 ALTER TABLE `rows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `societe`
--

DROP TABLE IF EXISTS `societe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `societe` (
  `id_societe` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(45) NOT NULL,
  `t_succursale` varchar(45) DEFAULT NULL,
  `t_tva` varchar(45) DEFAULT NULL,
  `d_date_convention` date DEFAULT NULL,
  `adresse_id` int(11) NOT NULL,
  PRIMARY KEY (`id_societe`),
  KEY `fk_societe_adresse1` (`adresse_id`) USING BTREE,
  CONSTRAINT `societe_ibfk_1` FOREIGN KEY (`adresse_id`) REFERENCES `adresse` (`id_adresse`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `societe`
--

LOCK TABLES `societe` WRITE;
/*!40000 ALTER TABLE `societe` DISABLE KEYS */;
/*!40000 ALTER TABLE `societe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stage`
--

DROP TABLE IF EXISTS `stage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stage` (
  `id_stage` int(11) NOT NULL,
  `t_finalite` longtext,
  `d_date_debut_stage` date DEFAULT NULL,
  `t_duree_stage` varchar(45) DEFAULT NULL,
  `d_date_fin_stage` varchar(45) DEFAULT NULL,
  `t_horaire_stage` varchar(45) DEFAULT NULL,
  `t_type_stage` varchar(45) NOT NULL,
  `d_contrat_stage` date DEFAULT NULL,
  `t_metier_stage` varchar(45) DEFAULT NULL,
  `t_activite_stage` varchar(45) DEFAULT NULL,
  `t_criteres_stage` varchar(45) DEFAULT NULL,
  `lieu_stage_id` int(11) NOT NULL,
  `responsable_interne_id` int(11) NOT NULL,
  `societe_id` int(11) NOT NULL,
  `contrat_id` int(11) NOT NULL,
  PRIMARY KEY (`id_stage`),
  KEY `fk_stage_contrat1` (`contrat_id`) USING BTREE,
  KEY `fk_stage_societe1` (`societe_id`) USING BTREE,
  KEY `fk_stage_adresse1` (`lieu_stage_id`) USING BTREE,
  KEY `fk_stage_login1` (`responsable_interne_id`) USING BTREE,
  CONSTRAINT `stage_ibfk_1` FOREIGN KEY (`lieu_stage_id`) REFERENCES `adresse` (`id_adresse`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `stage_ibfk_2` FOREIGN KEY (`contrat_id`) REFERENCES `contrat` (`id_contrat`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `stage_ibfk_3` FOREIGN KEY (`responsable_interne_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `stage_ibfk_4` FOREIGN KEY (`societe_id`) REFERENCES `societe` (`id_societe`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stage`
--

LOCK TABLES `stage` WRITE;
/*!40000 ALTER TABLE `stage` DISABLE KEYS */;
/*!40000 ALTER TABLE `stage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statut_entree`
--

DROP TABLE IF EXISTS `statut_entree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statut_entree` (
  `id_statut_entree` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(255) NOT NULL,
  `t_valeur` varchar(10) NOT NULL,
  `i_position` int(11) NOT NULL,
  `type_statut_id` int(11) NOT NULL,
  PRIMARY KEY (`id_statut_entree`),
  KEY `fk_type_statut` (`type_statut_id`) USING BTREE,
  CONSTRAINT `statut_entree_ibfk_1` FOREIGN KEY (`type_statut_id`) REFERENCES `type_statut` (`id_type_statut`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statut_entree`
--

LOCK TABLES `statut_entree` WRITE;
/*!40000 ALTER TABLE `statut_entree` DISABLE KEYS */;
INSERT INTO `statut_entree` VALUES (1,'Salarié','A10',1,1),(2,'Sous contrat de travail article 60/61','A11',2,1),(3,'Sous contrat de travail PTP','A12',3,1),(4,'Autre contrat de travail','A13',4,1),(5,'Indépendant sans personnel','A20',5,1),(6,'Chef d\'entreprise','A30',6,1),(7,'Autre (à spécifier)','A40',7,1),(8,'Chômeur complet indemnisé','B10',1,2),(9,'Allocation d\'attente','B20',2,2),(10,'Stage d\'attente','B30',3,2),(11,'Demandeur d\'emploi libre(mais inscrites comme demandeurs d\'emploi)','B40',4,2),(12,'Demandeurs d\'emploi à aptitude réduite (reconnaissance ONEM, FOREM, ORBEM)','B50',5,2),(13,'Personnes avec handicap reconnu (affaires sociales, AWIPH, SBFPH, Fonds des accidents du travail)','B60',6,2),(14,'Personnes à charge du CPAS','B70',7,2),(15,'Enseignement secondaire en alternance<','C10',1,3),(16,'Enseignement secondaire de plein exercice','C20',2,3),(17,'Enseignement supérieur de plein exercice','C30',3,3),(18,'Formation IFPME','C40',4,3),(19,'Autre','C50',5,3),(20,'Personne à charge du CPAS','D10',1,4),(21,'Personne avec handicap reconnu (Affaire sociales, AWIPH, SBFPH, Fonds des accidents du travail)','D20',2,4),(22,'Autre','D30',3,4);
/*!40000 ALTER TABLE `statut_entree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subside`
--

DROP TABLE IF EXISTS `subside`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subside` (
  `id_subside` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(45) NOT NULL,
  PRIMARY KEY (`id_subside`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subside`
--

LOCK TABLES `subside` WRITE;
/*!40000 ALTER TABLE `subside` DISABLE KEYS */;
INSERT INTO `subside` VALUES (1,'Région Wallonne '),(2,'Fond Propre'),(3,'Fse'),(4,'Forem'),(5,'PM2V-Isolation'),(6,'PM2V-Alimentation');
/*!40000 ALTER TABLE `subside` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_cedefop`
--

DROP TABLE IF EXISTS `type_cedefop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type_cedefop` (
  `id_cedefop` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(255) NOT NULL,
  `i_code` varchar(11) NOT NULL,
  `i_position` int(11) NOT NULL,
  PRIMARY KEY (`id_cedefop`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_cedefop`
--

LOCK TABLES `type_cedefop` WRITE;
/*!40000 ALTER TABLE `type_cedefop` DISABLE KEYS */;
INSERT INTO `type_cedefop` VALUES (1,'Batiment','582',1),(2,'Horeca','811',2),(5,'Développement personnel','090',3);
/*!40000 ALTER TABLE `type_cedefop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_contrat`
--

DROP TABLE IF EXISTS `type_contrat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type_contrat` (
  `id_type_contrat` int(11) NOT NULL AUTO_INCREMENT,
  `t_type_contrat` varchar(45) NOT NULL DEFAULT 'EFT',
  `b_type_contrat_actif` char(1) NOT NULL DEFAULT '1',
  `i_heures` int(11) NOT NULL,
  `i_paye` tinyint(1) NOT NULL DEFAULT '0',
  `i_position` tinyint(4) DEFAULT NULL,
  `subside_id` int(11) NOT NULL,
  PRIMARY KEY (`id_type_contrat`),
  KEY `fk_type_contrat_subside1_idx` (`subside_id`) USING BTREE,
  CONSTRAINT `type_contrat_ibfk_1` FOREIGN KEY (`subside_id`) REFERENCES `subside` (`id_subside`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_contrat`
--

LOCK TABLES `type_contrat` WRITE;
/*!40000 ALTER TABLE `type_contrat` DISABLE KEYS */;
INSERT INTO `type_contrat` VALUES (1,'EFT','1',131400,1,4,1),(2,'F70Bis','1',131400,0,5,4),(3,'F70Bis-15h','1',54000,0,1,4),(8,'Art.60','1',131400,0,3,1),(9,'EFT-25h','1',90000,1,2,1),(10,'PM2V-Isolation','1',27000,1,6,5),(11,'PM2V-Alimentation','1',14400,1,7,6);
/*!40000 ALTER TABLE `type_contrat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_enseignement`
--

DROP TABLE IF EXISTS `type_enseignement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type_enseignement` (
  `id_type_enseignement` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id_type_enseignement`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_enseignement`
--

LOCK TABLES `type_enseignement` WRITE;
/*!40000 ALTER TABLE `type_enseignement` DISABLE KEYS */;
INSERT INTO `type_enseignement` VALUES (1,'Type d\\\'enseignement'),(2,'Niveaux de diplôme');
/*!40000 ALTER TABLE `type_enseignement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_formation`
--

DROP TABLE IF EXISTS `type_formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type_formation` (
  `id_type_formation` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id_type_formation`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_formation`
--

LOCK TABLES `type_formation` WRITE;
/*!40000 ALTER TABLE `type_formation` DISABLE KEYS */;
INSERT INTO `type_formation` VALUES (1,'A. Mise à l\'emploi avant la fin de la formation prévue'),(2,'B1. Emploi après la formation'),(3,'B2.1. Poursuite d\'une formation en Wallonie'),(4,'B2.2. Poursuite d\'une formation à Bruxelles'),(5,'Axx. Autres arrêts avant la fin de formation prévue'),(6,'B2. Poursuite d\'une formation'),(7,'Bx. Autres fins de formation');
/*!40000 ALTER TABLE `type_formation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_pays`
--

DROP TABLE IF EXISTS `type_pays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type_pays` (
  `id_pays` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(100) DEFAULT NULL,
  `t_valeur` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id_pays`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_pays`
--

LOCK TABLES `type_pays` WRITE;
/*!40000 ALTER TABLE `type_pays` DISABLE KEYS */;
INSERT INTO `type_pays` VALUES (5,'Afrique du Sud','03'),(6,'Afghanistan\n			','03'),(7,'Albanie','03'),(8,'Algérie','03'),(9,'Andorre','03'),(10,'Angola','03'),(11,'Antigua-et-Barbuda','03'),(12,'Arabie saoudite','03'),(13,'Argentine','03'),(14,'Arménie','03'),(15,'Australie','03'),(16,'Azerbaïdjan','03'),(17,'Bahamas','03'),(18,'Bahreïn','03'),(19,'Bangladesh','03'),(20,'Barbade','03'),(21,'Belize','03'),(22,'Bénin','03'),(23,'Bhoutan','03'),(24,'Biélorussie','03'),(25,'Birmanie','03'),(26,'Bolivie','03'),(27,'Bosnie-Herzégovine','03'),(28,'Botswana','03'),(29,'Brésil','03'),(30,'Brunei','03'),(31,'Burkina Faso','03'),(32,'Burundi','03'),(33,'Cambodge','03'),(34,'Cameroun','03'),(35,'Canada','03'),(36,'Cap-Vert','03'),(37,'Centrafrique','03'),(38,'Chili','03'),(39,'Chine','03'),(40,'Colombie','03'),(41,'Comores','03'),(42,'Congo','03'),(43,'Corée du Nord','03'),(44,'Corée du Sud','03'),(45,'Costa Rica','03'),(46,'Côte d\'Ivoire','03'),(47,'Croatie','03'),(48,'Cuba','03'),(49,'Djibouti','03'),(50,'Dominique','03'),(51,'Égypte','03'),(52,'Émirats','03'),(53,'Équateur','03'),(54,'Érythrée','03'),(55,'Allemagne','02'),(56,'Bulgarie','02'),(57,'Chypre','02'),(58,'Danemark','02'),(59,'Espagne','02'),(60,'Estonie','02'),(61,'Finlande','02'),(62,'France','02'),(63,'Grèce','02'),(64,'Hongrie','02'),(65,'Irlande','02'),(66,'Italie','02'),(67,'Lettonie','02'),(68,'Lituanie','02'),(69,'Luxembourg','02'),(70,'Malte','02'),(71,'Pays-Bas','02'),(72,'Pologne','02'),(73,'Portugal','02'),(74,'République tchèque','02'),(75,'Roumanie','02'),(76,'Royaume-Uni','02'),(77,'Slovaquie','02'),(78,'Slovénie','02'),(79,'Suède','02'),(80,'Belgique','01'),(81,'Apatride','04'),(82,'Inconnu','05');
/*!40000 ALTER TABLE `type_pays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_statut`
--

DROP TABLE IF EXISTS `type_statut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type_statut` (
  `id_type_statut` int(11) NOT NULL AUTO_INCREMENT,
  `t_nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id_type_statut`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_statut`
--

LOCK TABLES `type_statut` WRITE;
/*!40000 ALTER TABLE `type_statut` DISABLE KEYS */;
INSERT INTO `type_statut` VALUES (1,'A. Personne en emploi'),(2,'B. Demandeur emploi inscrit inoccupé'),(3,'C. Etudiant'),(4,'D. Autres inactifs non inscrits comme demandeurs d\\\'emploi');
/*!40000 ALTER TABLE `type_statut` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `last_login` int(11) NOT NULL,
  `login_hash` varchar(255) NOT NULL,
  `profile_fields` text NOT NULL,
  `is_actif` bit(1) NOT NULL DEFAULT b'1',
  `t_nom` varchar(45) DEFAULT NULL,
  `t_prenom` varchar(45) DEFAULT NULL,
  `t_acl` varchar(50) DEFAULT NULL,
  `i_access` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (7,'demo','AgyuNUkgUnQtmJL/i6y9L+E98houZm2NhZsl5c7f5ws=',100,NULL,1372750786,'98fcf0cc5cb71b1fe899d4b89961729afc89f58f','a:0:{}','',NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `valider_heure`
--

DROP TABLE IF EXISTS `valider_heure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `valider_heure` (
  `id_valider_heure` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `t_mois` date DEFAULT NULL,
  `i_secondes` int(11) DEFAULT NULL,
  `participant_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_valider_heure`),
  KEY `fk_valider_heure` (`participant_id`) USING BTREE,
  CONSTRAINT `valider_heure_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id_participant`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3924 DEFAULT CHARSET=utf8 COMMENT='Gestion des heures à valider';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `vs_database_diagrams`
--

DROP TABLE IF EXISTS `vs_database_diagrams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vs_database_diagrams` (
  `name` char(80) DEFAULT NULL,
  `diadata` text,
  `comment` varchar(1022) DEFAULT NULL,
  `preview` text,
  `lockinfo` char(80) DEFAULT NULL,
  `locktime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `version` char(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vs_database_diagrams`
--

LOCK TABLES `vs_database_diagrams` WRITE;
/*!40000 ALTER TABLE `vs_database_diagrams` DISABLE KEYS */;
/*!40000 ALTER TABLE `vs_database_diagrams` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-11-19  7:40:15
