CREATE DATABASE  IF NOT EXISTS `saladeaula` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `saladeaula`;
-- MySQL dump 10.13  Distrib 5.7.12, for Win32 (AMD64)
--
-- Host: 127.0.0.1    Database: saladeaula
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.30-MariaDB

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
-- Table structure for table `arquivos`
--

DROP TABLE IF EXISTS `arquivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arquivos` (
  `idarquivo` int(11) NOT NULL AUTO_INCREMENT,
  `idpasta` int(11) DEFAULT NULL,
  `caminho` varchar(245) NOT NULL,
  `nivel` int(11) DEFAULT NULL,
  `is_folder` tinyint(4) DEFAULT NULL,
  `do_professor` tinyint(1) NOT NULL DEFAULT '0',
  `idtarefa` int(11) NOT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `data_criado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizado` datetime DEFAULT NULL,
  PRIMARY KEY (`idarquivo`),
  KEY `fk_arquivos_tarefas1_idx` (`idtarefa`),
  KEY `fk_arquivos_usuarios1_idx` (`idusuario`),
  CONSTRAINT `fk_arquivos_tarefas1` FOREIGN KEY (`idtarefa`) REFERENCES `tarefas` (`idtarefa`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_arquivos_usuarios1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-08 10:16:10
