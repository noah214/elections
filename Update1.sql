-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: schema_vote
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `candidate_table`
--

DROP TABLE IF EXISTS `candidate_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidate_table` (
  `candidate_id` int(11) NOT NULL AUTO_INCREMENT,
  `candidate_name` varchar(45) DEFAULT NULL,
  `party_affiliation` varchar(45) DEFAULT NULL,
  `election_position` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`candidate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate_table`
--

LOCK TABLES `candidate_table` WRITE;
/*!40000 ALTER TABLE `candidate_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `candidate_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_table`
--

DROP TABLE IF EXISTS `logs_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs_table` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `DateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_table`
--

LOCK TABLES `logs_table` WRITE;
/*!40000 ALTER TABLE `logs_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position_table`
--

DROP TABLE IF EXISTS `position_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `position_table` (
  `position_id` int(11) NOT NULL,
  `position_name` varchar(45) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_table`
--

LOCK TABLES `position_table` WRITE;
/*!40000 ALTER TABLE `position_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `position_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_table`
--

DROP TABLE IF EXISTS `user_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_table` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(45) DEFAULT NULL,
  `role` varchar(45) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_table`
--

LOCK TABLES `user_table` WRITE;
/*!40000 ALTER TABLE `user_table` DISABLE KEYS */;
INSERT INTO `user_table` VALUES (2,'John Smith2','Admin','johnsmith','password123','john@gmail.com'),(3,'Leonard Panergo','Admin','noah214','password','leonardpanergo@gmail.com'),(4,'Leonard Panergo','Admin','noah214','password','leonardpanergo@gmail.com'),(5,'Leonard Rael Juanico Panergo','voter','noah214','password','leonardrael.panergo.cics@ust.edu.ph'),(6,'Leonard Rael Juanico Panergo','voter','noah214','password','leonardrael.panergo.cics@ust.edu.ph');
/*!40000 ALTER TABLE `user_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vote_count_table`
--

DROP TABLE IF EXISTS `vote_count_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vote_count_table` (
  `vote_count_id` int(11) NOT NULL AUTO_INCREMENT,
  `vote_count` bigint(20) DEFAULT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`vote_count_id`),
  KEY `fk_candidate_id` (`candidate_id`),
  CONSTRAINT `fk_candidate_id` FOREIGN KEY (`candidate_id`) REFERENCES `candidate_table` (`candidate_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vote_count_table`
--

LOCK TABLES `vote_count_table` WRITE;
/*!40000 ALTER TABLE `vote_count_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `vote_count_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vote_table`
--

DROP TABLE IF EXISTS `vote_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vote_table` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `voter_id` int(11) DEFAULT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `vote_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`vote_id`),
  KEY `voter_id` (`voter_id`),
  KEY `candidate_id` (`candidate_id`),
  CONSTRAINT `candidate_id` FOREIGN KEY (`candidate_id`) REFERENCES `candidate_table` (`candidate_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `voter_id` FOREIGN KEY (`voter_id`) REFERENCES `voter_table` (`voter_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vote_table`
--

LOCK TABLES `vote_table` WRITE;
/*!40000 ALTER TABLE `vote_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `vote_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voter_table`
--

DROP TABLE IF EXISTS `voter_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voter_table` (
  `voter_id` int(11) NOT NULL AUTO_INCREMENT,
  `voter_name` varchar(45) DEFAULT NULL,
  `date_of_birth` datetime DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `contact_information` bigint(20) DEFAULT NULL,
  `student_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`voter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voter_table`
--

LOCK TABLES `voter_table` WRITE;
/*!40000 ALTER TABLE `voter_table` DISABLE KEYS */;
INSERT INTO `voter_table` VALUES (1,'Leonard Rael Juanico Panergo','2004-01-05 00:00:00','m',9763885312,2023188725);
/*!40000 ALTER TABLE `voter_table` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-13 22:24:20
