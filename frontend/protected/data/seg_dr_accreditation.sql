/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.24-log : Database - hiseclaimsdb
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `seg_dr_accreditation` */

CREATE TABLE `seg_dr_accreditation` (
  `dr_nr` int(11) NOT NULL,
  `hcare_id` int(8) NOT NULL,
  `accreditation_nr` varchar(20) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `history` text,
  `modify_id` varchar(300) DEFAULT NULL,
  `modify_dt` datetime DEFAULT NULL,
  `create_id` varchar(300) DEFAULT NULL,
  `create_dt` datetime DEFAULT NULL,
  `accreditation_start` date DEFAULT NULL,
  `accreditation_end` date DEFAULT NULL,
  PRIMARY KEY (`dr_nr`,`hcare_id`),
  CONSTRAINT `FK_seg_dr_accreditation_personell` FOREIGN KEY (`dr_nr`) REFERENCES `care_personell` (`nr`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
