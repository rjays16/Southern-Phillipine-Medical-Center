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
/*Table structure for table `seg_eclaims_claim_status` */

CREATE TABLE `seg_eclaims_claim_status` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `claim_id` int(10) DEFAULT NULL,
  `as_of` date DEFAULT NULL,
  `as_of_time` time DEFAULT NULL,
  `status` varchar(60) DEFAULT NULL,
  `claim_date_received` date DEFAULT NULL,
  `claim_date_refile` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claim_id` (`claim_id`),
  CONSTRAINT `claim_id` FOREIGN KEY (`claim_id`) REFERENCES `seg_eclaims_claim` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
