/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.24-log : Database - hisdbeclaims
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`hisdbeclaims` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `hisdbeclaims`;

/*Table structure for table `seg_transmittal_details` */

DROP TABLE IF EXISTS `seg_transmittal_details`;

CREATE TABLE `seg_transmittal_details` (
  `transmit_no` varchar(14) NOT NULL,
  `encounter_nr` varchar(12) NOT NULL DEFAULT '',
  `patient_claim` double(10,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`transmit_no`,`encounter_nr`),
  UNIQUE KEY `FK_seg_transmittal_details_billing_encounter` (`encounter_nr`),
  CONSTRAINT `FK_seg_transmittal_details_header` FOREIGN KEY (`transmit_no`) REFERENCES `seg_transmittal` (`transmit_no`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `seg_transmittal_details_eclaims_claim` FOREIGN KEY (`encounter_nr`) REFERENCES `seg_eclaims_claim` (`encounter_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
