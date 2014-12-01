-- MySQL dump 10.13  Distrib 5.6.20, for osx10.6 (x86_64)
--
-- Host: localhost    Database: todolist_wine
-- ------------------------------------------------------
-- Server version	5.6.20

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
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `user_id` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Sport',1),(2,'Health',1),(46,'Music',1),(49,'Food',1),(50,'Science',1),(51,'Education',1),(52,'Paly',1),(54,'Test666',1),(55,'Test2222',1),(62,'Music',42),(65,'test2',41),(66,'test23',41),(71,'aaa',41),(75,'aaa',52),(76,'bbb',52),(77,'aaa',53),(186,'tttt',42),(231,'abab',42);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `list`
--

DROP TABLE IF EXISTS `list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `list`
--

LOCK TABLES `list` WRITE;
/*!40000 ALTER TABLE `list` DISABLE KEYS */;
INSERT INTO `list` VALUES (1,'Bungee Jump',0,'2014-10-15 00:00:00',1),(2,'Skin Diving',0,'2014-10-15 00:00:00',1),(41,'Sleep 7 hours',0,'2014-10-15 00:00:00',2),(42,'Drink 5000cc water test',0,'2014-10-15 00:00:00',2),(43,'play piano',0,'2014-10-15 00:00:00',46),(54,'play bob',0,'2014-10-28 10:47:20',46),(59,'test',1,'2014-11-10 09:29:50',1),(60,'testaaa',1,'2014-11-12 01:14:48',55),(61,'eee',1,'2014-11-12 01:16:08',55),(62,'aaa',0,'2014-11-12 03:21:34',66),(64,'eee',0,'2014-11-12 06:49:17',71),(67,'bbb',1,'2014-11-12 09:26:42',77),(210,'ooo',0,'2014-11-22 11:47:44',52),(215,'aaa',0,'2014-11-23 10:45:11',186),(216,'aaa',0,'2014-11-26 03:45:26',62),(217,'bbb',0,'2014-11-26 03:47:25',62),(252,'abeeee',0,'2014-11-27 07:39:28',231);
/*!40000 ALTER TABLE `list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `api_key` varchar(32) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'amanda','amanda@gmail.com','$2a$10$73181a566722399d90cfdOt/p1XMqN6dGhym5h.UpKY0.SZSxpT8.',NULL,1,'2014-10-23 14:42:48'),(41,'hank','hank@gmail.com','$2a$10$cc0fc92ccccd67b477bb5uLWLsbw64GXA0/Fgpag.TqAUxgov1pdO',NULL,1,'2014-10-24 01:23:06'),(42,'Hannah','light@gmail.com','$2a$10$30e84a63fbf0dc2ae170auJHCwij4MBarCD0gJSX7po4D10qiqxSK',NULL,1,'2014-10-26 11:44:57');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-11-27 15:40:58
