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

/*Table structure for table `seg_transmittal` */

DROP TABLE IF EXISTS `seg_transmittal`;

CREATE TABLE `seg_transmittal` (
  `transmit_no` varchar(14) NOT NULL,
  `transmit_dte` datetime NOT NULL,
  `hcare_id` int(8) unsigned NOT NULL,
  `remarks` text NOT NULL,
  `create_id` varchar(35) DEFAULT NULL,
  `create_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_id` varchar(35) DEFAULT NULL,
  `modify_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ticket_no` varchar(18) DEFAULT NULL,
  `no_claim` int(11) NOT NULL DEFAULT '0',
  `control_no` varchar(18) DEFAULT NULL,
  `xml_data` text,
  `is_uploaded` tinyint(4) NOT NULL DEFAULT '0',
  `is_mapped` tinyint(4) NOT NULL DEFAULT '0',
  `xml_is_valid` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`transmit_no`),
  KEY `FK_seg_transmittal_insurance_firm` (`hcare_id`),
  CONSTRAINT `FK_seg_transmittal_insurance_firm` FOREIGN KEY (`hcare_id`) REFERENCES `care_insurance_firm` (`hcare_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
