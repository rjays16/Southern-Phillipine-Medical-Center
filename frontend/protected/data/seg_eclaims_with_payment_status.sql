/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.24-log : Database - hisdbeclaims
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `seg_eclaims_claim` */

CREATE TABLE `seg_eclaims_claim` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `encounter_nr` varchar(12) DEFAULT NULL,
  `claim_series_lhio` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_claim_encounter` (`encounter_nr`),
  CONSTRAINT `FK_claim_encounter` FOREIGN KEY (`encounter_nr`) REFERENCES `care_encounter` (`encounter_nr`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

/*Data for the table `seg_eclaims_claim` */

insert  into `seg_eclaims_claim`(`id`,`encounter_nr`,`claim_series_lhio`) values (14,'2012686857','060516030031231'),(15,'2012681239','060516030019903'),(16,'2012686205','060516030019902');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
