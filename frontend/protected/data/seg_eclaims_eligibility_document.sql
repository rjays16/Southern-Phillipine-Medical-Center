/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.6.12-log : Database - hiseclaimsdb
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `seg_eclaims_eligibility_document` */

CREATE TABLE `seg_eclaims_eligibility_document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eligibility_id` int(10) unsigned NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `reason` tinytext,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `eligibility_id` (`eligibility_id`),
  CONSTRAINT `seg_eclaims_eligibility_document_ibfk_1` FOREIGN KEY (`eligibility_id`) REFERENCES `seg_eclaims_eligibility` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
