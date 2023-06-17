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
/*Table structure for table `seg_eclaims_denied_claim_status` */

CREATE TABLE `seg_eclaims_denied_claim_status` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `status_id` int(10) DEFAULT NULL,
  `reason` tinytext,
  PRIMARY KEY (`id`),
  KEY `stat_id` (`status_id`),
  CONSTRAINT `stat_id` FOREIGN KEY (`status_id`) REFERENCES `seg_eclaims_claim_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
